<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\AccountType;
use App\Models\TradingUser;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\PaymentAccount;
use App\Models\TradingAccount;
use Illuminate\Support\Carbon;
use App\Services\CTraderService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Services\RunningNumberService;
use App\Services\DropdownOptionService;
use App\Services\ChangeTraderBalanceType;
use Illuminate\Validation\ValidationException;

class TradingAccountController extends Controller
{
    public function index()
    {
        return Inertia::render('TradingAccount/Account');
    }

    public function getOptions()
    {
        // Fetch account options where category is 'Individual' and status is 'active'
        $accountOptions = AccountType::whereNot('account_group', 'Demo Account')
            ->where('status', 'active')
            ->get();
    
        return response()->json([
            'leverages' => (new DropdownOptionService())->getLeveragesOptions(),
            'transferOptions' => (new DropdownOptionService())->getInternalTransferOptions(),
            'walletOptions' => (new DropdownOptionService())->getWalletOptions(),
            'accountOptions' => $accountOptions,
        ]);
    }

    public function create_live_account(Request $request)
    {
        // Validate the request data
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'accountType' => 'required|exists:account_types,account_group',
            'leverage' => 'required|integer|min:1',
        ]);

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

        // Check the number of existing trading accounts for this user and account type
        $existingAccountsCount = TradingAccount::where('user_id', $user->id)
            ->where('account_type_id', $accountType->id)
            ->count();

        // Check if the user has reached the maximum number of accounts
        if ($existingAccountsCount >= $accountType->maximum_account_number) {
            return back()->with('toast', [
                'title' => trans("public.account_limit_reach"),
                'message' => trans("public.account_limit_reach_message"),
                'type' => 'warning',
            ]);
        }
        
        if (App::environment('production')) {
            $mainPassword = Str::random(8);
            $investorPassword = Str::random(8);
            (new CTraderService)->createUser($user,  $mainPassword, $investorPassword, $accountType->account_group, $request->leverage, $accountType->id, null, null, '');
        }

        return back()->with('toast', [
            'title' => trans("public.toast_open_live_account_success"),
            'type' => 'success',
        ]);
    }

    public function create_demo_account(Request $request)
    {
        // Validate the request data
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric',
            'leverage' => 'required|integer|min:1',
        ]);

        
        return back()->with('toast', [
            'title' => trans("public.toast_open_demo_account_success"),
            'type' => 'success',
        ]);
    }

    public function getLiveAccount(Request $request)
    {
        $user = Auth::user();
        $accountType = $request->input('accountType');

        $liveAccounts = TradingAccount::with('account_type')
            ->where('user_id', $user->id)
            ->when($accountType, function ($query) use ($accountType) {
                return $query->whereHas('account_type', function ($query) use ($accountType) {
                    $query->where('category', $accountType);
                });
            })
            ->get()
            ->map(function ($account) {
                return [
                    'id' => $account->id,
                    'user_id' => $account->user_id,
                    'meta_login' => $account->meta_login,
                    'balance' => $account->balance,
                    'credit' => $account->credit,
                    'leverage' => $account->margin_leverage,
                    'equity' => $account->equity,
                    'account_type' => $account->account_type->name
                ];
            });

        return response()->json($liveAccounts);
    }

    public function getAccountReport(Request $request)
    {
        $meta_login = $request->query('meta_login');
        $startDate = $request->query('startDate');
        $endDate = $request->query('endDate');
        $type = $request->query('type');

        // Convert date strings to YYYY-MM-DD format directly
        if ($startDate) {
            // Extract the date part from the string
            if (preg_match('/(\w{3} \w{3} \d{2} \d{4})/', $startDate, $matches)) {
                $datePart = $matches[1]; // e.g., "Jul 10 2024"
                $startDate = (new \DateTime($datePart))->format('Y-m-d');
            } else {
                $startDate = null; // Handle error or invalid format
            }
        }

        if ($endDate) {
            // Extract the date part from the string
            if (preg_match('/(\w{3} \w{3} \d{2} \d{4})/', $endDate, $matches)) {
                $datePart = $matches[1]; // e.g., "Jul 10 2024"
                $endDate = (new \DateTime($datePart))->format('Y-m-d');
            } else {
                $endDate = null; // Handle error or invalid format
            }
        }

        // Query for transactions
        $query = Transaction::query();
    
        // Ensure category is 'trading_account'
        $query->where('category', 'trading_account');

        if ($meta_login) {
            $query->where(function($subQuery) use ($meta_login) {
                $subQuery->where('from_meta_login', $meta_login)
                         ->orWhere('to_meta_login', $meta_login);
            });
        }
            
        // Apply date filter based on availability of startDate and/or endDate
        if ($startDate && $endDate) {
            // Both startDate and endDate are provided
            $query->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate);
        }
    
        // Apply type filter
        if ($type && $type !== 'all') {
            // Filter based on specific transaction types directly
            if ($type === 'deposit') {
                $query->where('transaction_type', 'deposit');
            } elseif ($type === 'withdrawal') {
                $query->where('transaction_type', 'withdrawal');
            } elseif ($type === 'transfer') {
                $query->where('transaction_type', 'transfer');
            }
        }

        $transactions = $query->latest()->get();

        return response()->json($transactions);
    }

    public function deposit_to_account(Request $request)
    {
        $request->validate([
            'account_id' => 'required|exists:trading_accounts,id',
        ]);
    
        // $tradingAccount = TradingAccount::find($request->account_id);
        // (new CTraderService)->getUserInfo(collect($tradingAccount));

        // $tradingAccount = TradingAccount::find($request->account_id);
        // $amount = $request->input('amount');
        // $wallet = Auth::user()->wallet->first();

        // if ($wallet->balance < $amount) {
        //     throw ValidationException::withMessages(['wallet' => trans('public.insufficient_balance')]);
        // }

        // try {
        //     $trade = (new CTraderService)->createTrade($tradingAccount->meta_login, $amount, $tradingAccount->account_type_id, "Deposit To Account", ChangeTraderBalanceType::DEPOSIT);
        // } catch (\Throwable $e) {
        //     if ($e->getMessage() == "Not found") {
        //         TradingUser::firstWhere('meta_login', $tradingAccount->meta_login)->update(['acc_status' => 'Inactive']);
        //     } else {
        //         Log::error($e->getMessage());
        //     }
        //     return response()->json(['success' => false, 'message' => $e->getMessage()]);
        // }

        // $ticket = $trade->getTicket();
        // $newBalance = $wallet->balance - $amount;

        // $transaction = Transaction::create([
        //     'user_id' => Auth::id(),
        //     'category' => 'trading_account',
        //     'transaction_type' => 'fund_in',
        //     'from_wallet_id' => $wallet->id,
        //     'to_meta_login' => $tradingAccount->meta_login,
        //     'transaction_number' => RunningNumberService::getID('transaction'),
        //     'amount' => $amount,
        //     'transaction_charges' => 0,
        //     'transaction_amount' => $amount,
        //     'old_wallet_amount' => $wallet->balance,
        //     'new_wallet_amount' => $newBalance,
        //     'status' => 'processing',
        //     'ticket' => $ticket,
        // ]);

        // $wallet->balance = $newBalance;
        // $wallet->save();

        // // Check if the account exists
        // if ($tradingAccount) {    
        //     // Redirect back with success message
        //     return back()->with('toast', [
        //         'title' => trans('public.toast_revoke_account_success'),
        //         'type' => 'success',
        //     ]);
        // }

        $transactionData = [
            'user_id' => 1,
            'transaction_number' => 'TX1234567890',
            'from_meta_login' => '123456',
            'transaction_amount' => 1000.00,
            'amount' => 1000.00,
            'receiving_address' => 'dummy_address',
            'created_at' => '2024-07-27 16:09:45',
        ];

        // Set notification data in the session
        return redirect()->back()->with('notification', [
            'details' => $transactionData,
            'type' => 'deposit',
        ]);

    }

    //this only sent request to admin
    public function withdrawal_from_account(Request $request)
    {
        // $request->validate([
        //     'account_id' => 'required|exists:trading_accounts,id',
        //     'amount' => 'required|numeric|gt:0',
        //     'receiving_wallet' => 'required'    
        // ]);
    
        // $conn = (new CTraderService)->connectionStatus();
        // if ($conn['code'] != 0) {
        //     return back()
        //         ->with('toast', [
        //             'title' => 'Connection Error',
        //             'type' => 'error'
        //         ]);
        // }

        // $tradingAccount = TradingAccount::find($request->account_id);
        // (new CTraderService)->getUserInfo(collect($tradingAccount));

        // $tradingAccount = TradingAccount::find($request->account_id);
        // $amount = $request->input('amount');
        // $receiving_wallet = $request->input('receiving_wallet');

        // $paymentAccount = PaymentAccount::where('receiving_wallet', $receiving_wallet)->first();

        // if ($tradingAccount->balance < $amount) {
        //     throw ValidationException::withMessages(['wallet' => trans('public.insufficient_balance')]);
        // }

        // try {
        //     $trade = (new CTraderService)->createTrade($tradingAccount->meta_login, $amount, $tradingAccount->account_type_id, "Withdraw From Account", ChangeTraderBalanceType::WITHDRAW);
        // } catch (\Throwable $e) {
        //     if ($e->getMessage() == "Not found") {
        //         TradingUser::firstWhere('meta_login', $tradingAccount->meta_login)->update(['acc_status' => 'Inactive']);
        //     } else {
        //         Log::error($e->getMessage());
        //     }
        //     return response()->json(['success' => false, 'message' => $e->getMessage()]);
        // }

        // $ticket = $trade->getTicket();
        // Transaction::create([
        //     'user_id' => Auth::id(),
        //     'category' => 'trading_account',
        //     'transaction_type' => 'fund_out',
        //     'from_meta_login' => $tradingAccount->meta_login,
        //     'ticket' => $ticket,
        //     'transaction_number' => RunningNumberService::getID('transaction'),
        //     'amount' => $amount,
        //     'transaction_charges' => 0,
        //     'transaction_amount' => $amount,
        //     'status' => 'successful',
        // ]);

        // // $new_balance = $wallet->balance + $amount;
        // $transaction = Transaction::create([
        //     'user_id' => Auth::id(),
        //     'category' => 'wallet',
        //     'transaction_type' => 'withdrawal_from_account',
        //     'payment_account_id' => $paymentAccount->id,
        //     'from_meta_login' => $tradingAccount->meta_login,
        //     'transaction_number' => RunningNumberService::getID('transaction'),
        //     'amount' => $amount,
        //     'transaction_charges' => 0,
        //     'transaction_amount' => $amount,
        //     'status' => 'processing',
        // ]);

        // // Fetch the payment account's account number
        // $paymentAccount = PaymentAccount::find($transaction->payment_account_id);

        // $transactionData = [
        //     'user_id' => Auth::id(),
        //     'transaction_number' => $transaction->transaction_number,
        //     'from_meta_login' => $transaction->from_meta_login,
        //     'transaction_amount' => $transaction->transaction_amount,
        //     'amount' => $transaction->amount,
        //     'receiving_address' => $paymentAccount->account_no,
        //     'created_at =>  $transaction->created_at,
        // ];
        
        $transactionData = [
            'user_id' => 1,
            'transaction_number' => 'TX1234567890',
            'from_meta_login' => '123456',
            'transaction_amount' => 1000.00,
            'amount' => 1000.00,
            'receiving_address' => 'dummy_address',
            'created_at' => '2024-07-27 16:09:45',
        ];

        // Set notification data in the session
        return redirect()->back()->with('notification', [
            'details' => $transactionData,
            'type' => 'withdrawal',
            // 'withdrawal_type' => 'rebate' this not put show meta_login put rebate show Rebate put bonus show Bonus
        ]);
    }

    public function internal_transfer(Request $request)
    {
        // $request->validate([
        //     'account_id' => 'required|exists:trading_accounts,id',
        // ]);
    
        // $conn = (new CTraderService)->connectionStatus();
        // if ($conn['code'] != 0) {
        //     return back()
        //         ->with('toast', [
        //             'title' => 'Connection Error',
        //             'type' => 'error'
        //         ]);
        // }

        // $tradingAccount = TradingAccount::find($request->account_id);
        // (new CTraderService)->getUserInfo(collect($tradingAccount));

        // $tradingAccount = TradingAccount::find($request->account_id);
        // $amount = $request->input('amount');
        // $to_meta_login = $request->input('to_meta_login');

        // if ($tradingAccount->balance < $amount) {
        //     throw ValidationException::withMessages(['wallet' => trans('public.insufficient_balance')]);
        // }

        // try {
        //     $tradeFrom = (new CTraderService)->createTrade($tradingAccount->meta_login, $amount, $tradingAccount->account_type_id, "Withdraw From Account", ChangeTraderBalanceType::WITHDRAW);
        //     $tradeTo = (new CTraderService)->createTrade($tradingAccount->meta_login, $amount, $tradingAccount->account_type_id, "Deposit To Account", ChangeTraderBalanceType::DEPOSIT);
        // } catch (\Throwable $e) {
        //     if ($e->getMessage() == "Not found") {
        //         TradingUser::firstWhere('meta_login', $tradingAccount->meta_login)->update(['acc_status' => 'Inactive']);
        //     } else {
        //         Log::error($e->getMessage());
        //     }
        //     return response()->json(['success' => false, 'message' => $e->getMessage()]);
        // }

        // $ticketFrom = $tradeFrom->getTicket();
        // $ticketTo = $tradeTo->getTicket();
        // Transaction::create([
        //     'user_id' => Auth::id(),
        //     'category' => 'trading_account',
        //     'transaction_type' => 'internal_transfer',
        //     'from_meta_login' => $tradingAccount->meta_login,
        //     'ticket' => $ticketFrom,
        //     'transaction_number' => RunningNumberService::getID('transaction'),
        //     'amount' => $amount,
        //     'transaction_charges' => 0,
        //     'transaction_amount' => $amount,
        //     'status' => 'successful',
        // ]);
    
        // Transaction::create([
        //     'user_id' => Auth::id(),
        //     'category' => 'trading_account',
        //     'transaction_type' => 'internal_transfer',
        //     'to_meta_login' => $to_meta_login,
        //     'ticket' => $ticketTo,
        //     'transaction_number' => RunningNumberService::getID('transaction'),
        //     'amount' => $amount,
        //     'transaction_charges' => 0,
        //     'transaction_amount' => $amount,
        //     'status' => 'successful',
        // ]);

        // Redirect back with success message
        return back()->with('toast', [
            'title' => trans('public.toast_internal_transfer_success'),
            'type' => 'success',
        ]);
    }

    public function change_leverage(Request $request)
    {
        $request->validate([
            'account_id' => 'required',
        ]);
    
        $account = TradingAccount::find($request->account_id);
    
        // Check if the account exists
        if ($account) {    
            // Redirect back with success message
            return back()->with('toast', [
                'title' => trans('public.toast_change_leverage_success'),
                'type' => 'success',
            ]);
        }
    }

    public function revoke_account(Request $request)
    {
        $request->validate([
            'account_id' => 'required|exists:trading_accounts,id',
        ]);
    
        $account = TradingAccount::find($request->account_id);
    
        // Check if the account exists
        if ($account) {    
            // Redirect back with success message
            return back()->with('toast', [
                'title' => trans('public.toast_revoke_account_success'),
                'type' => 'success',
            ]);
        }
    }

    public function delete_account(Request $request)
    {
        $request->validate([
            'account_id' => 'required|exists:trading_accounts,id',
            'type' => 'nullable|string'
        ]);
    
        $account = TradingAccount::find($request->account_id);
    
        // Check if the account exists
        if ($account) {
            // Delete the account
            // $account->delete();

            // Determine the success message based on the type parameter
            $successTitle = trans('public.toast_delete_account_success');
            if ($request->type === 'demo') {
                $successTitle = trans('public.toast_delete_demo_account_success');
            }
    
            // Redirect back with the success message
            return back()->with('toast', [
                'title' => $successTitle,
                'type' => 'success',
            ]);
        }
    }
}
