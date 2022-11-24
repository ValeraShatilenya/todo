<?php

namespace App\Services;

use App\Dto\ChangeCompletedTaskDto;
use App\Dto\CreateTaskDto;
use App\Dto\GetTasksDto;
use App\Dto\UpdateTaskDto;
use App\Repositories\FileRepository;
use App\Repositories\TaskRepository;

class TaskService
{
    private $taskRepository;
    private $fileRepository;

    public function __construct(TaskRepository $taskRepository, FileRepository $fileRepository)
    {
        $this->taskRepository = $taskRepository;
        $this->fileRepository = $fileRepository;
    }

    public function getNotCompleted(GetTasksDto $dto)
    {
        $data = $this->taskRepository->getNotCompletedData($dto->userId, $dto->sort, $dto->page, $dto->perPage);
        $total = $this->taskRepository->getNotCompletedCount($dto->userId);

        return compact('data', 'total');
    }

    public function getCompleted(GetTasksDto $dto)
    {
        $data = $this->taskRepository->getCompletedData($dto->userId, $dto->sort, $dto->page, $dto->perPage);
        $total = $this->taskRepository->getCompletedCount($dto->userId);

        return compact('data', 'total');
    }

    public function changeCompleted(ChangeCompletedTaskDto $data)
    {
        $this->taskRepository->changeCompleted($data->id, $data->userId, $data->type);
    }

    public function create(CreateTaskDto $data)
    {
        $task = $this->taskRepository->create($data);

        $this->fileRepository->createTaskFiles($task, $data->files);
    }

    public function update(UpdateTaskDto $data)
    {
        $task = $this->taskRepository->update($data);

        $files = $this->fileRepository->getTaskFiles($task);

        $this->fileRepository->destroyAllTaskFilesByIdExcept($files, $data->oldFiles);

        $this->fileRepository->createTaskFiles($task, $data->files);
    }

    public function destroy(int $id, int $userId)
    {
        $task = $this->taskRepository->getById($id, $userId);

        $files = $this->fileRepository->getTaskFiles($task);

        $this->fileRepository->destroyAllTaskFiles($task, $files);

        $this->taskRepository->destroy($id);
    }
}
