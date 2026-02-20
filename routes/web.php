<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\PrefeituraController;
use App\Http\Controllers\ContratoController;
use App\Http\Controllers\LoteController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\EntregaController;


Route::get('/', function () {
    return auth()->check() 
        ? redirect()->route('dashboard') 
        : redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('empresas', EmpresaController::class);
    
    Route::resource('prefeituras', PrefeituraController::class);
    Route::resource('contratos', ContratoController::class);

    Route::resource('lotes', LoteController::class);
    
    Route::post('itens/import', [ItemController::class, 'import'])->name('itens.import');
    Route::get('itens/modelo', [ItemController::class, 'downloadModelo'])->name('itens.modelo');
    Route::resource('itens', ItemController::class);

    Route::resource('entregas', EntregaController::class);
    Route::get('entregas/{id}/recibo', [EntregaController::class, 'gerarRecibo'])->name('entregas.recibo');

});

require __DIR__.'/auth.php';