<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - {{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8">
            <div class="relative flex justify-between h-16">
                <div class="flex-shrink-0 flex items-center">
                    <img class="h-12" src="https://muestras.sncpharma.com/images/logo/1.png" alt="Logo">
                </div>
                <div class="flex items-center">
                    <form method="POST" action="{{ route('logout') }}" class="flex">
                        @csrf
                        <button type="submit" class="text-gray-600 hover:text-gray-900">
                            Cerrar Sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @if(auth()->user()->isAdmin())
                        <!-- Admin Options -->
                        <a href="{{ route('orders.index') }}" class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-50">
                            <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900">Gestionar Órdenes</h5>
                            <p class="font-normal text-gray-700">Revisa y gestiona todas las órdenes de pago pendientes.</p>
                        </a>
                        <div class="block p-6 bg-white border border-gray-200 rounded-lg shadow">
                            <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900">Reportes</h5>
                            <p class="font-normal text-gray-700">Accede a reportes y estadísticas del sistema.</p>
                        </div>
                        <div class="block p-6 bg-white border border-gray-200 rounded-lg shadow">
                            <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900">Configuración</h5>
                            <p class="font-normal text-gray-700">Administra la configuración del sistema.</p>
                        </div>
                        @else
                        <!-- Guest Options -->
                        <a href="{{ route('orders.create') }}" class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-50">
                            <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900">Nueva Orden</h5>
                            <p class="font-normal text-gray-700">Crea una nueva orden de pago.</p>
                        </a>
                        <a href="{{ route('orders.index') }}" class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-50">
                            <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900">Mis Órdenes</h5>
                            <p class="font-normal text-gray-700">Ver el estado de tus órdenes de pago.</p>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
