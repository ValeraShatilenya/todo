<?php

namespace App\Dto;

class ChangeCompletedTaskDto
{
    public int $id;
    public int $userId;
    public bool $type;

    public static function createFromArray(array $data): static
    {
        $dto = new static();

        $dto->id = $data['id'];
        $dto->userId = $data['userId'];
        $dto->type = $data['type'];

        return $dto;
    }
}
