<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class TradingAccount extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'balance' => 'decimal:2',
        'created_at' => 'datetime:Y-m-d',
    ];

    // Relations
    public function accountType(): HasOne
    {
        return $this->hasOne(AccountType::class, 'id', 'account_type_id');
    }

    public function account_type(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AccountType::class, 'account_type_id', 'id');
    }

    public function ofUser(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
