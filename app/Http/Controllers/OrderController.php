<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Supplier;
use App\Models\User;
use App\Mail\NewOrderNotification;
use App\Mail\OrderStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

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
                // Enviar correo al solicitante
                Log::info('Enviando correo al solicitante: ' . $order->user->email);
                Mail::to($order->user->email)
                    ->send(new NewOrderNotification($order));

                // Enviar correo a los administradores
                $admins = User::whereIn('role', ['admin', 'superadmin'])->get();
                foreach ($admins as $admin) {
                    Log::info('Enviando correo al administrador: ' . $admin->email);
                    Mail::to($admin->email)
                        ->send(new NewOrderNotification($order));
                }
            } catch (\Exception $e) {
                Log::error('Error al enviar correos: ' . $e->getMessage());
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

            // Crear nuevos items
            foreach ($request->items as $item) {
                $order->items()->create([
                    'description' => $item['description'],
                    'unit_price' => $item['unit_price'],
                    'quantity' => $item['quantity']
                ]);
            }

            DB::commit();
            return redirect()->route('orders.index')->with('success', 'Orden actualizada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error al actualizar la orden. Por favor, intente nuevamente.');
        }
    }

    public function updateStatus(Request $request, Order $order)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        $request->validate([
            'status' => 'required|in:' . Order::STATUS_APPROVED . ',' . Order::STATUS_DECLINED,
            'admin_comments' => 'nullable|string'
        ]);

        $order->update([
            'status' => $request->status,
            'admin_comments' => $request->admin_comments,
            'admin_id' => auth()->id()
        ]);

        try {
            // Enviar correo al creador de la orden
            Log::info('Enviando correo de actualización de estado al solicitante: ' . $order->user->email);
            Mail::to($order->user->email)
                ->send(new OrderStatusNotification($order));

            // Enviar correo a los administradores
            $admins = User::whereIn('role', ['admin', 'superadmin'])->get();
            foreach ($admins as $admin) {
                Log::info('Enviando correo de actualización de estado al administrador: ' . $admin->email);
                Mail::to($admin->email)
                    ->send(new OrderStatusNotification($order));
            }
        } catch (\Exception $e) {
            Log::error('Error al enviar correos de actualización de estado: ' . $e->getMessage());
        }

        return redirect()->route('orders.admin')
            ->with('success', 'Estado de la orden actualizado exitosamente.');
    }
}
