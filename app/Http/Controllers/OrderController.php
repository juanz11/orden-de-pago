<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $orders = auth()->user()->orders()->with(['supplier', 'items'])->latest()->get();
        return view('orders.index', compact('orders'));
    }

    public function adminIndex()
    {
        $orders = Order::with(['user', 'supplier', 'items'])->latest()->get();
        return view('orders.admin', compact('orders'));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();
        return view('orders.create', compact('suppliers'));
    }

    public function store(Request $request)
    {
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
            $order = new Order([
                'supplier_id' => $request->supplier_id !== 'otro' ? $request->supplier_id : null,
                'other_supplier' => $request->supplier_id === 'otro' ? $request->other_supplier : null,
                'status' => Order::STATUS_PENDING,
                'total' => 0 // Se calculará automáticamente
            ]);

            $order->user()->associate(auth()->user());
            $order->save();

            foreach ($request->items as $item) {
                $order->items()->create([
                    'description' => $item['description'],
                    'unit_price' => $item['unit_price'],
                    'quantity' => $item['quantity']
                ]);
            }

            // El total se calcula automáticamente por el modelo
            $order->save();

            DB::commit();
            return redirect()->route('orders.index')->with('success', 'Orden creada exitosamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error al crear la orden. Por favor, intente nuevamente.');
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

        $order->update([
            'supplier_id' => $request->supplier_id !== 'otro' ? $request->supplier_id : null,
            'other_supplier' => $request->supplier_id === 'otro' ? $request->other_supplier : null,
        ]);

        $order->items()->delete();

        foreach ($request->items as $item) {
            $order->items()->create([
                'description' => $item['description'],
                'unit_price' => $item['unit_price'],
                'quantity' => $item['quantity']
            ]);
        }

        // El total se calcula automáticamente por el modelo
        $order->save();

        if (auth()->user()->isAdmin()) {
            return redirect()->route('orders.admin')
                ->with('success', 'Orden actualizada exitosamente.');
        }

        return redirect()->route('orders.index')
            ->with('success', 'Orden actualizada exitosamente.');
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
            'admin_comments' => $request->admin_comments
        ]);

        return redirect()->route('orders.admin')
            ->with('success', 'Estado de la orden actualizado exitosamente.');
    }
}
