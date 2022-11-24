<?php

namespace App\Dto;

class GetGroupTasksDto
{
    public int $groupId;
    public int $userId;
    public int $page;
    public int $perPage;
    public string $sort;

    public static function createFromArray(array $data): self
    {
        $dto = new self();

        $dto->groupId = $data['groupId'];
        $dto->userId = $data['userId'];
        $dto->page = $data['page'];
        $dto->perPage = $data['perPage'];
        $dto->sort = $data['sort'];

        return $dto;
    }
}
