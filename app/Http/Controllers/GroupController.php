<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\GroupRequest;
use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    private function findOrFailGroup(int $id, int $userId, bool $definitelyAdmin = false): object
    {
        $group = Group::whereHas('users', function ($query) use ($userId) {
            $query->userId($userId);
        })
            ->with(['groupUsers' => function ($query) use ($userId) {
                $query->userId($userId)->admin();
            }])
            ->findOrFail($id);
        $isAdmin = $group->groupUsers->isNotEmpty();
        if ($definitelyAdmin && !$isAdmin) {
            return response()->json([
                'message' => 'Access denied!'
            ], 404);
        }
        return (object)compact('group', 'isAdmin');
    }

    public function getGroupsForTasks(): JsonResponse
    {
        $userId = Auth::user()->id;
        $groups = Group::select('id', 'name')
            ->whereHas('users', function ($query) use ($userId) {
                $query->userId($userId);
            })
            ->orderBy('name')
            ->get();
        return response()->json($groups);
    }

    private function getGroupsForGroupsBuilder($userId): Builder
    {
        return Group::select('id', 'name', 'description', 'created_at as dateTime')
            ->whereHas('users', function ($query) use ($userId) {
                $query->userId($userId);
            })
            ->with(['groupUsers' => function ($query) use ($userId) {
                $query->userId($userId)->admin();
            }])
            ->orderBy('name');
    }

    public function getGroupsForGroups(Request $request)
    {
        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 15);
        if ($perPage > 100) $perPage = 100;

        $userId = Auth::user()->id;

        $data = $this->getGroupsForGroupsBuilder($userId)
            ->forPage($page, $perPage)
            ->get()
            ->each(function ($group) {
                $group->canEdit = $group->groupUsers->isNotEmpty();
                unset($group->groupUsers);
            });

        $total = $this->getGroupsForGroupsBuilder()->count();

        return compact('data', 'total');
    }

    public function getUsers(int $id): JsonResponse
    {
        $userId = Auth::user()->id;

        $this->findOrFailGroup($id, $userId);

        $users = User::select('id', 'name', 'email')
            ->notUserId($userId)
            ->whereHas('groups', function ($query) use ($id) {
                $query->where('id', $id);
            })
            ->with(['groupUsers' => function ($query) use ($id) {
                $query->where('group_id', $id);
            }])
            ->orderBy('name')
            ->get()
            ->each(function ($user) {
                $user->isAdmin = $user->groupUsers->isNotEmpty() && $user->groupUsers[0]->admin;
                unset($user->groupUsers);
            });
        return response()->json($users);
    }

    public function create(GroupRequest $request): Response
    {
        $group = Group::create($request->only('name', 'description'));
        $group->users()->attach(Auth::user()->id, ['admin' => 1]);
        return response()->noContent();
    }

    public function addUser(Request $request, int $id): Response
    {
        $userId = Auth::user()->id;

        $groupInfo = $this->findOrFailGroup($id, $userId, true);
        $userId = User::where('email', $request->email)->firstOrFail();
        $groupInfo->group->users()->syncWithoutDetaching($userId);
        return response()->noContent();
    }

    public function deleteUser(int $groupId, int $userId): Response
    {
        $groupInfo = $this->findOrFailGroup($groupId, $userId, true);
        $groupInfo->group->users()->detach($userId);
        return response()->noContent();
    }

    public function update(GroupRequest $request, int $id): Response
    {
        $userId = Auth::user()->id;

        $groupInfo = $this->findOrFailGroup($id, $userId, true);
        $groupInfo->group->update($request->only('name', 'description'));
        return response()->noContent();
    }

    public function destroy(int $id): Response
    {
        $userId = Auth::user()->id;

        $groupInfo = $this->findOrFailGroup($id, $userId, true);
        $groupInfo->group->delete();
        return response()->noContent();
    }
}
