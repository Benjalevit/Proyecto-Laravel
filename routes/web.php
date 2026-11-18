<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EstadisticasController;
use App\Http\Controllers\PartidaController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/registro', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/registro', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/salir', [AuthController::class, 'logout'])->name('logout');

    Route::get('/jugar', [PartidaController::class, 'jugar'])->name('jugar');
    Route::post('/partidas', [PartidaController::class, 'store'])->name('partidas.store');
    Route::get('/historial', [PartidaController::class, 'historial'])->name('historial');
    Route::put('/partidas/{partida}', [PartidaController::class, 'update'])->name('partidas.update');
    Route::delete('/partidas/{partida}', [PartidaController::class, 'destroy'])->name('partidas.destroy');

    Route::get('/estadisticas', [EstadisticasController::class, 'index'])->name('estadisticas');
});