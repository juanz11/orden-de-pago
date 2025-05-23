<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Custom CSS -->
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <!-- Navigation -->
    <nav class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <!-- Logo -->
                    <div class="shrink-0 flex items-center">
                        <a href="{{ route('dashboard') }}">
                            <img src="https://muestras.sncpharma.com/images/logo/1.png" alt="Logo" class="h-12">
                        </a>
                    </div>

                    <!-- Navigation Links -->
                    <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                        @auth
                            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                                Dashboard
                            </x-nav-link>
                            <x-nav-link :href="route('orders.index')" :active="request()->routeIs('orders.index')">
                                Mis Órdenes
                            </x-nav-link>
                            <x-nav-link :href="route('orders.create')" :active="request()->routeIs('orders.create')">
                                Nueva Orden
                            </x-nav-link>
                            <x-nav-link :href="route('suppliers.index')" :active="request()->routeIs('suppliers.*')">
                                Proveedores
                            </x-nav-link>
                            @can('admin')
                                <x-nav-link :href="route('orders.admin')" :active="request()->routeIs('orders.admin')">
                                    Gestión de Órdenes
                                </x-nav-link>
                                <x-nav-link :href="route('orders.payments.index')" :active="request()->routeIs('orders.payments.*')">
                                    Gestión de Pagos
                                </x-nav-link>
                                <x-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')">
                                    Reportes
                                </x-nav-link>
                            @endcan
                            @can('superadmin')
                                <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                                    Usuarios
                                </x-nav-link>
                            @endcan
                        @endauth
                    </div>
                </div>

                <!-- User Menu -->
                <div class="hidden sm:flex sm:items-center sm:ml-6">
                    @auth
                        <div class="ml-3 relative">
                            <div class="flex items-center space-x-4">
                                <span class="text-gray-700">{{ auth()->user()->name }}</span>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="text-sm text-gray-500 hover:text-gray-700">
                                        Cerrar Sesión
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endauth
                </div>

                <!-- Hamburger -->
                <div class="-mr-2 flex items-center sm:hidden">
                    <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Responsive Navigation Menu -->
        <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
            <div class="pt-2 pb-3 space-y-1">
                @auth
                    <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        Dashboard
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('orders.index')" :active="request()->routeIs('orders.index')">
                        Mis Órdenes
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('orders.create')" :active="request()->routeIs('orders.create')">
                        Nueva Orden
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('suppliers.index')" :active="request()->routeIs('suppliers.*')">
                        Proveedores
                    </x-responsive-nav-link>
                    @can('admin')
                        <x-responsive-nav-link :href="route('orders.admin')" :active="request()->routeIs('orders.admin')">
                            Gestión de Órdenes
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('orders.payments.index')" :active="request()->routeIs('orders.payments.*')">
                            Gestión de Pagos
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')">
                            Reportes
                        </x-responsive-nav-link>
                    @endcan
                    @can('superadmin')
                        <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                            Usuarios
                        </x-responsive-nav-link>
                    @endcan
                @endauth
            </div>

            <!-- Responsive Settings Options -->
            <div class="pt-4 pb-1 border-t border-gray-200">
                @auth
                    <div class="px-4">
                        <div class="font-medium text-base text-gray-800">{{ auth()->user()->name }}</div>
                        <div class="font-medium text-sm text-gray-500">{{ auth()->user()->email }}</div>
                    </div>

                    <div class="mt-3 space-y-1">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out">
                                Cerrar Sesión
                            </button>
                        </form>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <main class="bg-gray-100">
        @yield('content')
    </main>

    <!-- Scripts -->
    @stack('scripts')
</body>
</html>
