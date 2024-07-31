<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StructureController;
use App\Http\Controllers\TradingAccountController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return to_route('login');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    /**
     * ==============================
     *         Structure
     * ==============================
     */
    Route::prefix('structure')->group(function() {
        Route::get('/', [StructureController::class, 'show'])->name('structure');
        Route::get('/getDownlineData', [StructureController::class, 'getDownlineData'])->name('structure.getDownlineData');
        Route::get('/getDownlineListingData', [StructureController::class, 'getDownlineListingData'])->name('structure.getDownlineListingData');
        Route::get('/getFilterData', [StructureController::class, 'getFilterData'])->name('structure.getFilterData');
        Route::get('/downline/{id_number}', [StructureController::class, 'viewDownline'])->name('structure.viewDownline');
    });

    /**
     * ==============================
     *            Account
     * ==============================
     */
    Route::prefix('account')->group(function () {
        Route::get('/', [TradingAccountController::class, 'index'])->name('account');
        Route::get('/accountOptions', [TradingAccountController::class, 'accountOptions'])->name('account.accountOptions');
        Route::get('/getLeverages', [TradingAccountController::class, 'getLeverages'])->name('account.getLeverages');
        Route::post('/create_live_account', [TradingAccountController::class, 'create_live_account'])->name('account.create_live_account');
    });

    /**
     * ==============================
     *            Profile
     * ==============================
     */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
