<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class TradingAccount extends Model
{
    use SoftDeletes;

    // Relations
    public function accountType(): HasOne
    {
        return $this->hasOne(AccountType::class, 'id', 'account_type_id');
    }
}
