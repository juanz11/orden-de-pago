@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="mb-4">
                    <h2 class="text-2xl font-bold">Registrar Pago</h2>
                </div>

                <form method="POST" action="{{ route('orders.payments.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="order_id" class="block text-sm font-medium text-gray-700">Orden</label>
                        <select name="order_id" id="order_id" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            required>
                            <option value="">Seleccionar orden</option>
                            @foreach($orders as $order)
                                <option value="{{ $order->id }}" 
                                        data-total="{{ $order->total }}"
                                        data-remaining="{{ $order->remaining_percentage }}">
                                    Orden #{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }} - 
                                    {{ $order->supplier ? $order->supplier->name : $order->other_supplier }}
                                    (Disponible: {{ number_format($order->remaining_percentage, 1) }}%)
                                </option>
                            @endforeach
                        </select>
                        @error('order_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="percentage" class="block text-sm font-medium text-gray-700">Porcentaje de Pago</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <input type="number" name="percentage" id="percentage" 
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 pr-12"
                                   placeholder="0" step="0.01" min="0.01" max="100"
                                   required>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                <span class="text-gray-500 sm:text-sm">%</span>
                            </div>
                        </div>
                        <p class="mt-1 text-sm text-gray-500" id="amount_preview"></p>
                        @error('percentage')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="reference_number" class="block text-sm font-medium text-gray-700">Número de Referencia</label>
                        <input type="text" name="reference_number" id="reference_number" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                               required>
                        @error('reference_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end space-x-3">
                        <a href="{{ route('orders.payments.index') }}" 
                            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            Cancelar
                        </a>
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                            Registrar Pago
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const orderSelect = document.getElementById('order_id');
    const percentageInput = document.getElementById('percentage');
    const amountPreview = document.getElementById('amount_preview');

    function updateAmountPreview() {
        const selectedOption = orderSelect.options[orderSelect.selectedIndex];
        if (selectedOption && selectedOption.value && percentageInput.value) {
            const total = parseFloat(selectedOption.dataset.total);
            const percentage = parseFloat(percentageInput.value);
            const amount = total * (percentage / 100);
            amountPreview.textContent = `Monto a pagar: ${amount.toLocaleString('es-VE', { style: 'currency', currency: 'VES' })}`;
        } else {
            amountPreview.textContent = '';
        }
    }

    orderSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption && selectedOption.value) {
            const remainingPercentage = parseFloat(selectedOption.dataset.remaining);
            percentageInput.max = remainingPercentage;
            percentageInput.placeholder = `Máximo: ${remainingPercentage}%`;
        }
        updateAmountPreview();
    });

    percentageInput.addEventListener('input', updateAmountPreview);
});
</script>
@endpush
