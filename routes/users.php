<?php

use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index')->middleware('can:listar.usuarios');
    
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create')->middleware('can:crear.usuarios');
    Route::post('/users', [UserController::class, 'store'])->name('users.store')->middleware('can:crear.usuarios');
    
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show')->middleware('can:ver.usuarios');
    
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit')->middleware('can:editar.usuarios');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update')->middleware('can:editar.usuarios');
    
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy')->middleware('can:eliminar.usuarios');
    
});