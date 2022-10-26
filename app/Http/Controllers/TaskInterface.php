<?php

namespace App\Http\Controllers;

interface TaskInterface
{
    public function getNotCompletedData();

    public function getCompletedData();
}
