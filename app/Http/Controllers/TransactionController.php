<?php

namespace App\Http\Controllers;

use App\Models\PaymentAccount;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\RunningNumberService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class TransactionController extends Controller
{
    public function index()
    {
        return Inertia::render('Transaction/Transaction');
    }

    public function getTotal()
    {
        $total_deposit = Auth::user()->transactions->where('transaction_type', 'deposit')->sum('transaction_amount');
        $total_withdrawal = Auth::user()->transactions->where('transaction_type', 'withdrawal')->sum('transaction_amount');

        return response()->json([
            'totalDeposit' => $total_deposit,
            'totalWithdrawal' => $total_withdrawal,
        ]);
    }

    public function getTransactions()
    {
        $transactions = Transaction::where('category', 'trading_account')
            ->where('transaction_type', 'deposit')
            ->orWhere('transaction_type', 'withdrawal')
            ->get();

        return response()->json([
            'transactions' => $transactions,
        ]);
    }

    public function walletWithdrawal(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'wallet_id' => ['required', 'exists:wallets,id'],
            'amount' => ['required', 'numeric', 'gt:30'],
            'wallet_address' => ['required']
        ])->setAttributeNames([
            'wallet_id' => trans('public.wallet'),
            'amount' => trans('public.amount'),
            'wallet_address' => trans('public.receiving_wallet'),
        ]);
        $validator->validate();

        $user = Auth::user();
        $amount = $request->amount;
        $wallet = Wallet::find($request->wallet_id);
        $paymentWallet = PaymentAccount::where('user_id', Auth::id())
            ->where('account_no', $request->wallet_address)
            ->first();

        if ($wallet->balance < $amount) {
            throw ValidationException::withMessages(['amount' => trans('public.insufficient_balance')]);
        }

        $transaction = Transaction::create([
            'user_id' => $user->id,
            'category' => 'rebate_wallet',
            'transaction_type' => 'withdrawal',
            'from_wallet_id' => $wallet->id,
            'transaction_number' => RunningNumberService::getID('transaction'),
            'payment_account_id' => $paymentWallet->id,
            'to_wallet_address' => $paymentWallet->account_no,
            'amount' => $amount,
            'transaction_charges' => 0,
            'transaction_amount' => $amount,
            'old_wallet_amount' => $wallet->balance,
            'new_wallet_amount' => $wallet->balance -= $amount,
            'status' => 'processing',
        ]);

        $wallet->save();

        return redirect()->back()->with('notification', [
            'details' => $transaction,
            'type' => 'withdrawal',
             'withdrawal_type' => 'rebate'
        ]);
    }
}
