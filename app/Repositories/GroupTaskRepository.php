<?php

namespace App\Repositories;

use App\Dto\ChangeCompletedTaskDto;
use App\Dto\CreateGroupTaskDto;
use App\Dto\UpdateTaskDto;
use App\Models\GroupTask;
use Illuminate\Database\Eloquent\Builder;

class GroupTaskRepository extends CoreRepository implements TaskInterface
{
    public function getById(int $id)
    {
        return $this->startConditions()->findOrFail($id);
    }

    public function getNotCompletedById(int $id)
    {
        return $this->startConditions()->notCompleted()->findOrFail($id);
    }

    public function getNotCompletedData(int $groupId, string $sort = 'dateTime', int $page = null, int $perPage = null)
    {
        $builder = $this->getNotCompletedBuilder($groupId, $sort);
        if ($page && $perPage) {
            $builder->forPage($page, $perPage);
        }
        return $builder->get()->toArray();
    }

    public function getCompletedData(int $groupId, string $sort = 'dateTime', int $page = null, int $perPage = null)
    {
        $builder = $this->getCompletedBuilder($groupId, $sort);
        if ($page && $perPage) {
            $builder->forPage($page, $perPage);
        }
        return $builder->get()->toArray();
    }

    public function getNotCompletedCount(int $groupId)
    {
        return $this->getNotCompletedBuilder($groupId)->count();
    }

    public function getCompletedCount(int $groupId)
    {
        return $this->getCompletedBuilder($groupId)->count();
    }

    public function changeCompleted(GroupTask $groupTask, ChangeCompletedTaskDto $data)
    {
        if ($data->type && !$groupTask->completed) {
            $groupTask->completed = now();
            $groupTask->completed_user_id = $data->userId;
            $groupTask->save();
        } else if (!$data->type && $groupTask->completed) {
            $groupTask->completed = null;
            $groupTask->completed_user_id = null;
            $groupTask->save();
        }
    }

    public function create(CreateGroupTaskDto $data): GroupTask
    {
        return $this->startConditions()
            ->create([
                'group_id' => $data->groupId,
                'user_id' => $data->userId,
                'title' => $data->title,
                'description' => $data->description,
                'status' => $data->status
            ]);
    }

    public function update(UpdateTaskDto $data)
    {
        $this->startConditions()
            ->where('id', $data->id)
            ->update([
                'title' => $data->title,
                'description' => $data->description,
                'status' => $data->status
            ]);
    }

    public function destroy(int $id)
    {
        $this->startConditions()->destroy($id);
    }

    private function getNotCompletedBuilder(int $groupId = null, string $sort = 'dateTime'): Builder
    {
        $builder = $this->startConditions()
            ->select('id', 'user_id', 'title', 'description', 'status', 'created_at as dateTime')
            ->where('group_id', $groupId)
            ->with(['user' => function ($query) {
                $query->select('id', 'name');
            }])
            ->with('files')
            ->notCompleted();
        $builder = $this->addSort($builder, $sort);
        return $builder;
    }

    private function getCompletedBuilder(int $groupId = null, string $sort = 'dateTime'): Builder
    {
        $builder = $this->startConditions()
            ->select('id', 'user_id', 'completed_user_id', 'title', 'description', 'status', 'completed as dateTime')
            ->where('group_id', $groupId)
            ->with(['completedUser' => function ($query) {
                $query->select('id', 'name');
            }])
            ->with(['user' => function ($query) {
                $query->select('id', 'name');
            }])
            ->with('files')
            ->completed();
        $builder = $this->addSort($builder, $sort);
        return $builder;
    }

    /**
     * Add sort to builder
     */
    private function addSort(Builder $builder, string $sort = 'dateTime'): Builder
    {
        if ($sort === 'dateTime') $builder->orderByDesc('dateTime');
        else if ($sort === 'status') {
            $builder->orderBy('status')
                ->orderByDesc('dateTime');
        }
        return $builder;
    }

    /**
     * @return mixed
     */
    protected function getModelClass()
    {
        return GroupTask::class;
    }
}
