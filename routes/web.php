<?php

use App\Http\Controllers\AssetMasterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StructureController;
use App\Http\Controllers\TradingAccountController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

Route::get('locale/{locale}', function ($locale) {
    App::setLocale($locale);
    Session::put("locale", $locale);

    return redirect()->back();
});

Route::get('/', function () {
    return redirect(route('login'));
});

Route::post('deposit_callback', [TradingAccountController::class, 'depositCallback'])->name('depositCallback');

Route::middleware('auth')->group(function () {
    Route::get('deposit_return', [TradingAccountController::class, 'depositReturn']);
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

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
        Route::get('/getUserData', [StructureController::class, 'getUserData'])->name('structure.getUserData');
    });

    /**
     * ==============================
     *            Account
     * ==============================
     */
    Route::prefix('account')->group(function () {
        Route::get('/', [TradingAccountController::class, 'index'])->name('account');
        Route::get('/getOptions', [TradingAccountController::class, 'getOptions'])->name('account.getOptions');
        Route::get('/getAccountReport', [TradingAccountController::class, 'getAccountReport'])->name('account.getAccountReport');
        Route::get('/getLiveAccount', [TradingAccountController::class, 'getLiveAccount'])->name('account.getLiveAccount');
        Route::post('/create_live_account', [TradingAccountController::class, 'create_live_account'])->name('account.create_live_account');
        Route::post('/create_demo_account', [TradingAccountController::class, 'create_demo_account'])->name('account.create_demo_account');
        Route::post('/deposit_to_account', [TradingAccountController::class, 'deposit_to_account'])->name('account.deposit_to_account');
        Route::post('/withdrawal_from_account', [TradingAccountController::class, 'withdrawal_from_account'])->name('account.withdrawal_from_account');
        Route::post('/change_leverage', [TradingAccountController::class, 'change_leverage'])->name('account.change_leverage');
        Route::post('/internal_transfer', [TradingAccountController::class, 'internal_transfer'])->name('account.internal_transfer');
        Route::post('/revoke_account', [TradingAccountController::class, 'revoke_account'])->name('account.revoke_account');
        Route::delete('/delete_account', [TradingAccountController::class, 'delete_account'])->name('account.delete_account');
    });

    /**
     * ==============================
     *          Asset Master
     * ==============================
     */
    Route::prefix('asset_master')->group(function () {
        Route::get('/', [AssetMasterController::class, 'index'])->name('asset_master');
        Route::get('/getMasters', [AssetMasterController::class, 'getMasters'])->name('asset_master.getMasters');
        Route::get('/getFilterMasters/{filter}', [AssetMasterController::class, 'getFilterMasters'])->name('asset_master.getFilterMasters');
        Route::get('/getAvailableAccounts', [AssetMasterController::class, 'getAvailableAccounts'])->name('asset_master.getAvailableAccounts');
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
