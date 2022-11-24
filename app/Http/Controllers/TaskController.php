<?php

namespace App\Http\Controllers;

use App\Dto\ChangeCompletedTaskDto;
use App\Dto\CreateTaskDto;
use App\Dto\GetTasksDto;
use App\Dto\UpdateTaskDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChangeCompletedTaskRequest;
use App\Http\Requests\GetTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Jobs\SendPDFJob;
use App\Repositories\TaskRepository;
use App\Services\TaskService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function getNotCompleted(GetTaskRequest $request, TaskService $taskService)
    {
        $data = $request->validated();
        $data['userId'] = Auth::user()->id;

        $dto = GetTasksDto::createFromArray($data);

        return $taskService->getNotCompleted($dto);
    }

    public function getCompleted(GetTaskRequest $request, TaskService $taskService)
    {
        $data = $request->validated();
        $data['userId'] = Auth::user()->id;

        $dto = GetTasksDto::createFromArray($data);

        return $taskService->getCompleted($dto);
    }

    public function create(UpdateTaskRequest $request, TaskService $taskService)
    {
        $data = $request->validated();
        $data['userId'] = Auth::user()->id;

        $dto = CreateTaskDto::createFromArray($data);

        $taskService->create($dto);

        return response()->noContent();
    }

    public function changeCompleted(ChangeCompletedTaskRequest $request, TaskService $taskService, int $id)
    {
        $data = $request->validated();
        $data['id'] = $id;
        $data['userId'] = Auth::user()->id;

        $dto = ChangeCompletedTaskDto::createFromArray($data);

        $taskService->changeCompleted($dto);

        return response()->noContent();
    }

    public function update(UpdateTaskRequest $request, TaskService $taskService, int $id)
    {
        $data = $request->validated();
        $data['id'] = $id;
        $data['userId'] = Auth::user()->id;

        $dto = UpdateTaskDto::createFromArray($data);

        $taskService->update($dto);

        return response()->noContent();
    }

    public function destroy(TaskService $taskService, int $id)
    {
        $userId = Auth::user()->id;

        $taskService->destroy($id, $userId);

        return response()->noContent();
    }

    public function sendPdfToMail()
    {
        $user = Auth::user();

        dispatch(new SendPDFJob($user, new TaskRepository, [$user->id]));
    }
}
