@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold">Reporte de Órdenes</h2>
                    <form action="{{ route('reports.generate') }}" method="GET" class="inline-flex space-x-2">
                        <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                        <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                        <input type="hidden" name="department" value="{{ request('department') }}">
                        <button type="submit" name="format" value="pdf" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Descargar PDF
                        </button>
                        <button type="submit" name="format" value="excel" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            Descargar Excel
                        </button>
                    </form>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <span class="text-sm text-gray-500">Período:</span>
                            <p class="font-medium">{{ $start_date }} - {{ $end_date }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">Departamento:</span>
                            <p class="font-medium">{{ $department }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">Total General:</span>
                            <p class="font-medium"><x-format-currency :amount="$total" /></p>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto bg-white rounded-lg shadow overflow-y-auto relative">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Departamento</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Proveedor</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($orders as $order)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $order->created_at->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $order->user->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $order->user->department }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    {{ $order->supplier ? $order->supplier->name : $order->other_supplier }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <div class="space-y-1">
                                        @foreach($order->items as $item)
                                        <div>
                                            <span class="font-medium">{{ $item->description }}</span>
                                            <span class="text-gray-500 ml-2">
                                                ({{ $item->quantity }} x <x-format-currency :amount="$item->unit_price" />)
                                            </span>
                                        </div>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <x-format-currency :amount="$order->total" />
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($order->status === 'pendiente') bg-yellow-100 text-yellow-800
                                        @elseif($order->status === 'aprobado') bg-green-100 text-green-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ ucfirst($order->status) }}
                                    </span>
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
