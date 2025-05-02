@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="mb-4">
                    <h2 class="text-2xl font-bold">Nueva Orden</h2>
                </div>

                @include('components.exchange-rate')

                <form method="POST" action="{{ route('orders.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="supplier_id" class="block text-sm font-medium text-gray-700">Proveedor</label>
                        <select name="supplier_id" id="supplier_id" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            onchange="toggleOtherSupplier(this.value)">
                            <option value="">Seleccionar proveedor</option>
                            <option value="otro">Otro</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('supplier_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div id="other_supplier_div" style="display: none;">
                        <label for="other_supplier" class="block text-sm font-medium text-gray-700">Nombre del Otro Proveedor</label>
                        <input type="text" name="other_supplier" id="other_supplier" value="{{ old('other_supplier') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('other_supplier')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="payment_type" class="block text-sm font-medium text-gray-700">Tipo de Pago</label>
                        <select name="payment_type" id="payment_type" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="full">Total</option>
                            <option value="partial">Parcial</option>
                        </select>
                        @error('payment_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div id="payment_percentage_container" class="hidden">
                        <label for="payment_percentage" class="block text-sm font-medium text-gray-700">Porcentaje de Pago</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <input type="hidden" name="payment_percentage" id="payment_percentage_hidden" value="">
                            <input type="text" id="payment_percentage" 
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 pr-12"
                                   placeholder="0" pattern="[0-9]*" inputmode="numeric">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                <span class="text-gray-500 sm:text-sm">%</span>
                            </div>
                        </div>
                        <p class="mt-1 text-sm text-gray-500" id="remaining_percentage_text"></p>
                        @error('payment_percentage')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div id="related_order_container" class="hidden">
                        <label for="related_order_id" class="block text-sm font-medium text-gray-700">Orden Relacionada</label>
                        <select name="related_order_id" id="related_order_id" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Seleccionar orden...</option>
                            @foreach($orders as $order)
                                <option value="{{ $order->id }}" data-remaining="{{ $order->remaining_percentage }}">
                                    Orden #{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }} - 
                                    {{ $order->supplier ? $order->supplier->name : $order->other_supplier }}
                                    (Pagado: {{ number_format($order->total_paid_percentage, 1) }}% - Disponible: {{ number_format($order->remaining_percentage, 1) }}%)
                                </option>
                            @endforeach
                        </select>
                        @error('related_order_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div id="items_container">
                        <div class="mb-4">
                            <h3 class="text-lg font-medium">Productos</h3>
                        </div>

                        <div class="space-y-4" id="items_list">
                            <div class="border p-4 rounded-md item-entry">
                                <button type="button" onclick="removeItem(this)" class="remove-item-btn text-red-500 hover:text-red-700">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Descripción</label>
                                        <input type="text" name="items[0][description]" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Precio Unitario (Bs.)</label>
                                        <input type="number" step="0.01" name="items[0][unit_price]" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            onchange="calculateItemTotal(this)">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Cantidad</label>
                                        <input type="number" name="items[0][quantity]" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            onchange="calculateItemTotal(this)">
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <span class="text-sm font-medium text-gray-700">Total: </span>
                                    <span class="item-total">0,00</span>
                                    <span class="text-sm text-gray-700"> Bs.</span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 flex space-x-4">
                            <button type="button" onclick="addItem()" 
                                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500">
                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Agregar Producto
                            </button>
                            <button type="button" onclick="removeLastItem()" 
                                class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500">
                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                </svg>
                                Quitar Producto
                            </button>
                        </div>

                        <div class="mt-4 text-right">
                            <div>
                                <span class="text-lg font-medium">Total General: </span>
                                <span id="grand_total" class="text-lg font-bold">0,00</span>
                                <span class="text-lg font-medium"> Bs.</span>
                            </div>
                            <div class="text-sm text-gray-600 mt-1">
                                <span>Equivalente: $</span>
                                <span id="dollar_total" class="font-medium">0.00</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end space-x-3">
                        <a href="{{ route('orders.index') }}" 
                            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            Cancelar
                        </a>
                        <button type="submit" id="submit-button"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500" style="background-color: cornflowerblue;">
                            <svg id="loading-spinner" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span id="button-text">Crear Orden</span>
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
    let itemCount = 1;
    const bcvRate = parseFloat(document.querySelector('.exchange-rate-value')?.dataset?.rate || 0);
    const form = document.querySelector('form');
    const submitButton = document.getElementById('submit-button');
    const loadingSpinner = document.getElementById('loading-spinner');
    const buttonText = document.getElementById('button-text');
    const paymentPercentageInput = document.getElementById('payment_percentage');
    const paymentPercentageHidden = document.getElementById('payment_percentage_hidden');

    // Validate payment percentage input
    paymentPercentageInput?.addEventListener('input', function(e) {
        // Remove any non-numeric characters
        let value = this.value.replace(/[^0-9]/g, '');
        
        // Convert to number and validate range
        let numValue = parseInt(value) || 0;
        if (numValue > 100) numValue = 100;
        if (numValue < 0) numValue = 0;
        
        // Update input values
        this.value = numValue;
        paymentPercentageHidden.value = numValue;

        // Check related order's remaining percentage
        const relatedOrderSelect = document.getElementById('related_order_id');
        const selectedOption = relatedOrderSelect.options[relatedOrderSelect.selectedIndex];
        if (selectedOption && selectedOption.value) {
            const remainingPercentage = parseFloat(selectedOption.dataset.remaining);
            if (numValue > remainingPercentage) {
                this.value = Math.floor(remainingPercentage);
                paymentPercentageHidden.value = this.value;
            }
        }
    });

    // Form submission handler
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Set payment percentage for full payment
        const paymentType = document.getElementById('payment_type').value;
        if (paymentType === 'full') {
            paymentPercentageHidden.value = '100';
        }
        
        // Disable button and show loading state
        submitButton.disabled = true;
        submitButton.style.opacity = '0.75';
        loadingSpinner.classList.remove('hidden');
        buttonText.textContent = 'Creando...';

        // Submit the form
        this.submit();
    });

    // Manejo de la condición de pago
    const paymentTypeSelect = document.getElementById('payment_type');
    const percentageContainer = document.getElementById('payment_percentage_container');
    const relatedOrderContainer = document.getElementById('related_order_container');
    const percentageInput = document.getElementById('payment_percentage');
    const relatedOrderSelect = document.getElementById('related_order_id');
    const remainingText = document.getElementById('remaining_percentage_text');

    // Mostrar/ocultar campos según el tipo de pago
    paymentTypeSelect?.addEventListener('change', function() {
        const isPartial = this.value === 'partial';
        percentageContainer.classList.toggle('hidden', !isPartial);
        relatedOrderContainer.classList.toggle('hidden', !isPartial);
        
        if (!isPartial) {
            percentageInput.value = '100';
            paymentPercentageHidden.value = '100';
            relatedOrderSelect.value = '';
            remainingText.textContent = '';
        } else {
            percentageInput.value = '';
            paymentPercentageHidden.value = '';
            relatedOrderSelect.value = '';
            remainingText.textContent = '';
        }
    });

    // Actualizar información cuando se selecciona una orden relacionada
    relatedOrderSelect?.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption && selectedOption.value) {
            const remainingPercentage = selectedOption.dataset.remaining;
            remainingText.textContent = `Porcentaje disponible para pago: ${remainingPercentage}%`;
            percentageInput.max = remainingPercentage;
            if (parseFloat(percentageInput.value) > parseFloat(remainingPercentage)) {
                percentageInput.value = remainingPercentage;
                paymentPercentageHidden.value = percentageInput.value;
            }
        } else {
            remainingText.textContent = '';
            percentageInput.max = 100;
        }
    });

    // Validar que el porcentaje no exceda el máximo permitido
    percentageInput?.addEventListener('input', function() {
        const selectedOption = relatedOrderSelect.options[relatedOrderSelect.selectedIndex];
        if (selectedOption && selectedOption.value) {
            const remainingPercentage = parseFloat(selectedOption.dataset.remaining);
            const currentValue = parseFloat(this.value);
            if (currentValue > remainingPercentage) {
                this.value = remainingPercentage;
                paymentPercentageHidden.value = this.value;
            }
        }
    });

    // Resto del código existente...
    window.toggleOtherSupplier = function(value) {
        const otherSupplierDiv = document.getElementById('other_supplier_div');
        otherSupplierDiv.style.display = value === 'otro' ? 'block' : 'none';
        
        if (value !== 'otro') {
            document.getElementById('other_supplier').value = '';
        }
    };

    window.addItem = function() {
        const template = document.querySelector('.item-entry').cloneNode(true);
        const inputs = template.querySelectorAll('input');
        
        inputs.forEach(input => {
            const name = input.getAttribute('name');
            input.setAttribute('name', name.replace('[0]', `[${itemCount}]`));
            input.value = '';
        });

        const totalSpan = template.querySelector('.item-total');
        totalSpan.textContent = new Intl.NumberFormat('es-VE', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(0);

        document.getElementById('items_list').appendChild(template);
        itemCount++;
    };

    window.removeItem = function(button) {
        const itemsList = document.getElementById('items_list');
        if (itemsList.children.length > 1) {
            button.closest('.item-entry').remove();
            calculateGrandTotal();
        }
    };

    window.removeLastItem = function() {
        const itemsList = document.getElementById('items_list');
        if (itemsList.children.length > 1) {
            itemsList.lastChild.remove();
            calculateGrandTotal();
        }
    };

    window.calculateItemTotal = function(input) {
        const itemEntry = input.closest('.item-entry');
        const unitPrice = parseFloat(itemEntry.querySelector('input[name*="[unit_price]"]').value) || 0;
        const quantity = parseInt(itemEntry.querySelector('input[name*="[quantity]"]').value) || 0;
        const total = unitPrice * quantity;
        
        const formattedTotal = new Intl.NumberFormat('es-VE', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(total);
        
        itemEntry.querySelector('.item-total').textContent = formattedTotal;
        calculateGrandTotal();
    };

    window.calculateGrandTotal = function() {
        const totals = Array.from(document.querySelectorAll('.item-total'))
            .map(span => parseFloat(span.textContent.replace(/\./g, '').replace(',', '.')) || 0);
        
        const grandTotal = totals.reduce((sum, total) => sum + total, 0);
        const formattedGrandTotal = new Intl.NumberFormat('es-VE', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(grandTotal);
        
        document.getElementById('grand_total').textContent = formattedGrandTotal;

        // Calcular equivalente en dólares
        if (bcvRate > 0) {
            const dollarTotal = grandTotal / bcvRate;
            const formattedDollarTotal = new Intl.NumberFormat('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(dollarTotal);
            document.getElementById('dollar_total').textContent = formattedDollarTotal;
        }
    };

    // Inicializar
    const supplierSelect = document.getElementById('supplier_id');
    toggleOtherSupplier(supplierSelect.value);
});
</script>
@endpush
