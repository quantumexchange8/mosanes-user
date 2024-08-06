<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
}
