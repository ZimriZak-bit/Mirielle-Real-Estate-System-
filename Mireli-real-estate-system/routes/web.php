
<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Models\Property;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web routes (Pair 1 owns this file)
|--------------------------------------------------------------------------
| Public pages, login, dashboards and the admin reports.
| Other pairs keep their screens in separate route files.
*/

Route::get('/', function () {

    // Get the latest 3 available properties with their images
    $featuredProperties = Property::with('images')
        ->where('status', Property::AVAILABLE)
        ->latest()
        ->take(3)
        ->get();

    return view('welcome', compact('featuredProperties'));

})->name('home');

// ---- Guests only ----
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
});

// ---- Logged in ----
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');
});

// ---- Dashboards, one per role ----
Route::middleware(['auth', 'role:client'])
    ->get('/client', [DashboardController::class, 'client'])
    ->name('client.dashboard');

Route::middleware(['auth', 'role:management'])
    ->get('/management', [DashboardController::class, 'management'])
    ->name('management.dashboard');

Route::middleware(['auth', 'role:admin'])->group(function () {

    Route::get('/admin', [DashboardController::class, 'admin'])
        ->name('admin.dashboard');

    Route::get('/admin/reports', fn () => view('placeholder', [
        'title' => 'Reports',
        'pair' => 'Pair 1',
        'description' => 'Properties by status, viewings by status, deposits and successful payments, and payment plans. Built last, once the other slices have data.',
    ]))->name('admin.reports.index');

});

// ---- Other pairs' screens ----
require __DIR__.'/properties.php';
require __DIR__.'/viewings.php';
require __DIR__.'/payments.php';
