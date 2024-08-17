<?php

namespace App\Http\Controllers;

use App\Models\AssetMaster;
use App\Models\AssetMasterProfitDistribution;
use App\Models\AssetSubscription;
use App\Models\TradingAccount;
use App\Services\CTraderService;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class AssetMasterController extends Controller
{
    public function index()
    {
        return Inertia::render('AssetMaster/AssetMaster');
    }

    public function getMasters()
    {
        $masters = AssetMaster::where('status', 'active')
            ->latest()
            ->get()
            ->map(function($master) {
                $asset_subscription = AssetSubscription::where('asset_master_id', $master->id)
                    ->where('status', 'ongoing');

                $asset_profit_distribution = AssetMasterProfitDistribution::where('asset_master_id', $master->id)
                    ->whereDate('profit_distribution_date', Carbon::yesterday())
                    ->first();

                $profit = $asset_profit_distribution ? $asset_profit_distribution->profit : 0;

                return [
                    'id' => $master->id,
                    'asset_name' => $master->asset_name,
                    'trader_name' => $master->trader_name,
                    'total_investors' => $master->total_investors + $asset_subscription->count(),
                    'total_fund' => $master->total_fund + $asset_subscription ->sum('investment_amount'),
                    'minimum_investment' => $master->minimum_investment,
                    'minimum_investment_period' => $master->minimum_investment_period,
                    'performance_fee' => $master->performance_fee,
                    'total_gain' => $master->total_gain,
                    'monthly_gain' => $master->monthly_gain,
                    'latest_profit' => $master->created_at->isToday() ? $master->latest_profit : $profit,
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
        $user = Auth::user();

        $manage_accounts = $user->tradingAccounts()
            ->whereHas('account_type', function($q) {
                $q->where('category', 'manage');
            })
            ->get();

        try {
            foreach ($manage_accounts as $trading_account) {
                (new CTraderService)->getUserInfo($trading_account->meta_login);
            }
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
        }

        $accounts = TradingAccount::where('user_id', $user->id)
            ->whereHas('account_type', function ($query) {
                $query->where('category', 'manage');
            })
            ->whereDoesntHave('asset_subscriptions', function ($query) {
                $query->where('status', 'ongoing');
            })
            ->get()
            ->map(function($account) {
                return [
                    'id' => $account->id,
                    'meta_login' => $account->meta_login,
                    'balance' => $account->balance,
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

    public function joinPamm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'meta_login' => ['required'],
            'investment_amount' => ['required'],
        ])->setAttributeNames([
            'meta_login' => trans('public.managed_account'),
            'investment_amount' => trans('public.investment_amount'),
        ]);
        $validator->validate();

        try {
            (new CTraderService)->getUserInfo($request->meta_login);
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
        }

        $trading_account = TradingAccount::where('meta_login', $request->meta_login)->first();
        $asset_master = AssetMaster::find($request->asset_master_id);

        if ($trading_account->balance < $asset_master->minimum_investment) {
            throw ValidationException::withMessages(['investment_amount' => trans('public.insufficient_balance')]);
        }

        $investment_periods = $asset_master->minimum_investment_period;

        AssetSubscription::create([
            'user_id' => Auth::id(),
            'meta_login' => $trading_account->meta_login,
            'asset_master_id' => $asset_master->id,
            'investment_amount' => $trading_account->balance,
            'investment_periods' => $investment_periods,
            'matured_at' => $investment_periods > 0 ? now()->addMonths($investment_periods)->endOfDay() : null,
        ]);

        return back()->with('toast', [
            'title' => trans("public.toast_join_pamm_successful"),
            'message' => trans("public.toast_join_pamm_successful_message"),
            'type' => 'success',
        ]);
    }
}
