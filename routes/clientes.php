<?php

use App\Http\Controllers\Cliente\ClienteController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('clientes', ClienteController::class);
        
});