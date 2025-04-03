@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h2 class="text-2xl font-bold mb-4">Gestión de Órdenes</h2>

                @include('components.exchange-rate')

                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white" style="
    width: 100%;
">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-6 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                                <th class="px-6 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Departamento</th>
                                <th class="px-6 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Proveedor</th>
                                <th class="px-6 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Productos</th>
                                <th class="px-6 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="px-6 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($orders as $order)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    {{ $order->id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    {{ $order->user->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    {{ $order->user->department }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($order->supplier)
                                        {{ $order->supplier->name }}
                                    @elseif($order->other_supplier)
                                        {{ $order->other_supplier }} <span class="text-gray-500">(Otro)</span>
                                    @else
                                        <span class="text-gray-500">No especificado</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <div class="space-y-2">
                                        @foreach($order->items as $item)
                                        <div>
                                            <div class="font-medium">{{ $item->description }}</div>
                                            <div class="text-gray-500">
                                                Cantidad: {{ $item->quantity }}
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    ${{ number_format($order->total, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($order->status === 'pendiente')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Pendiente
                                        </span>
                                    @elseif($order->status === 'aprobado')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Aprobado
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Rechazado
                                        </span>
                                    @endif
                                    @if($order->admin_comments)
                                        <p class="text-xs text-gray-500 mt-1">{{ $order->admin_comments }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    {{ $order->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($order->status === 'pendiente')
                                        <div class="space-y-2">
                                            <a href="{{ route('orders.edit', $order) }}" class="inline-block bg-blue-600 text-white text-xs px-2 py-1 rounded hover:bg-blue-700 mb-2" style="
    background-color: cadetblue;
">
                                                Editar
                                            </a>
                                            <form action="{{ route('orders.update-status', $order) }}" method="POST" class="flex items-center space-x-2">
                                                @csrf
                                                <input type="hidden" name="status" value="aprobado">
                                                <input type="text" name="admin_comments" placeholder="Comentarios"
                                                    class="text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                <button type="submit" class="bg-green-600 text-white text-xs px-2 py-1 rounded hover:bg-green-700">
                                                    Aprobar
                                                </button>
                                            </form>
                                            <form action="{{ route('orders.update-status', $order) }}" method="POST" class="flex items-center space-x-2">
                                                @csrf
                                                <input type="hidden" name="status" value="rechazado">
                                                <input type="text" name="admin_comments" placeholder="Comentarios"
                                                    class="text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
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
            </div>
        </div>
    </div>
</div>
@endsection
