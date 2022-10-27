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
    /**
     * Find group or fail
     */
    private function findOrFailGroup(int $id, bool $definitelyAdmin = false): object
    {
        $group = Group::whereHas('users', function ($query) {
            $query->currentUser();
        })
            ->with(['groupUsers' => function ($query) {
                $query->currentUser()->admin();
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

    /**
     * Get all group current user.
     *
     */
    public function getGroupsForTasks(): JsonResponse
    {
        $groups = Group::select('id', 'name')
            ->whereHas('users', function ($query) {
                $query->currentUser();
            })
            ->orderBy('name')
            ->get();
        return response()->json($groups);
    }

    /**
     * Get all group current user builder.
     *
     */
    private function getGroupsForGroupsBuilder(): Builder
    {
        return Group::select('id', 'name', 'description', 'created_at as dateTime')
            ->whereHas('users', function ($query) {
                $query->currentUser();
            })
            ->with(['groupUsers' => function ($query) {
                $query->currentUser()->admin();
            }])
            ->orderBy('name');
    }

    /**
     * Get all group current user.
     *
     */
    public function getGroupsForGroups(Request $request)
    {
        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 15);
        if ($perPage > 100) $perPage = 100;

        $data = $this->getGroupsForGroupsBuilder()
            ->forPage($page, $perPage)
            ->get()
            ->each(function ($group) {
                $group->canEdit = $group->groupUsers->isNotEmpty();
                unset($group->groupUsers);
            });

        $total = $this->getGroupsForGroupsBuilder()->count();

        return compact('data', 'total');
    }

    /**
     * Get all group current user.
     *
     */
    public function getUsers(int $id): JsonResponse
    {
        $this->findOrFailGroup($id);

        $users = User::select('id', 'name', 'email')
            ->notCurrentUser()
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

    /**
     * Create the specified group in storage.
     *
     */
    public function create(GroupRequest $request): Response
    {
        $group = Group::create($request->only('name', 'description'));
        $group->users()->attach(Auth::user()->id, ['admin' => 1]);
        return response()->noContent();
    }

    /**
     * Add user to group
     *
     */
    public function addUser(Request $request, int $id): Response
    {
        $groupInfo = $this->findOrFailGroup($id, true);
        $userId = User::where('email', $request->email)->firstOrFail();
        $groupInfo->group->users()->syncWithoutDetaching($userId);
        return response()->noContent();
    }

    /**
     * Delete user from group
     *
     */
    public function deleteUser(int $groupId, int $userId): Response
    {
        $groupInfo = $this->findOrFailGroup($groupId, true);
        $groupInfo->group->users()->detach($userId);
        return response()->noContent();
    }

    /**
     * Update the specified group in storage.
     *
     */
    public function update(GroupRequest $request, int $id): Response
    {
        $groupInfo = $this->findOrFailGroup($id, true);
        $groupInfo->group->update($request->only('name', 'description'));
        return response()->noContent();
    }

    /**
     * Destroy the specified group from storage.
     *
     */
    public function destroy(int $id): Response
    {
        $groupInfo = $this->findOrFailGroup($id, true);
        $groupInfo->group->delete();
        return response()->noContent();
    }
}
