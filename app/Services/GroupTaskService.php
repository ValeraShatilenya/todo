<?php

namespace App\Services;

use App\Dto\ChangeCompletedTaskDto;
use App\Dto\CreateGroupTaskDto;
use App\Dto\GetGroupTasksDto;
use App\Dto\UpdateTaskDto;
use App\Repositories\FileRepository;
use App\Repositories\GroupRepository;
use App\Repositories\GroupTaskRepository;
use Exception;

class GroupTaskService
{
    private $groupRepository;
    private $groupTaskRepository;
    private $fileRepository;

    public function __construct(GroupRepository $groupRepository, GroupTaskRepository $groupTaskRepository, FileRepository $fileRepository)
    {
        $this->groupRepository = $groupRepository;
        $this->groupTaskRepository = $groupTaskRepository;
        $this->fileRepository = $fileRepository;
    }

    public function getNotCompleted(GetGroupTasksDto $dto)
    {
        $group = $this->groupRepository->getGroupById($dto->groupId, $dto->userId);

        $data = $this->groupTaskRepository
            ->getNotCompletedData($dto->groupId, $dto->sort, $dto->page, $dto->perPage);

        foreach ($data as &$task) {
            $task['canEdit'] = $group->isAdmin || $task['user_id'] === $dto->userId;
            unset($task['user_id']);
        }

        $total = $this->groupTaskRepository->getNotCompletedCount($dto->groupId);

        return compact('data', 'total');
    }

    public function getCompleted(GetGroupTasksDto $dto)
    {
        $group = $this->groupRepository->getGroupById($dto->groupId, $dto->userId);

        $data = $this->groupTaskRepository
            ->getCompletedData($dto->groupId, $dto->sort, $dto->page, $dto->perPage);

        foreach ($data as &$task) {
            $task['canEdit'] = $group->isAdmin || $task['user_id'] === $dto->userId;
            unset($task['user_id'], $task['completed_user_id']);
        }

        $total = $this->groupTaskRepository->getCompletedCount($dto->groupId);

        return compact('data', 'total');
    }

    public function changeCompleted(ChangeCompletedTaskDto $data)
    {
        $groupTask = $this->groupTaskRepository->getById($data->id);

        $this->hasGroupOrFail($groupTask->group_id, $data->userId);

        $this->groupTaskRepository->changeCompleted($groupTask, $data);
    }

    public function create(CreateGroupTaskDto $data)
    {
        $this->hasGroupOrFail($data->groupId, $data->userId);

        $groupTask = $this->groupTaskRepository->create($data);

        $this->fileRepository->createTaskFiles($groupTask, $data->files);
    }

    public function update(UpdateTaskDto $dto)
    {
        $groupTask = $this->groupTaskRepository->getNotCompletedById($dto->id);
        $group = $this->groupRepository->getGroupById($groupTask->group_id, $dto->userId);

        if (!$group->isAdmin && !$groupTask->user_id === $dto->userId) {
            throw new Exception("This user does't have access to the task", 403);
        }

        $this->groupTaskRepository->update($dto);

        $groupTask = $this->groupTaskRepository->getNotCompletedById($dto->id);

        $files = $this->fileRepository->getTaskFiles($groupTask);

        $this->fileRepository->destroyAllTaskFilesByIdExcept($files, $dto->oldFiles);

        $this->fileRepository->createTaskFiles($groupTask, $dto->files);
    }

    public function destroy(int $id, int $userId)
    {
        $groupTask = $this->groupTaskRepository->getById($id);

        $group = $this->groupRepository->getGroupById($groupTask->group_id, $userId);

        if (!$group->isAdmin && $groupTask->user_id !== $userId) {
            throw new Exception("This user does't have access to the task", 403);
        }

        $files = $this->fileRepository->getTaskFiles($groupTask);

        $this->fileRepository->destroyAllTaskFiles($groupTask, $files);

        $this->groupTaskRepository->destroy($id);
    }

    private function hasGroupOrFail(int $id, int $userId)
    {
        if (!$this->groupRepository->hasAccessToGroup($id, $userId)) {
            throw new Exception("This user does't have access to the task", 403);
        }
    }
}
