<?php

namespace App\Services;

use App\Models\User;
use App\Models\Group;
use App\Models\Country;
use App\Models\Transaction;
use App\Models\GroupHasUser;
use App\Models\SettingLeverage;
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
}
