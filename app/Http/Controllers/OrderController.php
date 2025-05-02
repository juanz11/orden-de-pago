<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Supplier;
use App\Models\OrderPayment;
use App\Models\User;
use App\Mail\NewOrderNotification;
use App\Mail\OrderStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    public function index()
    {
        $orders = auth()->user()->orders()
            ->with(['supplier:id,name', 'items:id,order_id,description,quantity,unit_price'])
            ->latest()
            ->paginate(10);
        return view('orders.index', compact('orders'));
    }

    public function adminIndex()
    {
        $orders = Order::with([
            'user:id,name,email,department',
            'supplier:id,name',
            'items:id,order_id,description,quantity,unit_price'
        ])
        ->latest()
        ->paginate(10);
        
        $departments = User::distinct('department')->pluck('department')->filter();
        
        return view('orders.admin', [
            'orders' => $orders,
            'departments' => $departments
        ]);
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();
        $orders = Order::where('status', 'aprobado')
                      ->whereRaw('100 - (SELECT COALESCE(SUM(percentage), 0) FROM order_payments WHERE order_id = orders.id OR related_order_id = orders.id) > 0')
                      ->with('supplier')
                      ->get()
                      ->map(function($order) {
                          $order->remaining_percentage = 100 - ($order->payments()->sum('percentage') + $order->relatedPayments()->sum('percentage'));
                          return $order;
                      });
        
        return view('orders.create', compact('suppliers', 'orders'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'supplier_id' => 'required_without:other_supplier',
            'other_supplier' => 'required_without:supplier_id',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'payment_type' => 'required|in:full,partial',
            'payment_percentage' => 'required_if:payment_type,partial|nullable|numeric|min:1|max:100',
            'related_order_id' => 'required_if:payment_type,partial|nullable|exists:orders,id',
        ]);

        DB::beginTransaction();
        try {
            $order = new Order();
            $order->user_id = auth()->id();
            $order->supplier_id = $request->supplier_id !== 'otro' ? $request->supplier_id : null;
            $order->other_supplier = $request->supplier_id === 'otro' ? $request->other_supplier : null;
            $order->status = Order::STATUS_PENDING;
            $order->save();

            $total = 0;
            foreach ($request->items as $item) {
                $orderItem = $order->items()->create([
                    'description' => $item['description'],
                    'unit_price' => floatval($item['unit_price']),
                    'quantity' => intval($item['quantity'])
                ]);
                $total += $orderItem->unit_price * $orderItem->quantity;
            }

            $order->total = $total;
            $order->save();

            // Crear el registro de pago
            if ($request->payment_type === 'partial') {
                $payment = new OrderPayment([
                    'order_id' => $order->id,
                    'related_order_id' => $request->related_order_id,
                    'percentage' => $request->payment_percentage,
                    'amount' => $total * ($request->payment_percentage / 100),
                    'status' => Order::STATUS_PENDING
                ]);
            } else {
                // Si es pago total, crear un pago por el 100%
                $payment = new OrderPayment([
                    'order_id' => $order->id,
                    'percentage' => 100,
                    'amount' => $total,
                    'status' => Order::STATUS_PENDING
                ]);
            }
            $payment->save();

            DB::commit();

            // Enviar notificaciones
            try {
                Mail::to($order->user->email)->send(new NewOrderNotification($order));
                $admins = User::whereIn('role', ['admin', 'superadmin'])->get();
                foreach ($admins as $admin) {
                    Mail::to($admin->email)->send(new NewOrderNotification($order));
                }
            } catch (\Exception $e) {
                Log::error('Error al enviar correos: ' . $e->getMessage());
            }

            return redirect()->route('orders.index')->with('success', 'Orden creada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear orden: ' . $e->getMessage());
            return back()->with('error', 'Error al crear la orden. Por favor, inténtalo de nuevo.');
        }
    }

    public function edit(Order $order)
    {
        if (!Gate::allows('update', $order)) {
            abort(403, 'No tienes permiso para editar esta orden.');
        }
        
        $suppliers = Supplier::all();
        return view('orders.edit', compact('order', 'suppliers'));
    }

    public function update(Request $request, Order $order)
    {
        if (!Gate::allows('update', $order)) {
            abort(403, 'No tienes permiso para editar esta orden.');
        }

        if ($order->status !== Order::STATUS_PENDING) {
            return back()->with('error', 'No se puede editar una orden que ya ha sido aprobada o rechazada.');
        }

        $request->validate([
            'supplier_id' => 'nullable|exists:suppliers,id|required_without:other_supplier',
            'other_supplier' => 'nullable|string|required_without:supplier_id',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $order->update([
                'supplier_id' => $request->supplier_id !== 'otro' ? $request->supplier_id : null,
                'other_supplier' => $request->supplier_id === 'otro' ? $request->other_supplier : null,
            ]);

            // Eliminar items existentes
            $order->items()->delete();

            // Crear nuevos items y calcular total
            $total = 0;
            foreach ($request->items as $item) {
                $orderItem = $order->items()->create([
                    'description' => $item['description'],
                    'unit_price' => floatval($item['unit_price']),
                    'quantity' => intval($item['quantity'])
                ]);
                $total += $orderItem->unit_price * $orderItem->quantity;
            }

            // Actualizar el total de la orden
            $order->total = $total;
            $order->save();

            DB::commit();
            return redirect()->route('orders.index')->with('success', 'Orden actualizada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error al actualizar la orden. Por favor, intente nuevamente.');
        }
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:aprobado,rechazado',
            'admin_comments' => 'nullable|string',
            'exchange_rate' => 'required_if:approval_count,2|numeric|min:0'
        ]);

        $existingApproval = $order->approvals()->where('admin_id', auth()->id())->first();
        
        if ($existingApproval) {
            return redirect()->back()->with('error', 'Ya has registrado tu aprobación para esta orden.');
        }

        // Crear nueva aprobación
        $order->approvals()->create([
            'admin_id' => auth()->id(),
            'status' => $request->status,
            'comments' => $request->admin_comments
        ]);

        // Actualizar el conteo después de crear la nueva aprobación
        $approvalCount = $order->fresh()->approval_count;

        // Solo actualizar el estado de la orden si hay 3 aprobaciones
        if ($request->status === 'aprobado' && $approvalCount >= 3) {
            $order->update([
                'status' => 'aprobado',
                'admin_id' => auth()->id(),
                'exchange_rate' => $request->exchange_rate
            ]);

            // Enviar notificación
            Mail::to($order->user->email)->send(new OrderStatusNotification($order));
        } elseif ($request->status === 'rechazado') {
            $order->update([
                'status' => 'rechazado',
                'admin_id' => auth()->id()
            ]);

            // Enviar notificación
            Mail::to($order->user->email)->send(new OrderStatusNotification($order));
        }

        return redirect()->back()->with('success', 'Tu aprobación ha sido registrada. La orden requiere 3 aprobaciones para cambiar de estado.');
    }

    public function updateObservations(Request $request, Order $order)
    {
        try {
            $request->validate([
                'observations' => 'required|string|max:1000'
            ]);

            \Log::info('Actualizando observaciones para orden #' . $order->id, [
                'observations' => $request->observations
            ]);

            $order->update([
                'observations' => $request->observations
            ]);

            return redirect()->back()->with('success', 'Observaciones actualizadas correctamente');
        } catch (\Exception $e) {
            \Log::error('Error al actualizar observaciones: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al actualizar observaciones: ' . $e->getMessage());
        }
    }

    public function downloadPdf(Order $order, Request $request)
    {
        if ($order->status !== 'aprobado') {
            return back()->with('error', 'Solo se pueden descargar órdenes aprobadas.');
        }

        $order->load(['user', 'supplier', 'items']);

        // Validar moneda
        $currency = $request->query('currency', 'bs');
        if ($currency === 'usd' && !$order->exchange_rate) {
            return back()->with('error', 'La orden necesita una tasa de cambio para ser descargada en USD.');
        }

        $pdf = PDF::loadView('orders.pdf', compact('order', 'currency'));
        
        // Configurar tamaño de página personalizado (214 × 277 mm)
        $pdf->setPaper([0, 0, 606.77, 785.2]); // Convertir mm a puntos (1 mm = 2.835 puntos)
        
        return $pdf->download('orden-' . str_pad($order->id, 4, '0', STR_PAD_LEFT) . '.pdf');
    }

    public function downloadPaymentOrder(Order $order, Request $request)
    {
        $currency = $request->query('currency', 'bs');
        
        // Formatear números para Bs con punto como separador de miles
        $formatNumber = function($number) use ($currency, $order) {
            if ($currency === 'usd' && $order->exchange_rate) {
                return '$ ' . number_format($number / $order->exchange_rate, 2, ',', '.');
            }
            return 'Bs. ' . number_format($number, 2, ',', '.');
        };

        $pdf = PDF::loadView('pdf.payment-order', [
            'order' => $order,
            'currency' => $currency,
            'formatNumber' => $formatNumber
        ]);

        $currencyText = $currency === 'usd' ? 'usd' : 'bs';
        return $pdf->download('orden-pago-' . str_pad($order->id, 4, '0', STR_PAD_LEFT) . '-' . $currencyText . '.pdf');
    }
}
