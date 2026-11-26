<?php

use App\Http\Controllers\PropertyController;
use Illuminate\Support\Facades\Route;

// ---- CLIENT ROUTES ---
Route::get('/properties', [PropertyController::class, 'index'])->name('properties.index');
Route::get('/properties/{property}', [PropertyController::class, 'show'])->name('properties.show');

// --- MANAGEMENT ROUTES ---
Route::middleware(['auth'])->prefix('management')->group(function () {
    Route::get('/properties', [PropertyController::class, 'manage'])->name('management.properties.index');
    Route::get('/properties/create', [PropertyController::class, 'create'])->name('management.properties.create');
    Route::post('/properties', [PropertyController::class, 'store'])->name('management.properties.store');
    
    // Edit, Update, Delete
    Route::get('/properties/{property}/edit', [PropertyController::class, 'edit'])->name('management.properties.edit');
    Route::put('/properties/{property}', [PropertyController::class, 'update'])->name('management.properties.update');
    Route::delete('/properties/{property}', [PropertyController::class, 'destroy'])->name('management.properties.destroy');
});