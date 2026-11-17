<?php

use App\Http\Controllers\PartidaController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PartidaController::class, 'jugar'])->name('jugar');
Route::post('/partidas', [PartidaController::class, 'store'])->name('partidas.store');
Route::get('/historial', [PartidaController::class, 'historial'])->name('historial');
Route::put('/partidas/{partida}', [PartidaController::class, 'update'])->name('partidas.update');
Route::delete('/partidas/{partida}', [PartidaController::class, 'destroy'])->name('partidas.destroy');
