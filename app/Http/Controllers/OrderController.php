<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = auth()->user()->isAdmin() 
            ? Order::with('user')->latest()->get()
            : auth()->user()->orders()->latest()->get();

        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        return view('orders.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'description' => 'required|string',
            'unit_price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
        ]);

        $order = auth()->user()->orders()->create($validated);

        return redirect()->route('orders.index')
            ->with('success', 'Orden creada exitosamente.');
    }

    public function updateStatus(Order $order, Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        $validated = $request->validate([
            'status' => 'required|in:approved,declined',
            'admin_comments' => 'nullable|string',
        ]);

        $order->update($validated);

        return redirect()->route('orders.index')
            ->with('success', 'Estado de la orden actualizado exitosamente.');
    }
}
