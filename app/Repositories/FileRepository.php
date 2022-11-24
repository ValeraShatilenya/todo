<?php

namespace App\Repositories;

use App\Models\File;
use App\Models\GroupTask;
use App\Models\Task;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FileRepository extends CoreRepository
{
    const FOLDERS = [
        Task::class => 'tasks',
        GroupTask::class => 'group_tasks',
    ];

    public function getTaskFiles(Model $task): Collection
    {
        return $task->files;
    }

    public function getFileWithFilableById(int $id)
    {
        return $this->startConditions()
            ->with('filable')
            ->findOrFail($id);
    }

    public function createTaskFiles(Model $task, array $files)
    {
        $folder = self::FOLDERS[get_class($task)];
        foreach ($files as $file) {
            $path = Storage::putFile("$folder/$task->id", $file);
            $task->files()->create([
                'name' => $file->getClientOriginalName(),
                'path' => $path,
            ]);
        }
    }

    public function destroyAllTaskFilesByIdExcept(Collection $files, array $ids)
    {
        $filesToDelete = $files->whereNotIn('id', $ids)->values();
        $this->destroyTaskFiles($filesToDelete);
    }

    public function destroyTaskFiles(Collection $files)
    {
        $paths = $files->pluck('path')->all();
        Storage::delete($paths);
        $this->startConditions()->destroy($files);
    }

    public function destroyAllTaskFiles(Model $task, Collection $files)
    {
        $folder = self::FOLDERS[get_class($task)];
        Storage::deleteDirectory("$folder/$task->id");
        $this->startConditions()->destroy($files->pluck('id'));
    }

    /**
     * @return mixed
     */
    protected function getModelClass()
    {
        return File::class;
    }
}
