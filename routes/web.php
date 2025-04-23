<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::middleware(['auth'])->group(function () {
    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/dashboard', function () {
            return view('dashboard');
        })->name('dashboard');

        // Rutas de órdenes para todos los usuarios
        Route::resource('orders', OrderController::class);
        Route::post('/orders/{order}/update-status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
        Route::get('/orders/{order}/pdf', [OrderController::class, 'downloadPdf'])->name('orders.pdf');

        // Rutas de proveedores para todos los usuarios (excepto delete)
        Route::resource('suppliers', SupplierController::class)->except(['destroy']);

        // Rutas de reportes
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/generate', [ReportController::class, 'generate'])->name('reports.generate');

        // Rutas solo para administradores
        Route::middleware(['can:admin'])->group(function () {
            Route::get('/admin/orders', [OrderController::class, 'adminIndex'])->name('orders.admin');
            // Rutas de usuarios
            Route::get('users', [UserController::class, 'index'])->name('users.index');
            Route::get('users/create', [UserController::class, 'create'])->name('users.create');
            Route::post('users', [UserController::class, 'store'])->name('users.store');
            Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
            Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
            Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
            // Ruta de eliminación de proveedores solo para admins
            Route::delete('suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');
        });
    });
});
