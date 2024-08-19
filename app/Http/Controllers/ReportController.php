<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\SymbolGroup;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\RebateAllocation;
use App\Models\TradeRebateSummary;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index()
    {
        return Inertia::render('Report/Report');
    }

    public function getRebateSummary(Request $request)
    {
        $userId = Auth::id();

        // Retrieve date parameters from request
        $startDate = $request->query('startDate');
        $endDate = $request->query('endDate');

        // Initialize query for rebate summary with date filtering
        $query = TradeRebateSummary::with('symbolGroup')
            ->where('upline_user_id', $userId);

        // Apply date filter based on availability of startDate and/or endDate
        if ($startDate && $endDate) {
            // Both startDate and endDate are provided
            $query->whereDate('execute_at', '>=', $startDate)
                ->whereDate('execute_at', '<=', $endDate);
        } else {
            // Both startDate and endDate are null, apply default start date
            $query->whereDate('execute_at', '>=', '2024-01-01');
        }

        // Fetch rebate summary data
        $rebateSummary = $query->get(['symbol_group', 'volume', 'rebate']);

        // Retrieve all symbol groups with non-null display values
        $symbolGroups = SymbolGroup::whereNotNull('display')->pluck('display', 'id');

        // Aggregate rebate data in PHP
        $rebateSummaryData = $rebateSummary->groupBy('symbol_group')->map(function ($items) {
            return [
                'volume' => $items->sum('volume'),
                'rebate' => $items->sum('rebate'),
            ];
        });

        // Initialize final summary and totals
        $finalSummary = [];
        $totalVolume = 0;
        $totalRebate = 0;

        // Iterate over all symbol groups
        foreach ($symbolGroups as $id => $display) {
            // Retrieve data or use default values
            $data = $rebateSummaryData->get($id, ['volume' => 0, 'rebate' => 0]);

            // Add to the final summary
            $finalSummary[] = [
                'symbol_group' => $display,
                'volume' => $data['volume'],
                'rebate' => $data['rebate'],
            ];

            // Accumulate totals
            $totalVolume += $data['volume'];
            $totalRebate += $data['rebate'];
        }

        // Return the response with rebate summary, total volume, and total rebate
        return response()->json([
            'rebateSummary' => $finalSummary,
            'totalVolume' => $totalVolume,
            'totalRebate' => $totalRebate,
        ]);
    }

    public function getRebateListing(Request $request)
    {
        // Retrieve query parameters
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');

        // Fetch all symbol groups from the database, ordered by the primary key (id)
        $allSymbolGroups = SymbolGroup::pluck('display', 'id')->toArray();
        
        $query = TradeRebateSummary::with('user')
            ->where('upline_user_id', Auth::id());

        // Apply date filter based on availability of startDate and/or endDate
        if ($startDate && $endDate) {
            // Both startDate and endDate are provided
            $query->whereDate('execute_at', '>=', $startDate)
                ->whereDate('execute_at', '<=', $endDate);
        } else {
            // Both startDate and endDate are null, apply default start date
            $query->whereDate('execute_at', '>=', '2024-01-01');
        }
        
        // Fetch rebate listing data
        $data = $query->get()
            ->map(function ($item) {
                return [
                    'user_id' => $item->user_id,
                    'name' => $item->user->name,
                    'email' => $item->user->email,
                    'meta_login' => $item->meta_login,
                    'execute_at' => Carbon::parse($item->execute_at)->toDateString(),
                    'symbol_group' => $item->symbol_group,
                    'volume' => $item->volume,
                    'rebate' => $item->rebate,
                ];
            });
        
        // Generate rebateListing and details
        $rebateListing = $data->groupBy(function ($item) {
            return $item['execute_at'] . '-' . $item['user_id'] . '-' . $item['meta_login'] ;
        })->map(function ($group) use ($allSymbolGroups) {
            $group = collect($group);
            $userId = $group->first()['user_id']; // Fetch the user_id from the group
            
            // Fetch rebate allocation data for all symbol groups for the given user
            $rebateAllocations = RebateAllocation::where('user_id', $userId)
                ->get()
                ->keyBy('symbol_group_id');

            // Generate detailed data for this rebateListing item
            $symbolGroupDetails = $group->groupBy('symbol_group')->map(function ($symbolGroupItems) use ($allSymbolGroups, $rebateAllocations) {
                $symbolGroupId = $symbolGroupItems->first()['symbol_group'];
                
                // Fetch rebate allocation data from pre-fetched data
                $rebateAllocation = $rebateAllocations->get($symbolGroupId);
                
                return [
                    'id' => $symbolGroupId,
                    'name' => $allSymbolGroups[$symbolGroupId] ?? 'Unknown',
                    'volume' => $symbolGroupItems->sum('volume'),
                    'rebate' => $symbolGroupItems->sum('rebate'),
                    'rebate_allocation' => $rebateAllocation ? $rebateAllocation->amount : 0,
                ];
            })->values();
        
            // Add missing symbol groups with volume, rebate, and allocation as 0
            foreach ($allSymbolGroups as $symbolGroupId => $symbolGroupName) {
                if (!$symbolGroupDetails->pluck('id')->contains($symbolGroupId)) {
                    // Fetch rebate allocation data for missing symbol group from pre-fetched data
                    $rebateAllocation = $rebateAllocations->get($symbolGroupId);
                    
                    $symbolGroupDetails->push([
                        'id' => $symbolGroupId,
                        'name' => $symbolGroupName,
                        'volume' => 0,
                        'rebate' => 0,
                        'rebate_allocation' => $rebateAllocation ? $rebateAllocation->amount : 0,
                    ]);
                }
            }
        
            // Sort the symbol group details array to match the order of symbol groups
            $symbolGroupDetails = $symbolGroupDetails->sortBy('id')->values();
        
            // Return rebateListing item with details included
            return [
                'user_id' => $group->first()['user_id'],
                'name' => $group->first()['name'],
                'email' => $group->first()['email'],
                'meta_login' => $group->first()['meta_login'],
                'execute_at' => $group->first()['execute_at'],
                'volume' => $group->sum('volume'),
                'rebate' => $group->sum('rebate'),
                'details' => $symbolGroupDetails,
            ];
        })->values();
        
        // Sort rebateListing by execute_at in descending order to get the latest dates first
        $rebateListing = $rebateListing->sortByDesc('execute_at');

        // Return JSON response with combined rebateListing and details
        return response()->json([
            'rebateListing' => $rebateListing
        ]);
    }

    public function getGroupTransaction(Request $request)
    {
        $user = Auth::user();
        $groupIds = $user->getChildrenIds();
        $groupIds[] = $user->id;
    
        $transactionType = $request->query('type');
        $startDate = $request->query('startDate');
        $endDate = $request->query('endDate');
    
        // Initialize the query for transactions
        $query = Transaction::where('transaction_type', $transactionType)
            ->where('status', 'successful')
            ->whereIn('user_id', $groupIds);
    
        // Apply date filter based on availability of startDate and/or endDate
        if ($startDate && $endDate) {
            $query->whereDate('created_at', '>=', $startDate)
                  ->whereDate('created_at', '<=', $endDate);
        } else {
            // Handle cases where startDate or endDate are not provided
            $query->whereDate('created_at', '>=', '2024-01-01'); // Default start date
        }
    
        $transactions = $query->get()
            ->map(function ($transaction) {
                return [
                    'created_at' => $transaction->created_at,
                    'user_id' => $transaction->user_id,
                    'name' => $transaction->user->name,
                    'email' => $transaction->user->email,
                    'meta_login' => $transaction->to_meta_login ?: $transaction->from_meta_login,
                    'transaction_amount' => $transaction->transaction_amount
                ];
            });
    
        // Calculate total deposit and withdrawal amounts for the given date range
        $group_total_deposit = Transaction::where('transaction_type', 'deposit')
            ->where('status', 'successful')
            ->whereIn('user_id', $groupIds)
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereDate('created_at', '>=', $startDate)
                      ->whereDate('created_at', '<=', $endDate);
            })
            ->sum('transaction_amount');
    
        $group_total_withdrawal = Transaction::where('transaction_type', 'withdrawal')
            ->where('status', 'successful')
            ->whereIn('user_id', $groupIds)
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereDate('created_at', '>=', $startDate)
                      ->whereDate('created_at', '<=', $endDate);
            })
            ->sum('transaction_amount');
    
        return response()->json([
            'transactions' => $transactions,
            'groupTotalDeposit' => $group_total_deposit,
            'groupTotalWithdrawal' => $group_total_withdrawal,
            'groupTotalNetBalance' => $group_total_deposit - $group_total_withdrawal,
        ]);
    }
}
