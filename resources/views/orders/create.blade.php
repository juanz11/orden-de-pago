@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h2 class="text-2xl font-bold mb-4">Crear Nueva Orden</h2>

                <form action="{{ route('orders.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">
                            Descripción
                        </label>
                        <textarea id="description" name="description" rows="3" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            >{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label for="unit_price" class="block text-sm font-medium text-gray-700">
                                Monto por Unidad ($)
                            </label>
                            <input type="number" step="0.01" min="0" id="unit_price" name="unit_price" required
                                value="{{ old('unit_price') }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                onchange="calculateTotal()">
                            @error('unit_price')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="quantity" class="block text-sm font-medium text-gray-700">
                                Cantidad
                            </label>
                            <input type="number" min="1" id="quantity" name="quantity" required
                                value="{{ old('quantity', 1) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                onchange="calculateTotal()">
                            @error('quantity')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Total
                        </label>
                        <div id="total" class="mt-1 text-xl font-bold text-indigo-600">$0.00</div>
                    </div>

                    <div class="flex justify-end">
                        <a href="{{ route('orders.index') }}" class="bg-gray-200 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            Cancelar
                        </a>
                        <button type="submit" class="ml-3 bg-indigo-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Crear Orden
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function calculateTotal() {
    const unitPrice = parseFloat(document.getElementById('unit_price').value) || 0;
    const quantity = parseInt(document.getElementById('quantity').value) || 0;
    const total = unitPrice * quantity;
    document.getElementById('total').textContent = `$${total.toFixed(2)}`;
}

// Calculate initial total
calculateTotal();
</script>
@endsection
