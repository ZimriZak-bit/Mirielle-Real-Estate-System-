<?php

use App\Http\Controllers\PropertyController;
use Illuminate\Support\Facades\Route;

// Public route for clients to browse
Route::get('/properties', [PropertyController::class, 'index'])->name('properties.index');

// Route for a single property's details
Route::get('/properties/{property}', [PropertyController::class, 'show'])->name('properties.show');