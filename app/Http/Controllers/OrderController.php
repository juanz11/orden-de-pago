<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\User;
use App\Models\OrderItem;
use App\Models\OrderApproval;
use App\Models\Supplier;
use App\Mail\OrderCreated;
use App\Mail\NewOrderMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\OrderApprovalToken;

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
        return view('orders.create', compact('suppliers'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'supplier_id' => 'nullable|exists:suppliers,id|required_without:other_supplier',
                'other_supplier' => 'nullable|string|required_without:supplier_id',
                'items' => 'required|array|min:1',
                'items.*.description' => 'required|string',
                'items.*.unit_price' => 'required|numeric|min:0',
                'items.*.quantity' => 'required|integer|min:1',
            ]);

            DB::beginTransaction();

            $order = new Order([
                'supplier_id' => $request->supplier_id !== 'otro' ? $request->supplier_id : null,
                'other_supplier' => $request->supplier_id === 'otro' ? $request->other_supplier : null,
                'status' => 'pendiente',
                'total' => 0
            ]);

            $order->user()->associate(auth()->user());
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

            DB::commit();

            try {
                // Enviar correo al solicitante (sin botón de aprobación)
                Log::info('Enviando correo al solicitante: ' . $order->user->email);
                Mail::to($order->user->email)
                    ->send(new NewOrderMail($order));

                // Enviar correos a los administradores con token de aprobación
                $admins = User::whereIn('role', ['admin', 'superadmin'])->get();
                foreach ($admins as $admin) {
                    if ($order->status === 'pendiente') {
                        $token = \Illuminate\Support\Str::random(64);
                        $approval = new OrderApproval([
                            'order_id' => $order->id,
                            'user_id' => $admin->id,
                            'status' => 'pendiente',
                            'token' => $token
                        ]);
                        $approval->save();
                        
                        Log::info('Enviando correo al administrador: ' . $admin->email);
                        Mail::to($admin->email)
                            ->send(new NewOrderMail($order, $token));
                    } else {
                        Log::info('Enviando correo al administrador: ' . $admin->email);
                        Mail::to($admin->email)
                            ->send(new NewOrderMail($order));
                    }
                }
            } catch (\Exception $e) {
                Log::error('Error al enviar correos: ' . $e->getMessage());
                Log::error($e->getTraceAsString());
            }

            return redirect()->route('orders.index')->with('success', 'Orden creada correctamente.');

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
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

        $existingApproval = $order->approvals()->where('user_id', auth()->id())->first();
        
        if ($existingApproval) {
            return redirect()->back()->with('error', 'Ya has registrado tu aprobación para esta orden.');
        }

        // Crear nueva aprobación
        $order->approvals()->create([
            'user_id' => auth()->id(),
            'status' => $request->status,
            'comments' => $request->admin_comments
        ]);

        // Actualizar el conteo después de crear la nueva aprobación
        $approvalCount = $order->fresh()->approval_count;

        // Solo actualizar el estado de la orden si hay 3 aprobaciones
        if ($request->status === 'aprobado' && $approvalCount >= 3) {
            $order->update([
                'status' => 'aprobado',
                'user_id' => auth()->id(),
                'exchange_rate' => $request->exchange_rate
            ]);

            // Enviar notificación
            Mail::to($order->user->email)->send(new OrderCreated($order));
        } elseif ($request->status === 'rechazado') {
            $order->update([
                'status' => 'rechazado',
                'user_id' => auth()->id()
            ]);

            // Enviar notificación
            Mail::to($order->user->email)->send(new OrderCreated($order));
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

    public function downloadPdf($id)
    {
        $order = Order::with(['user', 'supplier', 'items', 'approvals'])
            ->findOrFail($id);

        $pdf = Pdf::loadView('orders.pdf.order-details', compact('order'));
        
        // Configurar el tamaño de página a 214 × 277 mm
        $pdf->setPaper([0, 0, 606.77, 785.2]); // Convertido de mm a puntos (1mm = 2.83465 puntos)
        
        return $pdf->download("orden-de-pago-{$order->id}.pdf");
    }

    public function downloadPaymentOrder(Order $order, Request $request)
    {
        $currency = $request->query('currency', 'bsf');
        
        // Formatear números para Bs con punto como separador de miles
        $formatNumber = function($number) use ($currency, $order) {
            if ($currency === 'usd' && $order->exchange_rate) {
                return '$ ' . number_format($number / $order->exchange_rate, 2, ',', '.');
            }
            return 'Bs. ' . number_format($number, 2, ',', '.');
        };

        $pdf = Pdf::loadView('pdf.payment-order', [
            'order' => $order,
            'currency' => $currency,
            'formatNumber' => $formatNumber
        ]);

        $currencyText = $currency === 'usd' ? 'usd' : 'bs';
        return $pdf->download('orden-pago-' . str_pad($order->id, 4, '0', STR_PAD_LEFT) . '-' . $currencyText . '.pdf');
    }

    public function downloadPaymentReceipt(Order $order, OrderPayment $payment)
    {
        if ($payment->order_id !== $order->id) {
            abort(404);
        }

        $pdf = Pdf::loadView('pdf.payment-receipt', compact('order', 'payment'));
        return $pdf->download('comprobante-pago-' . str_pad($order->id, 4, '0', STR_PAD_LEFT) . '-' . $payment->id . '.pdf');
    }

    public function paymentIndex()
    {
        $orders = Order::with(['supplier', 'payments'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('orders.payments.index', compact('orders'));
    }

    public function paymentCreate()
    {
        $orders = Order::with('supplier')
            ->whereHas('approvals', function($query) {
                $query->where('status', 'aprobado');
            }, '=', 3)
            ->get();

        return view('orders.payments.create', compact('orders'));
    }

    public function paymentStore(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'percentage' => 'required|numeric|min:0.01|max:100',
            'reference_number' => 'required|string|max:255'
        ]);

        $order = Order::findOrFail($request->order_id);

        // Verificar que el porcentaje no exceda el disponible
        $totalPaidPercentage = $order->total_paid_percentage;
        $remainingPercentage = 100 - $totalPaidPercentage;

        if ($request->percentage > $remainingPercentage) {
            return back()->withErrors([
                'percentage' => 'El porcentaje ingresado excede el porcentaje disponible para pago.'
            ])->withInput();
        }

        // Calcular el monto basado en el porcentaje
        $amount = $order->total * ($request->percentage / 100);

        // Crear el registro de pago
        $payment = new OrderPayment([
            'order_id' => $order->id,
            'user_id' => auth()->id(),
            'percentage' => $request->percentage,
            'amount' => $amount,
            'reference_number' => $request->reference_number
        ]);

        $payment->save();

        return redirect()->route('orders.payments.index')
            ->with('success', 'Pago registrado exitosamente.');
    }

    public function storePayment(Request $request)
    {
        try {
            $validated = $request->validate([
                'order_id' => 'required|exists:orders,id',
                'percentage' => 'required|numeric|min:0.01|max:100',
                'payment_type' => 'required|in:efectivo,banco',
                'cash_amount' => 'required_if:payment_type,efectivo|numeric|min:0.01|nullable',
                'bank_name' => 'required_if:payment_type,banco|string|nullable',
                'reference_number' => 'required_if:payment_type,banco|string|nullable',
                'accounting_entry' => 'required|string'
            ]);

            DB::beginTransaction();

            $order = Order::findOrFail($request->order_id);
            
            // Verificar que el porcentaje no exceda el disponible
            $remainingPercentage = $order->remaining_percentage;
            if ($request->percentage > $remainingPercentage) {
                throw ValidationException::withMessages([
                    'percentage' => "El porcentaje no puede exceder el disponible ({$remainingPercentage}%)"
                ]);
            }

            $amount = $order->total * ($request->percentage / 100);

            $payment = new OrderPayment([
                'order_id' => $order->id,
                'user_id' => auth()->id(),
                'percentage' => $request->percentage,
                'amount' => $amount,
                'payment_type' => $request->payment_type,
                'bank_name' => $request->payment_type === 'banco' ? $request->bank_name : null,
                'reference_number' => $request->payment_type === 'banco' ? $request->reference_number : null,
                'cash_amount' => $request->payment_type === 'efectivo' ? $request->cash_amount : null,
                'accounting_entry' => $request->accounting_entry
            ]);

            $order->payments()->save($payment);

            DB::commit();
            return redirect()->route('orders.payments.index')->with('success', 'Pago registrado correctamente');

        } catch (ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al registrar pago: ' . $e->getMessage());
            return back()->with('error', 'Error al registrar el pago. Por favor, inténtalo de nuevo.')->withInput();
        }
    }

    public function getLastAccountingEntry(Order $order)
    {
        $lastPayment = $order->payments()->latest()->first();
        return response()->json([
            'accounting_entry' => $lastPayment ? $lastPayment->accounting_entry : null
        ]);
    }

    protected function createApprovalToken($order, $user)
    {
        return OrderApprovalToken::create([
            'order_id' => $order->id,
            'user_id' => $user->id,
            'token' => \Illuminate\Support\Str::random(64),
            'expires_at' => now()->addDay(),
        ]);
    }

    public function approveByEmail($token)
    {
        try {
            Log::info('Iniciando aprobación por email con token: ' . $token);
            
            DB::beginTransaction();
            
            // Buscar la aprobación y cargar las relaciones
            $approval = OrderApproval::where('token', $token)
                ->with(['order', 'order.approvals'])
                ->first();
            
            if (!$approval) {
                Log::warning('Token no encontrado: ' . $token);
                return view('orders.token-used', [
                    'order' => null,
                    'error' => 'Token de aprobación inválido.',
                    'message' => null
                ]);
            }

            Log::info('Aprobación encontrada:', [
                'approval_id' => $approval->id,
                'order_id' => $approval->order_id,
                'user_id' => $approval->user_id,
                'status' => $approval->status
            ]);

            $order = $approval->order;

            // Si la orden ya no está pendiente
            if ($order->status !== 'pendiente') {
                Log::info('Orden no está pendiente');
                return view('orders.token-used', [
                    'order' => $order,
                    'message' => 'Esta orden ya no está pendiente de aprobación.',
                    'error' => null
                ]);
            }

            // Si la aprobación ya fue usada
            if ($approval->status === 'aprobado') {
                Log::info('Aprobación ya fue usada');
                return view('orders.token-used', [
                    'order' => $order,
                    'message' => 'Ya has aprobado esta orden anteriormente.',
                    'error' => null
                ]);
            }

            try {
                Log::info('Actualizando aprobación...');

                // Actualizar la aprobación
                $approval->fill([
                    'status' => 'aprobado',
                    'approved_at' => now()
                ]);
                
                if (!$approval->save()) {
                    throw new \Exception('No se pudo guardar la aprobación');
                }

                Log::info('Aprobación actualizada correctamente');

                // Contar aprobaciones actuales
                $approvedCount = $order->approvals()
                    ->where('status', 'aprobado')
                    ->count();

                Log::info('Conteo de aprobaciones: ' . $approvedCount);

                // Si tenemos 3 o más aprobaciones, actualizar el estado de la orden
                if ($approvedCount >= 3) {
                    $order->fill(['status' => 'aprobado']);
                    if (!$order->save()) {
                        throw new \Exception('No se pudo actualizar el estado de la orden');
                    }
                    Log::info('Orden marcada como aprobada');
                }

                DB::commit();
                Log::info('Transacción completada exitosamente');

                return view('orders.approval-success', [
                    'order' => $order->fresh(),
                    'approvedCount' => $approvedCount,
                    'error' => null,
                    'message' => null
                ]);

            } catch (\Exception $e) {
                Log::error('Error al actualizar la aprobación: ' . $e->getMessage());
                throw $e;
            }

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error completo en approveByEmail: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return view('orders.token-used', [
                'order' => isset($order) ? $order : null,
                'error' => 'Error al procesar la aprobación. Por favor, inténtalo de nuevo.',
                'message' => null
            ]);
        }
    }

    public function approve(Order $order)
    {
        try {
            DB::beginTransaction();

            $existingApproval = $order->approvals()->where('user_id', auth()->id())->first();

            if ($existingApproval) {
                if ($existingApproval->status === 'aprobado') {
                    return back()->with('info', 'Ya has aprobado esta orden anteriormente.');
                }

                $existingApproval->update([
                    'status' => 'aprobado',
                    'user_id' => auth()->id(),
                    'approved_at' => now()
                ]);
            } else {
                $order->approvals()->create([
                    'status' => 'aprobado',
                    'user_id' => auth()->id(),
                    'approved_at' => now()
                ]);
            }

            // Verificar si la orden está completamente aprobada
            if ($order->isFullyApproved()) {
                $order->update(['status' => 'aprobado']);
            }

            DB::commit();
            return back()->with('success', 'Orden aprobada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error approving order: ' . $e->getMessage());
            return back()->with('error', 'Error al aprobar la orden.');
        }
    }
}
