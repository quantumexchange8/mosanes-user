<?php

namespace App\Http\Controllers;

use App\Models\AssetMaster;
use App\Models\TradingAccount;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AssetMasterController extends Controller
{
    public function index()
    {
        return Inertia::render('AssetMaster/AssetMaster');
    }

    public function getMasters()
    {
        $masters = AssetMaster::where('status', 'active')->latest()->get()->map(function($master) {
            return [
                'id' => $master->id,
                'asset_name' => $master->asset_name,
                'trader_name' => $master->trader_name,
                'total_investors' => $master->total_investors,
                'total_fund' => $master->total_fund,
                'minimum_investment' => $master->minimum_investment,
                'minimum_investment_period' => $master->minimum_investment_period,
                'performance_fee' => $master->performance_fee,
                'total_gain' => $master->total_gain,
                'monthly_gain' => $master->monthly_gain,
                'latest_profit' => $master->latest_profit,
                'master_profile_photo' => $master->getFirstMediaUrl('master_profile_photo'),
            ];
        });

        return response()->json([
            'masters' => $masters
        ]);
    }

    public function getFilterMasters($filter)
    {
        $masters = '';
        if($filter === 'created_at' || $filter === 'total_fund' || $filter === 'total_investors') {
            $masters = AssetMaster::where('status', 'active')->orderBy($filter, 'desc')
                ->get()->map(function($master) {
                return [
                    'id' => $master->id,
                    'asset_name' => $master->asset_name,
                    'trader_name' => $master->trader_name,
                    'total_investors' => $master->total_investors,
                    'total_fund' => $master->total_fund,
                    'minimum_investment' => $master->minimum_investment,
                    'minimum_investment_period' => $master->minimum_investment_period,
                    'performance_fee' => $master->performance_fee,
                    'total_gain' => $master->total_gain,
                    'monthly_gain' => $master->monthly_gain,
                    'latest_profit' => $master->latest_profit,
                    'master_profile_photo' => $master->getFirstMediaUrl('master_profile_photo'),
                ];
            });
        }

        return response()->json([
            'masters' => $masters
        ]);
    }

    public function getAvailableAccounts(Request $request)
    {
        $accounts = TradingAccount::where('user_id', Auth::id())
            ->get()
            ->map(function($account) {
                return [
                    'id' => $account->id,
                    'meta_login' => $account->meta_login,
                ];
            });

        return response()->json([
            'accounts' => $accounts
        ]);
    }

    public function showPammInfo($id)
    {
        $master = AssetMaster::where('id', $id)->select('id', 'asset_name')->first();

        return Inertia::render('AssetMaster/PammInfo', ['master' => $master]);
    }

    public function getMasterDetail(Request $request)
    {
        $id = $request->id;
        $master = AssetMaster::find($id);

        $date = new DateTime($master->created_at);
        $duration = $date->diff(now())->format('%d');

        $master_detail = [
            'id' => $master->id,
            'asset_name' => $master->asset_name,
            'trader_name' => $master->trader_name,
            'total_investors' => $master->total_investors,
            'total_fund' => $master->total_fund,
            'minimum_investment' => $master->minimum_investment,
            'minimum_investment_period' => $master->minimum_investment_period,
            'performance_fee' => $master->performance_fee,
            'total_gain' => $master->total_gain,
            'monthly_gain' => $master->monthly_gain,
            'latest_profit' => $master->latest_profit,
            'with_us' => $duration,
            'profile_photo' => $master->getFirstMediaUrl('master_profile_photo'),
        ];

        return response()->json([
            'masterDetail' => $master_detail,
        ]);
    }
}
