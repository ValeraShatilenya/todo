<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Jobs\SendPDFJob;
use App\Models\Group;
use App\Models\GroupTask;
use App\Models\File as ModelsFile;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class GroupTaskController extends Controller implements TaskInterface
{
    private $group = null;

    /**
     * Set group or fail
     */
    private function setGroupOrFail(int $id)
    {
        if ($this->group) return $this->group;
        $group = Group::whereHas('users', function ($query) {
            $query->currentUser();
        })
            ->with(['groupUsers' => function ($query) {
                $query->currentUser()->admin();
            }])
            ->findOrFail($id);
        $group->isAdmin = $group->groupUsers->isNotEmpty();
        $this->group = $group;
        return $this;
    }

    /**
     * Get Not Completed tasks Builder.
     */
    private function getNotCompletedBuilder(int $id = null, int $userId = null): Builder
    {
        $userId = $userId ?? Auth::user()->id;
        return GroupTask::select('id', 'user_id', 'title', 'description', 'created_at as dateTime')
            ->where('group_id', $id)
            ->with(['user' => function ($query) {
                $query->select('id', 'name');
            }])
            ->with(['files' => function ($query) {
                $query->select('id', 'name', 'group_task_id');
            }])
            ->notCompleted()
            ->orderByDesc('created_at');
    }

    /**
     * Get Not Completed tasks data.
     */
    public function getNotCompletedData(int $id = null, int $userId = null, int $page = null, int $perPage = null)
    {
        $this->setGroupOrFail($id);
        $builder = $this->getNotCompletedBuilder($id, $userId);
        if ($page && $perPage) {
            $builder->forPage($page, $perPage);
        }
        return $builder->get()
            ->each(function ($task) use ($userId) {
                $task->canEdit = $this->group->isAdmin || $task->user_id === $userId;
                unset($task->user_id);
                foreach ($task->files as $file) {
                    unset($file->group_task_id);
                }
            });
    }

    /**
     * Get Not Completed tasks.
     */
    public function getNotCompleted(Request $request, int $id = null)
    {
        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 15);
        if ($perPage > 100)  $perPage = 100;
        return ['data' => $this->getNotCompletedData($id, null, $page, $perPage), 'total' => $this->getNotCompletedBuilder($id, null)->count()];
    }

    /**
     * Get Not Completed tasks Builder.
     */
    private function getCompletedBuilder(int $id, int $userId = null): Builder
    {
        $userId = $userId ?? Auth::user()->id;
        return GroupTask::select('id', 'user_id', 'completed_user_id', 'title', 'description', 'updated_at as dateTime')
            ->where('group_id', $id)
            ->with(['completedUser' => function ($query) {
                $query->select('id', 'name');
            }])
            ->with(['user' => function ($query) {
                $query->select('id', 'name');
            }])
            ->with(['files' => function ($query) {
                $query->select('id', 'name', 'group_task_id');
            }])
            ->completed()
            ->orderByDesc('completed');
    }

    /**
     * Get Completed tasks data.
     */
    public function getCompletedData(int $id = null, int $userId = null, int $page = null, int $perPage = null)
    {
        $this->setGroupOrFail($id);

        $builder = $this->getCompletedBuilder($id, $userId);
        if ($page && $perPage) {
            $builder->forPage($page, $perPage);
        }
        return $builder->get()
            ->each(function ($task) use ($userId) {
                $task->canEdit = $this->group->isAdmin || $task->user_id === $userId;
                unset($task->user_id, $task->completed_user_id);
                foreach ($task->files as $file) {
                    unset($file->group_task_id);
                }
            });
    }


    /**
     * Get Completed tasks.
     */
    public function getCompleted(Request $request, int $id = null)
    {
        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 15);
        if ($perPage > 100)  $perPage = 100;
        return ['data' => $this->getCompletedData($id, null, $page, $perPage), 'total' => $this->getCompletedBuilder($id, null)->count()];
    }

    /**
     * Get file.
     */
    public function downloadTaskFile(int $id)
    {
        $file = ModelsFile::with('groupTask')
            ->whereHas('groupTask')
            ->findOrFail($id);

        $this->setGroupOrFail($file->groupTask->group_id);

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
            $path = Storage::putFile("group_tasks/$id", $file);
            ModelsFile::create([
                'name' => $file->getClientOriginalName(),
                'path' => $path,
                'group_task_id' => $id
            ]);
            $indexFile++;
            $file = $request->file("file_$indexFile");
        }
    }

    /**
     * Create the specified task
     */
    public function create(int $id, Request $request)
    {
        $this->setGroupOrFail($id);

        $dataForCreate = $request->only('title', 'description');
        $dataForCreate['group_id'] = $id;
        $dataForCreate['user_id'] = Auth::user()->id;
        $groupTask = GroupTask::create($dataForCreate);

        $this->createFiles($request, $groupTask->id);

        return response()->noContent();
    }

    /**
     * Change completed date the specified task
     */
    public function changeCompleted(Request $request, int $id)
    {
        $type = $request->type;

        $groupTask = GroupTask::findOrFail($id);

        $this->setGroupOrFail($groupTask->group_id);

        if ($type && !$groupTask->completed) {
            $groupTask->completed = now();
            $groupTask->completed_user_id = Auth::user()->id;
            $groupTask->save();
        } else if (!$type && $groupTask->completed) {
            $groupTask->completed = null;
            $groupTask->completed_user_id = null;
            $groupTask->save();
        }
        $groupTask->save();

        return response()->noContent();
    }

    /**
     * Update the specified task
     */
    public function update(Request $request, int $id)
    {
        $groupTask = GroupTask::findOrFail($id);

        $this->setGroupOrFail($groupTask->group_id);

        if ($this->group->isAdmin || $groupTask->user_id === Auth::user()->id) {
            $groupTask->update($request->only('title', 'description'));

            $oldFiles = $request->oldFiles ? explode(',', $request->oldFiles) : [];

            // delete
            $filesToDelete = ModelsFile::select('id', 'path')
                ->where('group_task_id', $id)
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
        }

        return response()->noContent();
    }

    /**
     * Destroy the specified task
     */
    public function destroy(int $id)
    {
        $groupTask = GroupTask::findOrFail($id);

        $this->setGroupOrFail($groupTask->group_id);

        if ($this->group->isAdmin || $groupTask->user_id === Auth::user()->id) {
            Storage::deleteDirectory("group_tasks/$id");
            $groupTask->files()->delete();
            $groupTask->delete();
        }

        return response()->noContent();
    }

    /**
     * Send PDF to Mail
     */
    public function sendPdfToMail(int $id)
    {
        $this->setGroupOrFail($id);

        $user = Auth::user();

        dispatch(new SendPDFJob($user, $this, [$id, $user->id]));
    }
}
