<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TradingAccountController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

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


});

require __DIR__.'/auth.php';
