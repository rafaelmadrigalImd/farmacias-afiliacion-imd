<?php

use Illuminate\Support\Facades\Route;

// Ruta de inicio - redirige a login o dashboard según autenticación
Route::get('/', function () {
    return auth()->check()
        ? redirect('/dashboard')
        : redirect('/login');
});

// Dashboard y rutas protegidas (Jetstream ya registra automáticamente login, register, etc.)
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Rutas de Clientes
    Route::prefix('clientes')->group(function () {
        Route::get('/', \App\Livewire\Clientes\Index::class)->name('clientes.index');
        Route::get('/create', \App\Livewire\Clientes\Create::class)->name('clientes.create');
        Route::get('/{id}', \App\Livewire\Clientes\Show::class)->name('clientes.show');
    });
});
