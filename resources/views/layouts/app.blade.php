<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Custom CSS -->
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-100">
    <div x-data="{ sidebarOpen: false }" class="min-h-screen">

        <!-- Backdrop (móvil) -->
        <div x-show="sidebarOpen"
             x-transition:enter="transition-opacity ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false"
             class="fixed inset-0 z-40 bg-slate-950/60 backdrop-blur-sm lg:hidden"
             x-cloak></div>

        <!-- Sidebar -->
        <aside class="fixed inset-y-0 left-0 z-50 w-64 flex flex-col bg-gradient-to-b from-slate-950 via-blue-950 to-blue-900 shadow-2xl shadow-blue-950/40 transform transition-transform duration-300 ease-in-out -translate-x-full lg:translate-x-0"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

            <!-- Logo -->
            <div class="flex items-center gap-3 h-20 px-5 border-b border-white/10 shrink-0">
                <a href="{{ route('dashboard') }}" class="flex items-center justify-center bg-white rounded-xl px-3 py-2 shadow-lg shadow-black/20">
                    <img src="https://muestras.sncpharma.com/images/logo/1.png" alt="Logo" class="h-9">
                </a>
                <div class="min-w-0">
                    <p class="text-white font-semibold leading-tight truncate">Órdenes de Pago</p>
                    <p class="text-xs text-blue-300/70 truncate">Panel de gestión</p>
                </div>
            </div>

            <!-- Navegación -->
            <nav class="flex-1 overflow-y-auto px-3 py-5 space-y-6">
                @auth
                <div>
                    <p class="px-3 mb-2 text-[11px] font-semibold uppercase tracking-widest text-blue-300/50">Principal</p>
                    <div class="space-y-1">
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                            <x-slot:icon>
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75" />
                                </svg>
                            </x-slot>
                            Dashboard
                        </x-nav-link>
                        <x-nav-link :href="route('orders.index')" :active="request()->routeIs('orders.index')">
                            <x-slot:icon>
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </x-slot>
                            Mis Órdenes
                        </x-nav-link>
                        <x-nav-link :href="route('orders.create')" :active="request()->routeIs('orders.create')">
                            <x-slot:icon>
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </x-slot>
                            Nueva Orden
                        </x-nav-link>
                        <x-nav-link :href="route('suppliers.index')" :active="request()->routeIs('suppliers.*')">
                            <x-slot:icon>
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.9 17.9 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                                </svg>
                            </x-slot>
                            Proveedores
                        </x-nav-link>
                    </div>
                </div>

                @can('admin')
                <div>
                    <p class="px-3 mb-2 text-[11px] font-semibold uppercase tracking-widest text-blue-300/50">Administración</p>
                    <div class="space-y-1">
                        <x-nav-link :href="route('orders.admin')" :active="request()->routeIs('orders.admin')">
                            <x-slot:icon>
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </x-slot>
                            Gestión de Órdenes
                        </x-nav-link>
                        <x-nav-link :href="route('orders.payments.index')" :active="request()->routeIs('orders.payments.*')">
                            <x-slot:icon>
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                                </svg>
                            </x-slot>
                            Gestión de Pagos
                        </x-nav-link>
                        <x-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')">
                            <x-slot:icon>
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                                </svg>
                            </x-slot>
                            Reportes
                        </x-nav-link>
                        @can('superadmin')
                        <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                            <x-slot:icon>
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                </svg>
                            </x-slot>
                            Usuarios
                        </x-nav-link>
                        @endcan
                    </div>
                </div>
                @endcan
                @endauth
            </nav>

            <!-- Usuario -->
            @auth
            <div class="shrink-0 border-t border-white/10 p-4 bg-black/20">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 shrink-0 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white text-sm font-bold shadow-lg shadow-blue-950/50 ring-2 ring-white/20">
                        {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-blue-300/70 truncate">{{ auth()->user()->email }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="mt-3">
                    @csrf
                    <button type="submit"
                            class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-lg border border-white/10 bg-white/5 text-xs font-medium text-blue-200 hover:bg-red-500/20 hover:border-red-400/30 hover:text-red-200 transition-colors duration-150">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                        </svg>
                        Cerrar Sesión
                    </button>
                </form>
            </div>
            @endauth
        </aside>

        <!-- Contenido -->
        <div class="lg:pl-64 flex flex-col min-h-screen">

            <!-- Barra superior (móvil) -->
            <header class="lg:hidden sticky top-0 z-30 flex items-center gap-3 h-16 px-4 bg-slate-950/95 backdrop-blur border-b border-white/10 shadow-lg">
                <button @click="sidebarOpen = true"
                        class="inline-flex items-center justify-center p-2 rounded-lg text-blue-200 hover:text-white hover:bg-white/10 focus:outline-none transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <a href="{{ route('dashboard') }}" class="flex items-center justify-center bg-white rounded-lg px-2 py-1">
                    <img src="https://muestras.sncpharma.com/images/logo/1.png" alt="Logo" class="h-7">
                </a>
                @auth
                <div class="ml-auto h-8 w-8 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white text-xs font-bold ring-2 ring-white/20">
                    {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                </div>
                @endauth
            </header>

            <main class="flex-1 bg-gradient-to-b from-blue-50/60 via-slate-100 to-slate-100">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Scripts -->
    @stack('scripts')
</body>
</html>
