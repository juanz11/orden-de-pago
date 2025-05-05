@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold">Gestión de Pagos</h2>
                    <a href="{{ route('orders.payments.create') }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                        Registrar Pago
                    </a>
                </div>

                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Orden #
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Proveedor
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Monto Total
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    % Pagado
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Pagos
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($orders as $order)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($order->supplier)
                                            {{ $order->supplier->name }}
                                        @else
                                            {{ $order->other_supplier }}
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <x-format-currency :amount="$order->total" />
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="relative w-48 h-2 bg-gray-200 rounded">
                                                <div class="absolute top-0 left-0 h-2 bg-blue-600 rounded" 
                                                     style="width: {{ $order->total_paid_percentage }}%">
                                                </div>
                                            </div>
                                            <span class="ml-2 text-sm text-gray-600">
                                                {{ number_format($order->total_paid_percentage, 1) }}%
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="space-y-2">
                                            @foreach($order->payments as $payment)
                                                <div class="text-sm">
                                                    <span class="font-medium">{{ number_format($payment->percentage, 1) }}%</span>
                                                    <span class="text-gray-500">
                                                        (<x-format-currency :amount="$payment->amount" />)
                                                        @if($payment->payment_type === 'banco')
                                                            - {{ ucfirst($payment->bank_name) }}
                                                            - Ref: {{ $payment->reference_number }}
                                                        @else
                                                            - Efectivo ${{ number_format($payment->cash_amount, 2) }}
                                                        @endif
                                                        - {{ $payment->accounting_entry }}
                                                        <a href="{{ route('orders.payments.receipt', ['order' => $order, 'payment' => $payment]) }}" 
                                                           class="inline-flex items-center ml-2 text-blue-600 hover:text-blue-800">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                                                            </svg>
                                                        </a>
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
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
        </div>
    </div>
</div>
@endsection
