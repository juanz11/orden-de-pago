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

    @include('components.exchange-rate')

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
        <table class="min-w-full table-auto" style="
    width: 100%;
">
            <thead>
                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <th class="py-3 px-6 text-left">Proveedor</th>
                    <th class="py-3 px-6 text-left">Productos</th>
                    <th class="py-3 px-6 text-left">Total</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Estado
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Pago
                    </th>
                    <th class="py-3 px-6 text-left">Fecha</th>
                    @if(auth()->user()->isAdmin())
                    <th class="py-3 px-6 text-left">Usuario</th>
                    @endif
                    <th class="py-3 px-6 text-left">Acciones</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm font-light">
                @foreach($orders as $order)
                <tr class="border-b border-black-200 hover:bg-gray-100">
                    <td class="py-3 px-6 text-left">
                        @if($order->supplier)
                            {{ $order->supplier->name }}
                        @else
                            {{ $order->other_supplier }}
                        @endif
                    </td>
                    <td class="py-3 px-6 text-left">
                        <ul class="list-disc list-inside">
                            @foreach($order->items as $item)
                            <li>{{ $item->description }} ({{ $item->quantity }} x {{ number_format($item->unit_price, 2) }} Bs.)</li>
                            @endforeach
                        </ul>
                    </td>
                    <td class="py-3 px-6 text-left">{{ number_format($order->total, 2) }} Bs.</td>
                    <td class="py-3 px-6">
                        @if($order->status === 'pendiente')
                            <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded">Pendiente</span>
                        @elseif($order->status === 'aprobado')
                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">Aprobado</span>
                        @else
                            <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded">Rechazado</span>
                        @endif
                    </td>
                    <td class="py-3 px-6">
                        <span class="bg-{{ $order->payment_status['color'] }}-100 text-{{ $order->payment_status['color'] }}-800 text-xs font-medium px-2.5 py-0.5 rounded">
                            {{ $order->payment_status['text'] }}
                        </span>
                    </td>
                    <td class="py-3 px-6 text-left">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    @if(auth()->user()->isAdmin())
                    <td class="py-3 px-6 text-left">{{ $order->user->name }}</td>
                    @endif
                    <td class="py-3 px-6 text-left">
                        @if($order->status === 'pendiente')
                            @if(auth()->user()->isAdmin() || auth()->id() === $order->user_id)
                            <a href="{{ route('orders.edit', $order) }}" class="text-blue-600 hover:text-blue-900 mr-2">
                                Editar
                            </a>
                            @endif
                        @endif

                        @if($order->status === 'aprobado')
                            <a href="{{ route('orders.pdf', $order) }}" class="inline-block bg-gray-600 text-xs px-2 py-1 rounded hover:bg-gray-700">
                                Descargar PDF
                            </a>
                        @endif

                        @if(auth()->user()->isAdmin() && $order->status === 'pendiente')
                        <div class="mt-2 flex flex-col space-y-2">
                            <form action="{{ route('orders.update-status', $order) }}" method="POST" class="flex items-center space-x-2">
                                @csrf
                                <input type="hidden" name="status" value="aprobado">
                                <input type="text" name="admin_comments" placeholder="Comentarios"
                                    class="text-sm rounded-md border-black-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <button type="submit" class="bg-green-600 text-white text-xs px-2 py-1 rounded hover:bg-green-700">
                                    Aprobar
                                </button>
                            </form>
                            <form action="{{ route('orders.update-status', $order) }}" method="POST" class="flex items-center space-x-2">
                                @csrf
                                <input type="hidden" name="status" value="rechazado">
                                <input type="text" name="admin_comments" placeholder="Comentarios"
                                    class="text-sm rounded-md border-black-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <button type="submit" class="bg-red-600 text-white text-xs px-2 py-1 rounded hover:bg-red-700">
                                    Rechazar
                                </button>
                            </form>
                        </div>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $orders->links() }}
    </div>
</div>
@endsection
