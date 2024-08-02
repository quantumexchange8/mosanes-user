<?php

namespace App\Services;

use App\Models\PaymentAccount;
use App\Models\User;
use App\Models\Group;
use App\Models\Country;
use App\Models\Transaction;
use App\Models\GroupHasUser;
use App\Models\SettingLeverage;
use App\Models\TradingAccount;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class DropdownOptionService
{
    public function getUplines(): Collection
    {
        return User::whereIn('id', User::find(Auth::id())->getChildrenIds())
            ->select('id', 'name')
            ->get()
            ->map(function ($user) {
                return [
                    'value' => $user->id,
                    'name' => $user->name,
                    'profile_photo' => $user->getFirstMediaUrl('profile_photo')
                ];
            });
    }

    public function getCountries(): Collection
    {
        return Country::get()->map(function ($country) {
            return [
                'id' => $country->id,
                'name' => $country->name,
                'phone_code' => $country->phone_code,
            ];
        });
    }

    public function getGroups(): Collection
    {
        return Group::get()
            ->map(function ($group) {
                return [
                    'value' => $group->id,
                    'name' => $group->name,
                    'color' => $group->color,
                ];
            });
    }

    public function getLeverages(): Collection
    {
        $leverages = SettingLeverage::where('status', 'active')
                    ->get()
                    ->map(function ($leverage) {
                        return [
                            'name' => $leverage->display,
                            'value' => $leverage->value,
                        ];
                    });
        $leverages->prepend(['name' => 'Free', 'value' => 0]);
        return $leverages;
    }

    public function getLeveragesOptions(): Collection
    {
        $leverages = SettingLeverage::where('status', 'active')
                    ->get()
                    ->map(function ($leverage) {
                        return [
                            'name' => $leverage->display,
                            'value' => $leverage->value,
                        ];
                    });
        return $leverages;
    }

    public function getInternalTransferOptions(): Collection
    {
        $transferOptions = TradingAccount::where('user_id', Auth::id())
                    ->get()
                    ->map(function ($transferOption) {
                        return [
                            'name' => $transferOption->meta_login,
                            'value' => $transferOption->balance,
                        ];
                    });
        return $transferOptions;
    }

    public function getWalletOptions(): Collection
    {
        $walletOptions = PaymentAccount::where('user_id', Auth::id())
                    ->get()
                    ->map(function ($walletOption) {
                        return [
                            'name' => $walletOption->payment_account_name,
                            'value' => $walletOption->account_no,
                        ];
                    });
        return $walletOptions;
    }


    public function getAgents(): Collection
    {
        $has_group = GroupHasUser::pluck('user_id');

        $users = User::where('role', 'agent')
            ->whereNotIn('id', $has_group)
            ->select('id', 'name')
            ->get()
            ->map(function ($user) {
                return [
                    'value' => $user->id,
                    'name' => $user->name,
                    'profile_photo' => $user->getFirstMediaUrl('profile_photo')
                ];
            });

        return $users;
    }

    public function getTransactionMonths(): Collection
    {
        // Fetch the created_at dates of all transactions
        $transactionDates = Transaction::pluck('created_at');

        // Map the dates to the desired format and remove duplicates
        $months = $transactionDates
            ->map(function ($date) {
                return \Carbon\Carbon::parse($date)->format('m/Y');
            })
            ->unique()
            ->values();

        return $months;
    }

}
