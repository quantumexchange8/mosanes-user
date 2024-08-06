<?php

namespace App\Http\Controllers;

use App\Models\RebateAllocation;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;
use PhpParser\PrettyPrinter\Standard;

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

    public function getAgents()
    {
        //level 1 children
        $lv1_agents = User::find(Auth::id())->directChildren()->where('role', 'agent')
            ->get()->map(function($agent) {
                return [
                    'id' => $agent->id,
                    'profile_photo' => $agent->getFirstMediaUrl('profile_photo'),
                    'name' => $agent->name,
                    'hierarchy_list' => $agent->hierarchyList,
                    'level' => 1,
                ];
            })->toArray();
        
        //level 1 children rebate
        $lv1_rebate = $this->getRebateAllocate($lv1_agents[0]['id'], 1);

        $children_ids = User::find($lv1_agents[0]['id'])->getChildrenIds();
        $agents = User::whereIn('id', $children_ids)->where('role', 'agent')
            ->get()->map(function ($user) {
                return [
                    'id' => $user->id,
                    'profile_photo' => $user->getFirstMediaUrl('profile_photo'),
                    'name' => $user->name,
                    'hierarchy_list' => $user->hierarchyList,
                    'level' => $this->calculateLevel($user->hierarchyList),
                ];
            })
            ->groupBy('level')->toArray();

        $agents_array = [];
        $rebates = [];

        // push lvl 1 first
        // array_push($agents_array, $lv1_agents);
        // array_push($rebates, $lv1_rebate);

        $lv1_data = [];

        // push lvl 1 agent & rebate
        array_push($lv1_data, $lv1_agents, $lv1_rebate);
        array_push($agents_array, $lv1_data);

        // push lvl 2 and above agent & rebate into array 
        for ($i = 2; $i <= sizeof($agents) + 1; $i++) {
            // array_push($agents_array, $agents[$i]);

            // $rebate = $this->getRebateAllocate($agents[$i][0]['id'], 1);
            // array_push($rebates, $rebate);

            $temp = [];
            array_push($temp, $agents[$i]);

            $rebate = $this->getRebateAllocate($agents[$i][0]['id'], 1);
            array_push($temp, $rebate);

            array_push($agents_array, $temp);
        }
// dd($rebates);
// dd($agents_array);

        return response()->json([
            'agents' => $agents_array,
            // 'rebates' => $rebates,
        ]);
    }

    private function calculateLevel($hierarchyList)
    {
        if (is_null($hierarchyList) || $hierarchyList === '') {
            return 0;
        }

        $split = explode('-'.Auth::id().'-', $hierarchyList);
        return substr_count($split[1], '-') + 1;
    }

    private function getRebateAllocate($user_id, $type_id)
    {
        $rebate = User::find($user_id)->rebateAllocations()->where('account_type_id', $type_id)->get();

        $data = [
            'user_id' => $rebate[0]->user_id,
            'forex' => $rebate[0]->amount,
            'stocks' => $rebate[1]->amount,
            'indices' => $rebate[2]->amount,
            'commodities' => $rebate[3]->amount,
            'cryptocurrency' => $rebate[4]->amount,
        ];

        return $data;
    }
}
