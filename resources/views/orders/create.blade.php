@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="mb-4">
                    <h2 class="text-2xl font-bold">Nueva Orden</h2>
                </div>

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
                                        <label class="block text-sm font-medium text-gray-700">Precio Unitario</label>
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
                                    <span class="item-total">0.00</span>
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
                            <span class="text-lg font-medium">Total General: </span>
                            <span id="grand_total" class="text-lg font-bold">0.00</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-end space-x-3">
                        <a href="{{ route('orders.index') }}" 
                            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            Cancelar
                        </a>
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500">
                            Crear Orden
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
        totalSpan.textContent = '0.00';

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
        
        itemEntry.querySelector('.item-total').textContent = total.toFixed(2);
        calculateGrandTotal();
    };

    window.calculateGrandTotal = function() {
        const totals = Array.from(document.querySelectorAll('.item-total'))
            .map(span => parseFloat(span.textContent) || 0);
        
        const grandTotal = totals.reduce((sum, total) => sum + total, 0);
        document.getElementById('grand_total').textContent = grandTotal.toFixed(2);
    };

    // Inicializar
    const supplierSelect = document.getElementById('supplier_id');
    toggleOtherSupplier(supplierSelect.value);
});
</script>
@endpush
