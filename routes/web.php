<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GroupTaskController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\TaskController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

require __DIR__ . '/auth.php';

Route::middleware('auth')->group(function () {
    Route::prefix('task')->group(function () {
        Route::get('/notCompleted', [TaskController::class, 'getNotCompleted']);
        Route::get('/completed', [TaskController::class, 'getCompleted']);
        Route::get('/file/{id}/download', [TaskController::class, 'downloadTaskFile']);
        Route::post('/pdf', [TaskController::class, 'sendPdfToMail']);
        Route::post('/', [TaskController::class, 'create']);
        Route::patch('/{id}/changeCompleted', [TaskController::class, 'changeCompleted']);
        Route::post('/{id}', [TaskController::class, 'update']);
        Route::delete('/{id}', [TaskController::class, 'destroy']);
    });
    Route::prefix('group-task')->group(function () {
        Route::get('/{id}/notCompleted', [GroupTaskController::class, 'getNotCompleted']);
        Route::get('/{id}/completed', [GroupTaskController::class, 'getCompleted']);
        Route::get('/file/{id}/download', [GroupTaskController::class, 'downloadTaskFile']);
        Route::post('/{id}/pdf', [GroupTaskController::class, 'sendPdfToMail']);
        Route::post('/group/{id}', [GroupTaskController::class, 'create']);
        Route::patch('/{id}/changeCompleted', [GroupTaskController::class, 'changeCompleted']);
        Route::post('/{id}', [GroupTaskController::class, 'update']);
        Route::delete('/{id}', [GroupTaskController::class, 'destroy']);
    });
    Route::prefix('group')->group(function () {
        Route::get('/forTasks', [GroupController::class, 'getGroupsForTasks']);
        Route::get('/forGroups', [GroupController::class, 'getGroupsForGroups']);
        Route::get('/{id}/users', [GroupController::class, 'getUsers']);
        Route::post('/', [GroupController::class, 'create']);
        Route::post('/{groupId}/user', [GroupController::class, 'addUser']);
        Route::delete('/{groupId}/user/{userId}', [GroupController::class, 'deleteUser']);
        Route::patch('/{id}', [GroupController::class, 'update']);
        Route::delete('/{id}', [GroupController::class, 'destroy']);
    });
    Route::get('{any?}', function () {
        return view('main');
    })
        ->where('any', '.*');
});
