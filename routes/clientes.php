<?php

use App\Http\Controllers\Cliente\ClienteController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index')->middleware('can:listar.clientes');
    
    Route::get('/clientes/create', [ClienteController::class, 'create'])->name('clientes.create')->middleware('can:crear.clientes');
    Route::post('/clientes', [ClienteController::class, 'store'])->name('clientes.store')->middleware('can:crear.clientes');
    
    Route::get('/clientes/{cliente}', [ClienteController::class, 'show'])->name('clientes.show')->middleware('can:ver.clientes');
    
    Route::get('/clientes/{cliente}/edit', [ClienteController::class, 'edit'])->name('clientes.edit')->middleware('can:editar.clientes');
    Route::put('/clientes/{cliente}', [ClienteController::class, 'update'])->name('clientes.update')->middleware('can:editar.clientes');
    
    Route::delete('/clientes/{cliente}', [ClienteController::class, 'destroy'])->name('clientes.destroy')->middleware('can:eliminar.clientes');
        
});