<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\AccountType;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\CTraderService;
use Illuminate\Support\Facades\App;
use App\Services\DropdownOptionService;

class TradingAccountController extends Controller
{
    public function index()
    {
        return Inertia::render('TradingAccount/Account');
    }

    public function accountOptions()
    {
        // Fetch account options where category is 'Individual' and status is 'active'
        $accountOptions = AccountType::whereNot('account_group', 'Demo Account')
            ->where('status', 'active')
            ->get();

        // Return the account options as JSON
        return response()->json($accountOptions);
    }
    
    public function getLeverages()
    {
        return response()->json([
            'leverages' => (new DropdownOptionService())->getLeveragesOptions(),
        ]);

    }
    public function create_live_account(Request $request)
    {
        $user = User::find($request->user_id);

        // Only create ct_user_id if it is null
        if ($user->ct_user_id === null) {
            // Create CT ID to link ctrader account
            $ctUser = (new CTraderService)->CreateCTID($user->email);
            $user->ct_user_id = $ctUser['userId'];
            $user->save();
        }
        
        // Retrieve the account type by account_group
        $accountType = AccountType::where('account_group', $request->accountType)->first();
        
        if (App::environment('production')) {
            $mainPassword = Str::random(8);
            $investorPassword = Str::random(8);
            (new CTraderService)->createUser($user,  $mainPassword, $investorPassword, $accountType->account_group, $request->leverage, $accountType->id, null, null, '');
        }

        return back()->with('toast', [
            'title' => 'trans("public.toast_open_live_account_success")',
            'type' => 'success',
        ]);

    }
}
