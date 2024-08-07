<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard/Dashboard');
    }

    public function getDashboardData()
    {
        $user = Auth::user();

        $rebate_wallet = $user->rebate_wallet;

        return response()->json([
            'rebateWallet' => $rebate_wallet,
        ]);
    }
}
