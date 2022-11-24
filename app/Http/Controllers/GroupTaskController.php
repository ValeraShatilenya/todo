<?php

namespace App\Http\Controllers;

use App\Dto\ChangeCompletedTaskDto;
use App\Dto\CreateGroupTaskDto;
use App\Dto\GetGroupTasksDto;
use App\Dto\UpdateTaskDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChangeCompletedTaskRequest;
use App\Http\Requests\GetTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Jobs\SendPDFJob;
use App\Repositories\GroupTaskRepository;
use App\Services\GroupTaskService;
use Exception;
use Illuminate\Support\Facades\Auth;

class GroupTaskController extends Controller
{
    public function getNotCompleted(GetTaskRequest $request, GroupTaskService $groupTaskService, int $id = null)
    {
        $data = $request->validated();
        $data['groupId'] = $id;
        $data['userId'] = Auth::user()->id;

        $dto = GetGroupTasksDto::createFromArray($data);

        return $groupTaskService->getNotCompleted($dto);
    }

    public function getCompleted(GetTaskRequest $request, GroupTaskService $groupTaskService, int $id = null)
    {
        $data = $request->validated();
        $data['groupId'] = $id;
        $data['userId'] = Auth::user()->id;

        $dto = GetGroupTasksDto::createFromArray($data);

        return $groupTaskService->getCompleted($dto);
    }

    public function create(UpdateTaskRequest $request, GroupTaskService $groupTaskService, int $id)
    {
        $data = $request->validated();
        $data['groupId'] = $id;
        $data['userId'] = Auth::user()->id;

        $dto = CreateGroupTaskDto::createFromArray($data);

        $groupTaskService->create($dto);

        return response()->noContent();
    }

    public function changeCompleted(ChangeCompletedTaskRequest $request, GroupTaskService $groupTaskService, int $id)
    {
        $data = $request->validated();
        $data['id'] = $id;
        $data['userId'] = Auth::user()->id;

        $dto = ChangeCompletedTaskDto::createFromArray($data);

        $groupTaskService->changeCompleted($dto);

        return response()->noContent();
    }

    public function update(UpdateTaskRequest $request, GroupTaskService $groupTaskService, int $id)
    {
        $data = $request->validated();
        $data['id'] = $id;
        $data['userId'] = Auth::user()->id;

        $dto = UpdateTaskDto::createFromArray($data);

        $groupTaskService->update($dto);

        return response()->noContent();
    }

    public function destroy(GroupTaskService $groupTaskService, int $id)
    {
        $groupTaskService->destroy($id, Auth::user()->id);

        return response()->noContent();
    }

    public function sendPdfToMail(int $id)
    {
        if (!$this->groupRepository->hasAccessToGroup($id, Auth::user()->id)) {
            throw new Exception("This user does't have access to the group", 403);
        }

        $user = Auth::user();

        dispatch(new SendPDFJob($user, new GroupTaskRepository, [$id, $user->id]));
    }
}
