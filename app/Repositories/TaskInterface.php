<?php

namespace App\Repositories;

interface TaskInterface
{
    public function getNotCompletedData(int $userId);

    public function getCompletedData(int $userId);
}
