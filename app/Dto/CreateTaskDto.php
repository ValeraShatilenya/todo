<?php

namespace App\Dto;

class CreateTaskDto
{
    public int $userId;
    public string $title;
    public int $status;
    public string $description;
    public array $files;
    public array $oldFiles;

    public static function createFromArray(array $data): static
    {
        $dto = new static();

        $dto->userId = $data['userId'];
        $dto->title = $data['title'];
        $dto->status = $data['status'];
        $dto->description = $data['description'];
        $dto->files = array_key_exists('files', $data) ? $data['files'] : [];
        $dto->oldFiles = array_key_exists('oldFiles', $data) ? $data['oldFiles'] : [];

        return $dto;
    }
}
