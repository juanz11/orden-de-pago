@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-3xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Editar Orden</h2>
            <a href="{{ route('orders.index') }}" class="text-blue-600 hover:text-blue-900">
                Volver a Órdenes
            </a>
        </div>

        @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
        @endif

        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <form action="{{ route('orders.update', $order) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="description">
                        Descripción
                    </label>
                    <textarea
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('description') border-red-500 @enderror"
                        id="description"
                        name="description"
                        rows="3"
                        required
                    >{{ old('description', $order->description) }}</textarea>
                    @error('description')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="supplier_id">
                        Proveedor
                    </label>
                    <select
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('supplier_id') border-red-500 @enderror"
                        id="supplier_id"
                        name="supplier_id"
                        onchange="toggleOtherSupplier()"
                    >
                        <option value="">Seleccione un proveedor</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id', $order->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                        <option value="other" {{ old('supplier_id') == 'other' || (!$order->supplier_id && $order->other_supplier) ? 'selected' : '' }}>Otro</option>
                    </select>
                    @error('supplier_id')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>

                <div id="other_supplier_div" class="mb-4" style="display: none;">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="other_supplier">
                        Especifique el Proveedor
                    </label>
                    <input
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('other_supplier') border-red-500 @enderror"
                        id="other_supplier"
                        type="text"
                        name="other_supplier"
                        value="{{ old('other_supplier', $order->other_supplier) }}"
                    >
                    @error('other_supplier')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="unit_price">
                        Precio Unitario
                    </label>
                    <input
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('unit_price') border-red-500 @enderror"
                        id="unit_price"
                        type="number"
                        step="0.01"
                        name="unit_price"
                        value="{{ old('unit_price', $order->unit_price) }}"
                        required
                    >
                    @error('unit_price')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="quantity">
                        Cantidad
                    </label>
                    <input
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('quantity') border-red-500 @enderror"
                        id="quantity"
                        type="number"
                        name="quantity"
                        value="{{ old('quantity', $order->quantity) }}"
                        required
                    >
                    @error('quantity')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end">
                    <button type="submit" style="background-color: #10B981; color: white;" class="hover:bg-green-600 font-bold py-3 px-8 rounded-lg text-lg border-2 border-green-600 shadow-lg transform transition-all duration-200 hover:scale-105 hover:shadow-xl">
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleOtherSupplier() {
        const supplierSelect = document.getElementById('supplier_id');
        const otherSupplierDiv = document.getElementById('other_supplier_div');
        const otherSupplierInput = document.getElementById('other_supplier');
        
        if (supplierSelect.value === 'other') {
            otherSupplierDiv.style.display = 'block';
            otherSupplierInput.required = true;
        } else {
            otherSupplierDiv.style.display = 'none';
            otherSupplierInput.required = false;
            otherSupplierInput.value = '';
        }
    }

    // Ejecutar al cargar la página para manejar valores antiguos
    document.addEventListener('DOMContentLoaded', function() {
        toggleOtherSupplier();
    });
</script>
@endsection
