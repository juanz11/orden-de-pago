@extends('layouts.app')

@section('content')
<div class="py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-md shadow-slate-200/60 rounded-2xl border border-slate-200/70">
            <div class="p-6 sm:p-8 text-gray-900">
                <div class="mb-8">
                    <h1 class="text-2xl sm:text-3xl font-bold text-slate-800">¡Bienvenido, {{ auth()->user()->name }}!</h1>
                    <div class="mt-2 inline-flex items-center gap-2">
                        <span class="text-slate-500">Departamento:</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700 ring-1 ring-inset ring-blue-200">
                            {{ auth()->user()->department }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    <!-- Tarjeta para Nueva Orden -->
                    <a href="{{ route('orders.create') }}" class="group block p-6 bg-white border border-slate-200 rounded-2xl shadow-sm hover:shadow-xl hover:shadow-blue-100 hover:-translate-y-1 hover:border-blue-300 transition-all duration-200">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100 text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors duration-200">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h5 class="mt-4 text-lg font-bold tracking-tight text-slate-800 group-hover:text-blue-700 transition-colors">Nueva Orden</h5>
                        <p class="mt-1 text-sm text-slate-500">Crear una nueva orden de pago</p>
                    </a>

                    <!-- Tarjeta para Mis Órdenes -->
                    <a href="{{ route('orders.index') }}" class="group block p-6 bg-white border border-slate-200 rounded-2xl shadow-sm hover:shadow-xl hover:shadow-sky-100 hover:-translate-y-1 hover:border-sky-300 transition-all duration-200">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-sky-100 text-sky-600 group-hover:bg-sky-600 group-hover:text-white transition-colors duration-200">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h5 class="mt-4 text-lg font-bold tracking-tight text-slate-800 group-hover:text-sky-700 transition-colors">Mis Órdenes</h5>
                        <p class="mt-1 text-sm text-slate-500">Ver todas mis órdenes de pago</p>
                    </a>

                    @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
                    <!-- Tarjeta para Gestionar Órdenes (Admin) -->
                    <a href="{{ route('orders.admin') }}" class="group block p-6 bg-white border border-slate-200 rounded-2xl shadow-sm hover:shadow-xl hover:shadow-emerald-100 hover:-translate-y-1 hover:border-emerald-300 transition-all duration-200">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition-colors duration-200">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h5 class="mt-4 text-lg font-bold tracking-tight text-slate-800 group-hover:text-emerald-700 transition-colors">Gestionar Órdenes</h5>
                        <p class="mt-1 text-sm text-slate-500">Administrar todas las órdenes</p>
                    </a>
                    @endif

                    @if(auth()->user()->isSuperAdmin())
                    <!-- Tarjeta para Gestionar Usuarios (Superadmin) -->
                    <a href="{{ route('users.index') }}" class="group block p-6 bg-white border border-slate-200 rounded-2xl shadow-sm hover:shadow-xl hover:shadow-violet-100 hover:-translate-y-1 hover:border-violet-300 transition-all duration-200">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-violet-100 text-violet-600 group-hover:bg-violet-600 group-hover:text-white transition-colors duration-200">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                            </svg>
                        </div>
                        <h5 class="mt-4 text-lg font-bold tracking-tight text-slate-800 group-hover:text-violet-700 transition-colors">Gestionar Usuarios</h5>
                        <p class="mt-1 text-sm text-slate-500">Administrar usuarios del sistema</p>
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
