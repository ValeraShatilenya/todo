<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskRequest;
use App\Jobs\SendPDFJob;
use App\Models\Task;
use App\Models\File as ModelsFile;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller implements TaskInterface
{
    /**
     * Get Not Completed tasks Builder.
     */
    private function getNotCompletedBuilder(int $userId = null, string $sort = 'dateTime'): Builder
    {
        $userId = $userId ?? Auth::user()->id;
        $builder = Task::select('id', 'title', 'description', 'status', 'created_at as dateTime')
            ->with(['files' => function ($query) {
                $query->select('id', 'name', 'task_id');
            }])
            ->notCompleted()
            ->userId($userId);
        if ($sort === 'dateTime') $builder->orderByDesc('dateTime');
        else if ($sort === 'status') {
            $builder->orderBy('status')
                ->orderByDesc('dateTime');
        }
        return $builder;
    }

    /**
     * Get Not Completed tasks data.
     */
    public function getNotCompletedData(int $userId = null, int $page = null, int $perPage = null, string $sort = 'dateTime')
    {
        $builder = $this->getNotCompletedBuilder($userId, $sort);
        if ($page && $perPage) {
            $builder->forPage($page, $perPage);
        }
        return $builder->get()
            ->each(function ($task) {
                foreach ($task->files as $file) {
                    unset($file->task_id);
                }
            });
    }

    /**
     * Get Not Completed tasks.
     */
    public function getNotCompleted(Request $request)
    {
        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 15);
        if ($perPage > 100)  $perPage = 100;
        $sort = $request->input('sort', 'dateTime');
        return ['data' => $this->getNotCompletedData(null, $page, $perPage, $sort), 'total' => $this->getNotCompletedBuilder(null)->count()];
    }

    /**
     * Get Not Completed tasks Builder.
     */
    private function getCompletedBuilder(int $userId = null, string $sort = 'dateTime'): Builder
    {
        $userId = $userId ?? Auth::user()->id;
        $builder = Task::select('id', 'title', 'description', 'status', 'completed as dateTime')
            ->with(['files' => function ($query) {
                $query->select('id', 'name', 'task_id');
            }])
            ->completed()
            ->userId($userId);
        if ($sort === 'dateTime') $builder->orderByDesc('dateTime');
        else if ($sort === 'status') {
            $builder->orderBy('status')
                ->orderByDesc('dateTime');
        }
        return $builder;
    }

    /**
     * Get Completed tasks data.
     */
    public function getCompletedData(int $userId = null, int $page = null, int $perPage = null, string $sort = 'dateTime')
    {
        $builder = $this->getCompletedBuilder($userId, $sort);
        if ($page && $perPage) {
            $builder->forPage($page, $perPage);
        }
        return $builder->get()
            ->each(function ($task) {
                foreach ($task->files as $file) {
                    unset($file->task_id);
                }
            });
    }

    /**
     * Get Completed tasks.
     */
    public function getCompleted(Request $request)
    {
        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 15);
        if ($perPage > 100)  $perPage = 100;
        $sort = $request->input('sort', 'dateTime');
        return ['data' => $this->getCompletedData(null, $page, $perPage, $sort), 'total' => $this->getCompletedBuilder(null)->count()];
    }

    /**
     * Get file.
     */
    public function downloadTaskFile(int $id)
    {
        $file = ModelsFile::whereHas('task', function ($query) {
            $query->currentUser();
        })
            ->findOrFail($id);
        return Storage::get($file->path);
    }

    /**
     * Create files.
     */
    private function createFiles(Request $request, int $id)
    {
        $indexFile = 1;
        $file = $request->file("file_$indexFile");
        while ($file) {
            $path = Storage::putFile("tasks/$id", $file);
            ModelsFile::create([
                'name' => $file->getClientOriginalName(),
                'path' => $path,
                'task_id' => $id
            ]);
            $indexFile++;
            $file = $request->file("file_$indexFile");
        }
    }

    /**
     * Create the specified task in storage.
     */
    public function create(TaskRequest $request)
    {
        $dataForCreate = $request->only('title', 'status', 'description');
        $dataForCreate['user_id'] = Auth::user()->id;
        $task = Task::create($dataForCreate);

        $this->createFiles($request, $task->id);

        return response()->noContent();
    }

    /**
     * Change completed date the specified task in storage.
     */
    public function changeCompleted(Request $request, int $id)
    {
        $type = $request->type;
        $task = Task::currentUser()->findOrFail($id);
        if ($type && !$task->completed) {
            $task->completed = now();
            $task->save();
        } else if (!$type && $task->completed) {
            $task->completed = null;
            $task->save();
        }
        return response()->noContent();
    }

    /**
     * Update the specified task in storage.
     */
    public function update(TaskRequest $request, int $id)
    {
        $task = Task::currentUser()->findOrFail($id);
        $task->update($request->only('title', 'status', 'description'));
        $oldFiles = $request->oldFiles ? explode(',', $request->oldFiles) : [];

        // delete
        $filesToDelete = ModelsFile::select('id', 'path')
            ->where('task_id', $id)
            ->whereNotIn('id', $oldFiles)
            ->get()
            ->reduce(function ($arr, $file) {
                $arr['id'][] = $file->id;
                $arr['path'][] = $file->path;
                return $arr;
            }, ['id' => [], 'path' => []]);
        Storage::delete($filesToDelete['path']);
        ModelsFile::destroy($filesToDelete['id']);

        // create
        $this->createFiles($request, $id);

        return response()->noContent();
    }

    /**
     * Destroy the specified task from storage.
     */
    public function destroy(int $id)
    {
        $task = Task::currentUser()->findOrFail($id);

        Storage::deleteDirectory("tasks/$id");
        $task->files()->delete();
        $task->delete();

        return response()->noContent();
    }

    /**
     * Send PDF to Mail
     */
    public function sendPdfToMail()
    {
        $user = Auth::user();

        dispatch(new SendPDFJob($user, $this, [$user->id]));
    }
}
