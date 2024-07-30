<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\DropdownOptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class StructureController extends Controller
{
    public function show()
    {
        return Inertia::render('Structure/Structure');
    }

    public function getDownlineData(Request $request)
    {
        $upline_id = $request->upline_id;
        $parent_id = $request->parent_id ?: Auth::id();

        if ($request->filled('search')) {
            $search = '%' . $request->input('search') . '%';
            $parent = User::whereIn('role', ['agent', 'member'])
                ->where('id_number', 'LIKE', $search)
                ->orWhere('email', 'LIKE', $search)
                ->first();

            $parent_id = $parent->id;
            $upline_id = $parent->upline_id;
        }

        $parent = User::with(['directChildren:id,name,id_number,upline_id,role,hierarchyList'])
            ->whereIn('role', ['agent', 'member'])
            ->select('id', 'name', 'id_number', 'upline_id', 'role', 'hierarchyList')
            ->find($parent_id);

        $upline = $upline_id && $upline_id != Auth::user()->upline_id ? User::select('id', 'name', 'id_number', 'upline_id', 'role', 'hierarchyList')->find($upline_id) : null;

        $parent_data = $this->formatUserData($parent);
        $upline_data = $upline ? $this->formatUserData($upline) : null;

        $direct_children = $parent->directChildren->map(function ($child) {
            return $this->formatUserData($child);
        });

        return response()->json([
            'upline' => $upline_data,
            'parent' => $parent_data,
            'direct_children' => $direct_children,
        ]);
    }

    private function formatUserData($user)
    {
        if ($user->upline) {
            $upper_upline = $user->upline->upline;
        }

        return array_merge(
            $user->only(['id', 'name', 'id_number', 'upline_id', 'role']),
            [
                'profile_photo' => $user->getFirstMediaUrl('profile_photo'),
                'upper_upline_id' => $upper_upline->id ?? null,
                'level' => $user->id === Auth::id() ? 0 : $this->calculateLevel($user->hierarchyList),
                'total_agent_count' => $this->getChildrenCount('agent', $user->id),
                'total_member_count' => $this->getChildrenCount('member', $user->id),
            ]
        );
    }

    private function calculateLevel($hierarchyList)
    {
        if (is_null($hierarchyList) || $hierarchyList === '') {
            return 0;
        }

        $split = explode('-'.Auth::id().'-', $hierarchyList);
        return substr_count($split[1], '-') + 1;
    }

    private function getChildrenCount($role, $user_id): int
    {
        return User::where('role', $role)
            ->where('hierarchyList', 'like', '%-' . $user_id . '-%')
            ->count();
    }

    public function getDownlineListingData()
    {
        $children_ids = User::find(Auth::id())->getChildrenIds();
        $query = User::whereIn('id', $children_ids)
            ->get()->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'upline_id' => $user->upline_id,
                    'upline_name' => $user->upline->name,
                    'role' => $user->role,
                    'id_number' => $user->id_number,
                    'joined_date' => $user->created_at,
                    'level' => $this->calculateLevel($user->hierarchyList),
                ];
            });

        return response()->json([
            'users' => $query
        ]);
    }

    public function getFilterData()
    {
        return response()->json([
            'uplines' => (new DropdownOptionService())->getUplines(),
        ]);
    }
}
