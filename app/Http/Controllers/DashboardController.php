<?php

namespace App\Http\Controllers;

use App\Models\Term;
use App\Models\Transaction;
use App\Models\Wallet;
use Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $terms = Term::where('slug', 'terms-and-conditions')->get();

        $structuredTerms = [];

        foreach ($terms as $term) {
            $locale = $term->locale;
            $structuredTerms[$locale] = [
                'title' => $term->title,
                'contents' => $term->contents,
            ];
        }

        return Inertia::render('Dashboard/Dashboard', [
            'terms' => $structuredTerms,
        ]);
    }

    public function getDashboardData()
    {
        $user = Auth::user();
        $groupIds = $user->getChildrenIds();
        $groupIds[] = $user->id;

        $rebate_wallet = $user->rebate_wallet;

        $group_total_deposit = Transaction::where('transaction_type', 'deposit')
            ->where('status', 'successful')
            ->whereIn('user_id', $groupIds)
            ->sum('transaction_amount');

        $group_total_withdrawal = Transaction::where('transaction_type', 'withdrawal')
            ->where('status', 'successful')
            ->whereIn('user_id', $groupIds)
            ->sum('amount');

        return response()->json([
            'rebateWallet' => $rebate_wallet,
            'groupTotalDeposit' => $group_total_deposit,
            'groupTotalWithdrawal' => $group_total_withdrawal,
            'totalGroupNetBalance' => $group_total_deposit - $group_total_withdrawal,
        ]);
    }
}
