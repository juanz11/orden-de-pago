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

                <form method="POST" action="{{ route('orders.store') }}" enctype="multipart/form-data" class="space-y-8 divide-y divide-gray-200">
                    <input type="hidden" name="form_token" value="{{ Str::random(32) }}">
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
                        <label for="payment_condition" class="block text-sm font-medium text-gray-700">Condición de Pago</label>
                        <input type="text" name="payment_condition" id="payment_condition" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Ejemplo: 100% contra entrega"
                            value="{{ old('payment_condition') }}">
                        @error('payment_condition')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="payment_voucher" class="block text-sm font-medium text-gray-700">Soporte de presupuesto (PDF o Imagen)</label>
                        <input type="file" name="payment_voucher" id="payment_voucher" accept=".pdf,.jpg,.jpeg,.png"
                            class="mt-1 block w-full text-sm text-gray-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-md file:border-0
                                file:text-sm file:font-semibold
                                file:bg-blue-50 file:text-blue-700
                                hover:file:bg-blue-100"
                        >
                        @error('payment_voucher')
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
                                        <label class="block text-sm font-medium text-gray-700">Cantidad</label>
                                        <input type="number" name="items[0][quantity]" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            onchange="calculateItemTotal(this)">
                                    </div>
                                     <div>
                                        <label class="block text-sm font-medium text-gray-700">Precio Unitario (Bs.)</label>
                                        <input type="number" step="0.01" name="items[0][unit_price]" required
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
                        <button type="button" onclick="showConfirmModal()" id="createOrderBtn"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500" style="background-color: cornflowerblue;">
                            Crear Orden
                        </button>
                    </div>

                    <!-- Pantalla de carga -->
                    <div id="loadingScreen" class="hidden fixed inset-0 bg-gray-900 bg-opacity-90" style="z-index: 9999;">
                        <div class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-center">
                            <img src="{{ asset('img/logo.png') }}" alt="Logo" class="mx-auto w-64 h-64 animate-bounce" style="filter: drop-shadow(0 0 20px rgba(255,255,255,0.7));">
                            <div class="mt-8 text-white text-3xl font-bold tracking-wider flex items-center justify-center">
                                <span>Enviando orden</span>
                                <span class="dots ml-2 flex space-x-1">
                                    <span class="inline-block w-2 h-2 bg-white rounded-full animate-bounce" style="animation-delay: 0s;"></span>
                                    <span class="inline-block w-2 h-2 bg-white rounded-full animate-bounce" style="animation-delay: 0.2s;"></span>
                                    <span class="inline-block w-2 h-2 bg-white rounded-full animate-bounce" style="animation-delay: 0.4s;"></span>
                                </span>
                            </div>
                            <div class="mt-6 text-blue-300 text-xl">
                                Por favor, no cierre esta ventana
                            </div>
                        </div>
                    </div>

                    <!-- Modal de confirmación -->
                    <div id="confirmModal" class="fixed inset-0 bg-gray-900 bg-opacity-75 hidden h-full w-full" style="z-index: 9999;">
                        <div class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 p-8 w-[500px] shadow-2xl rounded-xl bg-white transition-all ease-in-out duration-300">
                            <div class="absolute top-0 right-0 pt-4 pr-4">
                                <button type="button" id="closeModal" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            <div class="text-center">
                                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-yellow-100 mb-6">
                                    <svg class="h-10 w-10 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <h3 class="text-2xl font-bold text-gray-900 mb-4">¿Está seguro?</h3>
                                <div class="mt-2 mb-6">
                                    <p class="text-lg text-gray-600">Esta acción creará una nueva orden de pago.<br>Por favor confirme si desea continuar.</p>
                                </div>
                                <div class="flex justify-center space-x-4">
                                    <div id="buttonsContainer" class="flex space-x-3">
                                        <button id="confirmYes" type="submit"
                                            class="inline-flex justify-center items-center px-6 py-3 bg-green-600 text-white text-lg font-medium rounded-lg shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                                            <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Confirmar
                                        </button>
                                        <button id="confirmNo" type="button"
                                            class="inline-flex justify-center items-center px-6 py-3 bg-gray-100 text-gray-700 text-lg font-medium rounded-lg shadow-sm hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                                            <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            Cancelar
                                        </button>
                                    </div>
                                    <div id="processingMessage" class="hidden text-center">
                                        <div class="inline-flex items-center">
                                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            <span class="text-blue-600 font-medium">Procesando orden...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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
    const form = document.querySelector('form');
    const confirmModal = document.getElementById('confirmModal');
    const buttonsContainer = document.getElementById('buttonsContainer');
    const loadingScreen = document.getElementById('loadingScreen');
    const confirmNo = document.getElementById('confirmNo');
    const createOrderBtn = document.getElementById('createOrderBtn');
    let hasSubmitted = false;
    let hasCancelled = false;

    // Manejar el envío del formulario
    form.addEventListener('submit', function(e) {
        e.preventDefault(); // Prevenir el envío normal del formulario

        if (hasSubmitted) {
            return;
        }

        hasSubmitted = true;

        // Ocultar el modal de confirmación
        closeModal();

        // Mostrar pantalla de carga
        loadingScreen.classList.remove('hidden');

        // Deshabilitar todos los inputs y botones
        const allInputs = form.getElementsByTagName('input');
        const allButtons = form.getElementsByTagName('button');
        const allSelects = form.getElementsByTagName('select');
        
        for (let el of [...allInputs, ...allButtons, ...allSelects]) {
            el.disabled = true;
        }

        // Esperar un momento para mostrar la animación y luego enviar
        setTimeout(() => {
            form.submit(); // Enviar el formulario
        }, 500);
    });

    window.showConfirmModal = function() {
        if (hasCancelled) {
            return;
        }
        confirmModal.classList.remove('hidden');
        // Efecto de entrada suave
        setTimeout(() => {
            confirmModal.querySelector('div').classList.remove('opacity-0', 'scale-95');
            confirmModal.querySelector('div').classList.add('opacity-100', 'scale-100');
        }, 10);
    };



    const closeModal = () => {
        // Efecto de salida suave
        confirmModal.querySelector('div').classList.remove('opacity-100', 'scale-100');
        confirmModal.querySelector('div').classList.add('opacity-0', 'scale-95');
        setTimeout(() => {
            confirmModal.classList.add('hidden');
        }, 200);
    };

    confirmNo.addEventListener('click', closeModal);
    document.getElementById('closeModal').addEventListener('click', closeModal);

    // Cerrar modal con Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !confirmModal.classList.contains('hidden')) {
            closeModal();
        }
    });

    // Cerrar modal al hacer clic fuera
    confirmModal.addEventListener('click', function(e) {
        if (e.target === confirmModal) {
            closeModal();
        }
    });

    let itemCount = 1;
    const bcvRate = parseFloat(document.querySelector('.exchange-rate-value')?.dataset?.rate || 0);

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
