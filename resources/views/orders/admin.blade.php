@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class=" mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="mb-4">
                    <h2 class="text-2xl font-bold">Gestión de Órdenes</h2>
                </div>

                @include('components.exchange-rate')

                <div class="mb-4">
                    <label for="department_filter" class="block text-sm font-medium text-gray-700">Filtrar por Departamento:</label>
                    <select id="department_filter" class="mt-1 block w-64 rounded-md border-black-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Todos los departamentos</option>
                        @foreach($departments as $department)
                            <option value="{{ $department }}">{{ $department }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="overflow-x-auto bg-white rounded-lg shadow overflow-y-auto relative">
                    <table class="min-w-full bg-white" style="
                    width: 100%;
                    ">
                        <thead class="bg-gray-100">
                            <tr >
                                <th class="px-6 py-3 border-b border-black-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                                <th class="px-6 py-3 border-b border-black-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Departamento</th>
                                <th class="px-6 py-3 border-b border-black-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Proveedor</th>
                                <th class="px-6 py-3 border-b border-black-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Productos</th>
                                <th class="px-6 py-3 border-b border-black-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="px-6 py-3 border-b border-black-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 border-b border-black-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 border-b border-black-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($orders as $order)
                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    {{ $order->user->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    {{ $order->user->department }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($order->supplier)
                                        {{ $order->supplier->name }}
                                    @elseif($order->other_supplier)
                                        {{ $order->other_supplier }} <span class="text-gray-500">(Otro)</span>
                                    @else
                                        <span class="text-gray-500">No especificado</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <div class="space-y-4">
                                        @foreach($order->items as $item)
                                        <div class="bg-gray-50 p-3 rounded-lg">
                                            <div class="font-medium text-gray-900">{{ $item->description }}</div>
                                            <div class="mt-2 grid grid-cols-3 gap-4 text-gray-600">
                                                <div>
                                                    <span class="font-medium">Cantidad:</span><br>
                                                    {{ $item->quantity }}
                                                </div>
                                                <div>
                                                    <span class="font-medium">Precio:</span><br>
                                                    <x-format-currency :amount="$item->unit_price" />
                                                </div>
                                                <div>
                                                    <span class="font-medium">Subtotal:</span><br>
                                                    <x-format-currency :amount="$item->quantity * $item->unit_price" />
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <x-format-currency :amount="$order->total" />
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($order->status === 'pendiente')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Pendiente
                                        </span>
                                    @elseif($order->status === 'aprobado')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Aprobado
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Rechazado
                                        </span>
                                    @endif
                                    @if($order->admin_comments)
                                        <p class="text-xs text-gray-500 mt-1">{{ $order->admin_comments }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    {{ $order->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($order->status === 'pendiente')
                                        <div class="space-y-2">
                                            <a href="{{ route('orders.edit', $order) }}" class="inline-block bg-blue-600  text-xs px-2 py-1 rounded hover:bg-blue-700 mb-2" style="background-color: cadetblue;">
                                                Editar
                                            </a>
                                            <form action="{{ route('orders.update-status', $order) }}" method="POST" class="flex items-center space-x-2">
                                                @csrf
                                                <input type="hidden" name="status" value="aprobado">
                                                <input type="text" name="admin_comments" placeholder="Comentarios"
                                                    class="text-sm rounded-md border-black-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                <button type="submit" class="bg-green-600 text-white text-xs px-2 py-1 rounded hover:bg-green-700">
                                                    Aprobar
                                                </button>
                                            </form>
                                            <form action="{{ route('orders.update-status', $order) }}" method="POST" class="flex items-center space-x-2">
                                                @csrf
                                                <input type="hidden" name="status" value="rechazado">
                                                <input type="text" name="admin_comments" placeholder="Comentarios"
                                                    class="text-sm rounded-md border-black-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                <button type="submit" class="bg-red-600 text-white text-xs px-2 py-1 rounded hover:bg-red-700">
                                                    Rechazar
                                                </button>
                                            </form>
                                        </div>
                                    @elseif($order->status === 'aprobado')
                                        <div class="space-y-2">
                                            <form action="{{ route('orders.update-observations', $order) }}" method="POST" class="flex items-center space-x-2 mb-2">
                                                @csrf
                                                <textarea name="observations" placeholder="Observaciones adicionales"
                                                    class="text-sm rounded-md border-black-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                    rows="2">{{ $order->observations }}</textarea>
                                                <button type="submit" class="bg-blue-600 text-white text-xs px-2 py-1 rounded hover:bg-blue-700">
                                                    Guardar
                                                </button>
                                            </form>
                                            <a href="{{ route('orders.pdf', $order) }}" class="inline-block bg-gray-600 text-white text-xs px-2 py-1 rounded hover:bg-gray-700" style="background-color: mediumaquamarine;">
                                                Descargar PDF
                                            </a>
                                        </div>
                                    @endif
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

@push('scripts')
<script>
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
</script>
@endpush
