@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">
                {{ auth()->user()->isAdmin() ? 'Todas las Órdenes' : 'Mis Órdenes' }}
            </h2>
            @if(!auth()->user()->isAdmin())
            <a href="{{ route('orders.create') }}" class="bg-indigo-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Nueva Orden
            </a>
            @endif
        </div>

        @if(session('success'))
        <div class="rounded-md bg-green-50 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">
                        {{ session('success') }}
                    </p>
                </div>
            </div>
        </div>
        @endif

        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <ul class="divide-y divide-gray-200">
                @forelse($orders as $order)
                <li>
                    <div class="px-4 py-4 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-indigo-600 truncate">
                                    Orden #{{ $order->id }}
                                    @if(auth()->user()->isAdmin())
                                    - {{ $order->user->name }}
                                    @endif
                                </p>
                                <p class="mt-2 text-sm text-gray-600">
                                    {{ $order->description }}
                                </p>
                            </div>
                            <div class="ml-4 flex-shrink-0 flex items-center space-x-4">
                                <div class="flex flex-col items-end">
                                    <p class="text-sm text-gray-500">Cantidad: {{ $order->quantity }}</p>
                                    <p class="text-sm text-gray-500">Precio unitario: ${{ number_format($order->unit_price, 2) }}</p>
                                    <p class="text-lg font-bold text-gray-900">Total: ${{ number_format($order->total_amount, 2) }}</p>
                                </div>
                                <div class="flex flex-col items-end">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $order->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $order->status === 'declined' ? 'bg-red-100 text-red-800' : '' }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                    @if($order->admin_comments)
                                    <p class="mt-1 text-sm text-gray-500">{{ $order->admin_comments }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @if(auth()->user()->isAdmin() && $order->status === 'pending')
                        <div class="mt-4 flex justify-end space-x-3">
                            <form action="{{ route('orders.update-status', $order) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="approved">
                                <div class="flex items-center space-x-2">
                                    <input type="text" name="admin_comments" placeholder="Comentarios" required
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
                                    <input type="text" name="admin_comments" placeholder="Razón del rechazo" required
                                        class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <button type="submit" class="bg-red-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                        Rechazar
                                    </button>
                                </div>
                            </form>
                        </div>
                        @endif
                    </div>
                </li>
                @empty
                <li class="px-4 py-4 sm:px-6 text-center text-gray-500">
                    No hay órdenes para mostrar.
                </li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection
