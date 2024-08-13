<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\RebateController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StructureController;
use App\Http\Controllers\AssetMasterController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\DownloadCenterController;
use App\Http\Controllers\TradingAccountController;

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

    /**
     * ==============================
     *          Dashboard
     * ==============================
     */
    Route::prefix('dashboard')->group(function() {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/getDashboardData', [DashboardController::class, 'getDashboardData'])->name('getDashboardData');

        Route::post('/walletTransfer', [TransactionController::class, 'walletTransfer'])->name('dashboard.walletTransfer');
        Route::post('/walletWithdrawal', [TransactionController::class, 'walletWithdrawal'])->name('dashboard.walletWithdrawal');
    });

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
        Route::get('/info/{id}', [AssetMasterController::class, 'showPammInfo'])->name('asset_master.showPammInfo');
        Route::get('/getMasterDetail', [AssetMasterController::class, 'getMasterDetail'])->name('asset_master.getMasterDetail');
    });

    /**
     * ==============================
     *        Rebate Allocate
     * ==============================
     */
    Route::prefix('rebate_allocate')->group(function () {
        Route::get('/', [RebateController::class, 'index'])->name('rebate_allocate');
        Route::get('/getRebateAllocateData', [RebateController::class, 'getRebateAllocateData'])->name('rebate_allocate.getRebateAllocateData');
        Route::get('/getAgents', [RebateController::class, 'getAgents'])->name('rebate_allocate.getAgents');
    });

    /**
     * ==============================
     *          Transaction
     * ==============================
     */
    Route::prefix('transaction')->group(function () {
        Route::get('/', [TransactionController::class, 'index'])->name('transaction');
        Route::get('/getTotal', [TransactionController::class, 'getTotal'])->name('transaction.getTotal');
        Route::get('/getTransactions', [TransactionController::class, 'getTransactions'])->name('transaction.getTransactions');
    });

    /**
     * ==============================
     *        Download Center
     * ==============================
     */
    Route::prefix('download_center')->group(function () {
        Route::get('/', [DownloadCenterController::class, 'index'])->name('download_center');
    });

    /**
     * ==============================
     *            Profile
     * ==============================
     */
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('profile');
        Route::get('/getFilterData', [ProfileController::class, 'getFilterData'])->name('profile.getFilterData');
        Route::get('/getKycVerification', [ProfileController::class, 'getKycVerification'])->name('profile.getKycVerification');

        Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::post('/updateProfilePhoto', [ProfileController::class, 'updateProfilePhoto'])->name('profile.updateProfilePhoto');
        Route::post('/updateKyc', [ProfileController::class, 'updateKyc'])->name('profile.updateKyc');
        Route::post('/updateCryptoWalletInfo', [ProfileController::class, 'updateCryptoWalletInfo'])->name('profile.updateCryptoWalletInfo');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });
});

require __DIR__.'/auth.php';
