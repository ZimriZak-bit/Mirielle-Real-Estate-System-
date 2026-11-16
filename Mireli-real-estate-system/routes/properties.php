<?php

use App\Http\Controllers\PropertyController;
use Illuminate\Support\Facades\Route;

// Public route for clients to browse
Route::get('/properties', [PropertyController::class, 'index'])->name('properties.index');