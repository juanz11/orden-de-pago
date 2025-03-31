@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-2xl font-bold mb-4">¡Bienvenido {{ auth()->user()->name }}!</h1>
                <p class="mb-4">Departamento: {{ auth()->user()->department }}</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Tarjeta para Nueva Orden -->
                    <a href="{{ route('orders.create') }}" class="block p-6 bg-white border rounded-lg shadow hover:bg-gray-50">
                        <h5 class="mb-2 text-xl font-bold tracking-tight text-gray-900">Nueva Orden</h5>
                        <p class="font-normal text-gray-700">Crear una nueva orden de pago</p>
                    </a>

                    <!-- Tarjeta para Mis Órdenes -->
                    <a href="{{ route('orders.index') }}" class="block p-6 bg-white border rounded-lg shadow hover:bg-gray-50">
                        <h5 class="mb-2 text-xl font-bold tracking-tight text-gray-900">Mis Órdenes</h5>
                        <p class="font-normal text-gray-700">Ver todas mis órdenes de pago</p>
                    </a>

                    @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
                    <!-- Tarjeta para Gestionar Órdenes (Admin) -->
                    <a href="{{ route('orders.admin') }}" class="block p-6 bg-white border rounded-lg shadow hover:bg-gray-50">
                        <h5 class="mb-2 text-xl font-bold tracking-tight text-gray-900">Gestionar Órdenes</h5>
                        <p class="font-normal text-gray-700">Administrar todas las órdenes</p>
                    </a>
                    @endif

                    @if(auth()->user()->isSuperAdmin())
                    <!-- Tarjeta para Gestionar Usuarios (Superadmin) -->
                    <a href="{{ route('users.index') }}" class="block p-6 bg-white border rounded-lg shadow hover:bg-gray-50">
                        <h5 class="mb-2 text-xl font-bold tracking-tight text-gray-900">Gestionar Usuarios</h5>
                        <p class="font-normal text-gray-700">Administrar usuarios del sistema</p>
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
