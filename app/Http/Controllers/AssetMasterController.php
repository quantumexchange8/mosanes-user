<?php

namespace App\Http\Controllers;

use App\Models\AssetMaster;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AssetMasterController extends Controller
{
    public function index()
    {
        return Inertia::render('AssetMaster/AssetMaster');
    }

    public function getMasters()
    {
        $masters = AssetMaster::where('status', 'active')->get()->map(function($master) {
            return [
                'id' => $master->id,
                'asset_name' => $master->asset_name,
                'trader_name' => $master->trader_name,
                'total_investors' => $master->total_investors,
                'total_fund' => $master->total_fund,
                'minimum_invesment' => $master->minimum_invesment,
                'minimum_invesment_period' => $master->minimum_invesment_period,
                'performance_fee' => $master->performance_fee,
                'total_gain' => $master->total_gain,
                'monthly_gain' => $master->monthly_gain,
                'latest_profit' => $master->latest_profit,
            ];
        });

        return response()->json([
            'masters' => $masters
        ]);
    }

    public function getFilterMasters($filter)
    {
        if($filter === 'Latest') {
            // get data sort by desc
            $masters = AssetMaster::where('status', 'active')
                ->get()->map(function($master) {
                return [
                    'id' => $master->id,
                    'asset_name' => $master->asset_name,
                    'trader_name' => $master->trader_name,
                    'total_investors' => $master->total_investors,
                    'total_fund' => $master->total_fund,
                    'minimum_invesment' => $master->minimum_invesment,
                    'minimum_invesment_period' => $master->minimum_invesment_period,
                    'performance_fee' => $master->performance_fee,
                    'total_gain' => $master->total_gain,
                    'monthly_gain' => $master->monthly_gain,
                    'latest_profit' => $master->latest_profit,
                ];
            });
        }

        return response()->json([
            'masters' => $masters
        ]);
    }
}
