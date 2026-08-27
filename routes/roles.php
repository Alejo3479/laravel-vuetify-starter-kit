<?php
use App\Http\Controllers\Role\RoleController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index')->middleware('can:listar.roles');
    
    Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create')->middleware('can:crear.roles');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store')->middleware('can:crear.roles');
    
    Route::get('/roles/{role}', [RoleController::class, 'show'])->name('roles.show')->middleware('can:ver.roles');
    
    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit')->middleware('can:editar.roles');
    Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update')->middleware('can:editar.roles');
    
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy')->middleware('can:eliminar.roles');
        
});