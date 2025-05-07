@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Gestión de Órdenes</h2>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
    @endif

    @include('components.exchange-rate')

    <div class="mb-4">
        <label for="department_filter" class="block text-sm font-medium text-gray-700">Filtrar por Departamento:</label>
        <select id="department_filter" class="mt-1 block w-64 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">Todos los departamentos</option>
            @foreach($departments as $department)
                <option value="{{ $department }}">{{ $department }}</option>
            @endforeach
        </select>
    </div>

    <div class="bg-white shadow-md rounded my-6">
        <table class="min-w-full table-auto">
            <thead>
                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <th class="py-3 px-6 text-left">Usuario</th>
                    <th class="py-3 px-6 text-left">Departamento</th>
                    <th class="py-3 px-6 text-left">Proveedor</th>
                    <th class="py-3 px-6 text-left">Productos</th>
                    <th class="py-3 px-6 text-left">Total</th>
                    <th class="py-3 px-6 text-left">Estado</th>
                    <th class="py-3 px-6 text-left">Pago</th>
                    <th class="py-3 px-6 text-left">Fecha</th>
                    <th class="py-3 px-6 text-left">Acciones</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm font-light">
                @foreach($orders as $order)
                <tr class="border-b border-gray-200 hover:bg-gray-100">
                    <td class="py-3 px-6 text-left">
                        {{ $order->user->name }}
                    </td>
                    <td class="py-3 px-6 text-left">
                        {{ $order->user->department }}
                    </td>
                    <td class="py-3 px-6 text-left">
                        @if($order->supplier)
                            {{ $order->supplier->name }}
                        @elseif($order->other_supplier)
                            {{ $order->other_supplier }} <span class="text-gray-500">(Otro)</span>
                        @else
                            <span class="text-gray-500">No especificado</span>
                        @endif
                    </td>
                    <td class="py-3 px-6">
                        <div class="space-y-1">
                            @foreach($order->items as $item)
                            <div class="flex justify-between text-sm">
                                <span class="font-medium">{{ $item->description }}</span>
                                <span class="text-gray-500">
                                    {{ $item->quantity }} x <x-format-currency :amount="$item->unit_price" />
                                </span>
                            </div>
                            @endforeach
                        </div>
                    </td>
                    <td class="py-3 px-6 text-left font-medium">
                        <x-format-currency :amount="$order->total" />
                    </td>
                    <td class="py-3 px-6">
                        <div class="flex flex-col">
                            <span class="@if($order->status === 'pendiente') text-yellow-600 @elseif($order->status === 'aprobado') text-green-600 @else text-red-600 @endif">
                                {{ ucfirst($order->status) }}
                            </span>
                            @if($order->status === 'pendiente')
                                <span class="text-xs text-gray-500">
                                    Aprobaciones: {{ $order->approval_progress }}
                                    @if($order->hasUserApproved(auth()->id()))
                                        (Ya has aprobado)
                                    @endif
                                </span>
                            @endif
                            @if($order->admin_comments)
                                <p class="text-xs text-gray-500 mt-1">{{ $order->admin_comments }}</p>
                            @endif
                        </div>
                    </td>
                    <td class="py-3 px-6">
                        <div class="flex flex-col">
                            <div class="flex items-center">
                                <div class="w-24 bg-gray-200 rounded-full h-2.5 mr-2">
                                    <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $order->total_paid_percentage }}%"></div>
                                </div>
                                <span class="text-sm">{{ number_format($order->total_paid_percentage, 1) }}%</span>
                            </div>
                            @if($order->payments->count() > 0)
                                <div class="text-xs text-gray-500 mt-1">
                                    Último pago: {{ $order->payments->sortByDesc('created_at')->first()->created_at->format('d/m/Y') }}
                                </div>
                            @endif
                        </div>
                    </td>
                    <td class="py-3 px-6 text-left">
                        {{ $order->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td class="py-3 px-6">
                        <div class="flex flex-col space-y-2">
                            @if($order->status === 'pendiente')
                                <a href="{{ route('orders.edit', $order) }}" 
                                   class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Editar
                                </a>
                                
                                @if(!$order->hasUserApproved(auth()->id()))
                                    <form action="{{ route('orders.update-status', $order) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="aprobado">
                                        <div class="flex flex-col space-y-2">
                                            @if($order->approval_count === 2)
                                                <input type="number" name="exchange_rate" step="0.01" min="0" 
                                                       placeholder="Tasa Bs/USD"
                                                       class="text-xs rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            @endif
                                            <input type="text" name="admin_comments" 
                                                   placeholder="Comentarios"
                                                   class="text-xs rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <button type="submit" 
                                                    class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                Aprobar
                                            </button>
                                        </div>
                                    </form>

                                    <form action="{{ route('orders.update-status', $order) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="rechazado">
                                        <div class="flex flex-col space-y-2">
                                            <input type="text" name="admin_comments" 
                                                   placeholder="Razón del rechazo" required
                                                   class="text-xs rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <button type="submit" 
                                                    class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                Rechazar
                                            </button>
                                        </div>
                                    </form>
                                @endif
                            @endif

                            @if($order->status === 'aprobado')
                                <div class="space-y-2">
                                    <form action="{{ route('orders.update-observations', $order) }}" method="POST" class="mb-2">
                                        @csrf
                                        <div class="flex items-center space-x-2">
                                            <input type="text" name="observations" 
                                                   placeholder="OBSERVACIONES:" 
                                                   value="{{ $order->observations }}"
                                                   class="text-xs w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <button type="submit"
                                                    class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                Guardar
                                            </button>
                                        </div>
                                    </form>
                                    <div class="flex flex-col space-y-2">
                                        <a href="{{ route('orders.pdf', ['order' => $order, 'currency' => 'bs']) }}" 
                                           class="inline-flex items-center justify-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                            Orden de Compra (Bs)
                                        </a>
                                        <a href="{{ route('orders.pdf', ['order' => $order, 'currency' => 'usd']) }}" 
                                           class="inline-flex items-center justify-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                            Orden de Compra ($)
                                        </a>
                                    </div>
                                </div>
                            @endif
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
@endsection

@push('scripts')
<script>
    // Department filter functionality
    document.getElementById('department_filter').addEventListener('change', function() {
        const selectedDepartment = this.value;
        const rows = document.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const departmentCell = row.querySelector('td:nth-child(2)');
            if (!selectedDepartment || departmentCell.textContent.trim() === selectedDepartment) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // Auto-fill exchange rate functionality
    document.querySelectorAll('form[action*="update-status"]').forEach(form => {
        form.addEventListener('submit', function(e) {
            const exchangeRateInput = this.querySelector('input[name="exchange_rate"]');
            if (exchangeRateInput && !exchangeRateInput.value) {
                const currentRate = document.querySelector('.exchange-rate-value').dataset.rate;
                if (currentRate) {
                    exchangeRateInput.value = currentRate;
                }
            }
        });
    });
</script>
@endpush
