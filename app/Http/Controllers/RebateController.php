<?php

namespace App\Http\Controllers;

use App\Models\RebateAllocation;
use Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RebateController extends Controller
{
    public function index()
    {
        return Inertia::render('RebateAllocate/RebateAllocate');
    }

    public function getRebateAllocateData(Request $request)
    {
        $rebate_data = RebateAllocation::with('symbol_group:id,display')
            ->where('user_id', Auth::id())
            ->where('account_type_id', $request->account_type_id)
            ->get();

        return response()->json([
            'rebate_data' => $rebate_data
        ]);
    }
}
