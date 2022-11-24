<?php

namespace App\Services;

use App\Models\GroupTask;
use App\Models\Task;
use App\Repositories\FileRepository;
use App\Repositories\GroupRepository;
use Exception;
use Illuminate\Support\Facades\Storage;

class FileService
{
    private $fileRepository;
    private $groupRepository;

    public function __construct(FileRepository $fileRepository, GroupRepository $groupRepository)
    {
        $this->fileRepository = $fileRepository;
        $this->groupRepository = $groupRepository;
    }

    public function downloadTaskFile(int $id, int $userId)
    {
        $file = $this->fileRepository->getFileWithFilableById($id);

        $classFilable = get_class($file->filable);

        if (
            ($classFilable === Task::class && $file->filable->user_id !== $userId)
            ||
            ($classFilable === GroupTask::class && !$this->groupRepository->hasAccessToGroup($file->filable->group_id, $userId))
        ) {
            throw new Exception("This user does't have access to the file");
        }

        return Storage::get($file->path);
    }
}
