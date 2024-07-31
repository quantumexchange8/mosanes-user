<?php

namespace App\Services\Data;

use App\Models\TradingAccount;
use Illuminate\Support\Facades\DB;

class UpdateTradingAccount
{
    public function execute($meta_login, $data, $accountType): TradingAccount
    {
        return $this->updateTradingAccount($meta_login, $data, $accountType);
    }

    public function updateTradingAccount($meta_login, $data, $accountType): TradingAccount
    {
        $tradingAccount = TradingAccount::query()->where('meta_login', $meta_login)->first();
//        $accountType = AccountType::where('name', $tradingUser->meta_group)->first();

        $tradingAccount->currency_digits = $data['moneyDigits'];
        $tradingAccount->balance = $data['balance'] / 100;
        $tradingAccount->credit = $data['nonWithdrawableBonus'] / 100;
        $tradingAccount->margin_leverage = $data['leverageInCents'] / 100;
        $tradingAccount->equity = $data['equity'] / 100;
        $tradingAccount->account_type_id = $accountType;
        DB::transaction(function () use ($tradingAccount) {
            $tradingAccount->save();
        });

        return $tradingAccount;
    }
}
