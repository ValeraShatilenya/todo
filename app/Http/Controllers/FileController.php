<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\FileService;
use Illuminate\Support\Facades\Auth;

class FileController extends Controller
{
    private $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    public function downloadTaskFile(int $id)
    {
        $userId = Auth::user()->id;
        return $this->fileService->downloadTaskFile($id, $userId);
    }
}
