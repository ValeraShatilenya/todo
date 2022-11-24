<?php

namespace App\Repositories;

use App\Dto\CreateTaskDto;
use App\Dto\UpdateTaskDto;
use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;

class TaskRepository extends CoreRepository implements TaskInterface
{
    public function getById(int $id, int $userId)
    {
        return $this->startConditions()->userId($userId)->findOrFail($id);
    }

    public function getNotCompletedData(int $userId, string $sort = 'dateTime', int $page = 1, int $perPage = 15)
    {
        $builder = $this->getNotCompletedBuilder($userId, $sort);
        if ($page && $perPage) {
            $builder->forPage($page, $perPage);
        }
        return $builder->get()->toArray();
    }

    public function getCompletedData(int $userId, string $sort = 'dateTime', int $page = 1, int $perPage = 15)
    {
        $builder = $this->getCompletedBuilder($userId, $sort);
        if ($page && $perPage) {
            $builder->forPage($page, $perPage);
        }
        return $builder->get()->toArray();
    }

    public function getNotCompletedCount(int $userId)
    {
        return $this->getNotCompletedBuilder($userId)->count();
    }

    public function getCompletedCount(int $userId)
    {
        return $this->getCompletedBuilder($userId)->count();
    }

    /**
     * Change completed date the specified task.
     */
    public function changeCompleted(int $id, int $userId, bool $type)
    {
        $task = $this->getById($id, $userId);

        if ($type && !$task->completed) {
            $task->completed = now();
            $task->save();
        } else if (!$type && $task->completed) {
            $task->completed = null;
            $task->save();
        }
    }

    public function create(CreateTaskDto $data): Task
    {
        return $this->startConditions()
            ->create([
                'user_id' => $data->userId,
                'title' => $data->title,
                'description' => $data->description,
                'status' => $data->status
            ]);
    }

    public function update(UpdateTaskDto $data): Task
    {
        $task = $this->startConditions()
            ->userId($data->userId)
            ->notCompleted()
            ->findOrFail($data->id);

        $task->update([
            'title' => $data->title,
            'description' => $data->description,
            'status' => $data->status
        ]);

        return $task->fresh();
    }

    public function destroy(int $id)
    {
        $this->startConditions()->destroy($id);
    }

    /**
     * Get Not Completed tasks Builder.
     */
    private function getNotCompletedBuilder(int $userId, string $sort = 'dateTime'): Builder
    {
        $builder = $this->startConditions()
            ->select('id', 'title', 'description', 'status', 'created_at as dateTime')
            ->with('files')
            ->notCompleted()
            ->userId($userId);
        $builder = $this->addSort($builder, $sort);
        return $builder;
    }

    /**
     * Get Not Completed tasks Builder.
     */
    private function getCompletedBuilder(int $userId, string $sort = 'dateTime'): Builder
    {
        $builder = $this->startConditions()
            ->select('id', 'title', 'description', 'status', 'completed as dateTime')
            ->with('files')
            ->completed()
            ->userId($userId);
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
        return Task::class;
    }
}
