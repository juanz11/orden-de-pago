@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            {{ auth()->user()->isAdmin() ? 'Todas las Órdenes' : 'Mis Órdenes' }}
        </h2>
        @if(!auth()->user()->isAdmin())
        <a href="{{ route('orders.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Nueva Orden
        </a>
        @endif
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
    @endif

    <div class="bg-white shadow-md rounded my-6">
        <table class="min-w-full table-auto " style="
    width: 100%;
">
            <thead>
                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <th class="py-3 px-6 text-left">Descripción</th>
                    <th class="py-3 px-6 text-left">Precio Unitario</th>
                    <th class="py-3 px-6 text-left">Cantidad</th>
                    <th class="py-3 px-6 text-left">Total</th>
                    <th class="py-3 px-6 text-left">Estado</th>
                    <th class="py-3 px-6 text-left">Fecha Creación</th>
                    @if(auth()->user()->isAdmin())
                    <th class="py-3 px-6 text-left">Usuario</th>
                    @endif
                    <th class="py-3 px-6 text-left">Acciones</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm font-light">
                @foreach($orders as $order)
                <tr class="border-b border-gray-200 hover:bg-gray-100">
                    <td class="py-3 px-6 text-left">{{ $order->description }}</td>
                    <td class="py-3 px-6 text-left">${{ number_format($order->unit_price, 2) }}</td>
                    <td class="py-3 px-6 text-left">{{ $order->quantity }}</td>
                    <td class="py-3 px-6 text-left">${{ number_format($order->total_amount, 2) }}</td>
                    <td class="py-3 px-6 text-left">
                        <span class="@if($order->status === 'pending') text-yellow-600 @elseif($order->status === 'approved') text-green-600 @else text-red-600 @endif">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>
                    <td class="py-3 px-6 text-left">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    @if(auth()->user()->isAdmin())
                    <td class="py-3 px-6 text-left">{{ $order->user->name }}</td>
                    @endif
                    <td class="py-3 px-6 text-left">
                        @if($order->status === 'pending')
                            @if(auth()->user()->isAdmin() || auth()->id() === $order->user_id)
                            <a href="{{ route('orders.edit', $order) }}" class="text-blue-600 hover:text-blue-900 mr-2">
                                Editar
                            </a>
                            @endif
                        @endif

                        @if(auth()->user()->isAdmin() && $order->status === 'pending')
                        <div class="mt-4 flex justify-end space-x-3">
                            <form action="{{ route('orders.update-status', $order) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="approved">
                                <div class="flex items-center space-x-2">
                                    <input type="text" name="admin_comments" placeholder="Comentarios (opcional)"
                                        class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <button type="submit" class="bg-green-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                        Aprobar
                                    </button>
                                </div>
                            </form>
                            <form action="{{ route('orders.update-status', $order) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="declined">
                                <div class="flex items-center space-x-2">
                                    <input type="text" name="admin_comments" placeholder="Razón del rechazo (opcional)"
                                        class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <button type="submit" class="bg-red-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                        Rechazar
                                    </button>
                                </div>
                            </form>
                        </div>
                        @endif

                        @if($order->admin_comments)
                        <div class="mt-2 text-sm text-gray-500">
                            <strong>Comentarios:</strong> {{ $order->admin_comments }}
                        </div>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
