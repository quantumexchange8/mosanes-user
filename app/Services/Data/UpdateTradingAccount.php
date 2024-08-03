<?php

namespace App\Services\Data;

use App\Models\AccountType;
use App\Models\TradingAccount;
use Illuminate\Support\Facades\DB;

class UpdateTradingAccount
{
    public function execute($meta_login, $data, $accountTypeId): TradingAccount
    {
        return $this->updateTradingAccount($meta_login, $data, $accountTypeId);
    }

    public function updateTradingAccount($meta_login, $data, $accountTypeId): TradingAccount
    {
        $tradingAccount = TradingAccount::query()->where('meta_login', $meta_login)->first();
        \Log::debug('trading_account', $data);

        $tradingAccount->currency_digits = $data['moneyDigits'];
        $tradingAccount->balance = $data['balance'] / 100;
        $tradingAccount->credit = $data['nonWithdrawableBonus'] / 100;
        $tradingAccount->margin_leverage = $data['leverageInCents'] / 100;
        $tradingAccount->equity = $data['equity'] / 100;
        $tradingAccount->account_type_id = $accountTypeId;
        DB::transaction(function () use ($tradingAccount) {
            $tradingAccount->save();
        });

        return $tradingAccount;
    }
}
