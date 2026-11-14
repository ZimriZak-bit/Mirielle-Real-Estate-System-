<?php

use App\Http\Controllers\CustomizationController;
use App\Http\Controllers\Management\CustomizationController as ManagementCustomizationController;
use App\Http\Controllers\Management\ViewingController as ManagementViewingController;
use App\Http\Controllers\ViewingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PAIR 3: Viewings and customization (Jewel & Collins)
|--------------------------------------------------------------------------
| Real pages replace the Pair 1 placeholders. These route names are frozen
| because other pairs link to them:
|   viewings.index, viewings.create  (I4 links "Request viewing" here)
|   customizations.index
|   management.viewings.index
|
| Only management may mark a viewing completed, so every completion route
| sits inside the management group.
*/

Route::middleware(['auth', 'role:client'])->group(function () {
    // My viewings: status, cancel, propose a new time.
    Route::get('/viewings', [ViewingController::class, 'index'])->name('viewings.index');

    // Interface I4: the property page's "Request viewing" button lands here.
    Route::get('/viewings/create/{property}', [ViewingController::class, 'create'])->name('viewings.create');
    Route::post('/viewings/{property}', [ViewingController::class, 'store'])->name('viewings.store');

    Route::post('/viewings/{viewing}/cancel', [ViewingController::class, 'cancel'])->name('viewings.cancel');
    Route::post('/viewings/{viewing}/reschedule', [ViewingController::class, 'reschedule'])->name('viewings.reschedule');

    // Customization preferences (stretch goal, interface I7).
    Route::get('/customizations', [CustomizationController::class, 'index'])->name('customizations.index');
    Route::post('/customizations/{property}', [CustomizationController::class, 'store'])->name('customizations.store');
});

Route::middleware(['auth', 'role:management'])->prefix('management')->group(function () {
    Route::get('/viewings', [ManagementViewingController::class, 'index'])->name('management.viewings.index');

    Route::post('/viewings/{viewing}/approve', [ManagementViewingController::class, 'approve'])->name('management.viewings.approve');
    Route::post('/viewings/{viewing}/reject', [ManagementViewingController::class, 'reject'])->name('management.viewings.reject');
    Route::post('/viewings/{viewing}/reschedule', [ManagementViewingController::class, 'reschedule'])->name('management.viewings.reschedule');
    Route::post('/viewings/{viewing}/complete', [ManagementViewingController::class, 'complete'])->name('management.viewings.complete');

    Route::get('/customizations', [ManagementCustomizationController::class, 'index'])->name('management.customizations.index');
});
