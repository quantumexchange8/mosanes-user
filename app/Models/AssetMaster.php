<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssetMaster extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'asset_name',
        'trader_name',
        'category',
        'type',
        'started_at',
        'total_investors',
        'total_fund',
        'minimum_investment',
        'minimum_investment_period',
        'performance_fee',
        'total_gain',
        'monthly_gain',
        'latest_profit',
        'profit_generation_mode',
        'expected_gain_profit',
        'edited_by',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
        ];
    }
}
