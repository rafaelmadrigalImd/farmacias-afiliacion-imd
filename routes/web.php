<?php

use Illuminate\Support\Facades\Route;

// Ruta de inicio - redirige a login o dashboard según autenticación
Route::get('/', function () {
    return auth()->check()
        ? redirect('/dashboard')
        : redirect('/login');
});

// Dashboard (Jetstream ya registra automáticamente login, register, etc.)
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
