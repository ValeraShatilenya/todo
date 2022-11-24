<?php

namespace App\Repositories;

use App\Models\Group;

class GroupRepository extends CoreRepository
{
    private $groups = [];

    public function hasAccessToGroup(int $id, int $userId)
    {
        return $this->startConditions()
            ->whereHas('users', function ($query) use ($userId) {
                $query->userId($userId);
            })
            ->where('id', $id)
            ->exists();
    }

    public function getGroupById(int $id, int $userId)
    {
        if (array_key_exists($id, $this->groups)) {
            return $this->groups[$id];
        }
        $group = $this->startConditions()
            ->whereHas('users', function ($query) use ($userId) {
                $query->userId($userId);
            })
            ->with(['groupUsers' => function ($query) use ($userId) {
                $query->userId($userId)->admin();
            }])
            ->findOrFail($id);
        $group->isAdmin = $group->groupUsers->isNotEmpty();
        $this->groups[$id] = $group;
        return $group;
    }

    /**
     * @return mixed
     */
    protected function getModelClass()
    {
        return Group::class;
    }
}
