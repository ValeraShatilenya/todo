<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TaskTest extends TestCase
{
    /**
     * A test get user tasks (completed and not completed)
     *
     * @return void
     */
    public function test_get_user_personal_tasks()
    {
        $user = User::factory()->create();

        Task::factory()
            ->count(15)
            ->for($user)
            ->create();

        $response = $this->actingAs($user)
            ->get('/task/notCompleted', ['page' => '1', 'perPage' => 15]);

        $response->assertStatus(200);

        $response = $this->actingAs($user)
            ->get('/task/completed', ['page' => '1', 'perPage' => 15]);

        $response->assertStatus(200);

        $total = 0;

        $data = $this->actingAs($user)
            ->getJson('/task/notCompleted', ['page' => '1', 'perPage' => 15]);

        $total += $data->original['total'];

        $data = $this->actingAs($user)
            ->getJson('/task/completed', ['page' => '1', 'perPage' => 15]);

        $total += $data->original['total'];

        $this->assertEquals(15, $total);
    }

    /**
     * A test change user task completed date
     *
     * @return void
     */
    public function test_change_user_task_completed_date()
    {
        $user = User::factory()->create();

        $task = $user->tasks()->create([
            'title' => 'Title',
            'description' => 'Description',
            'status' => rand(1, 4),
            'completed' => null
        ]);

        $response = $this->actingAs($user)
            ->patch("/task/$task->id/changeCompleted", ['type' => true]);

        $response->assertSuccessful();

        $task = $task->fresh();

        $this->assertNotEmpty($task->completed);

        $response = $this->actingAs($user)
            ->patch("/task/$task->id/changeCompleted", ['type' => false]);

        $response->assertSuccessful();

        $task = $task->fresh();

        $this->assertEmpty($task->completed);

        $newUser = User::factory()->create();

        $newUserTask = Task::factory()
            ->for($newUser)
            ->create();

        $response = $this->actingAs($user)
            ->patch("/task/$newUserTask->id/changeCompleted", ['type' => true]);

        $response->assertNotFound();
    }

    /**
     * A test create user task
     *
     * @return void
     */
    public function test_create_user_task()
    {
        $user = User::factory()->create();

        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->actingAs($user)
            ->post("/task", [
                'title' => 'Test title',
                'description' => 'Lorem ipsum dolor sit amet consectetur, adipisicing elit. Ab, vero corrupti nihil esse similique, voluptatibus rerum excepturi consequatur aspernatur, at enim. Temporibus mollitia dolorum assumenda obcaecati doloribus iure, perferendis provident.',
                'status' => rand(1, 4),
                'file_1' => $file
            ]);

        $response->assertSuccessful();

        $this->assertDatabaseHas('tasks', [
            'user_id' => $user->id,
            'title' => 'Test title',
            'description' => 'Lorem ipsum dolor sit amet consectetur, adipisicing elit. Ab, vero corrupti nihil esse similique, voluptatibus rerum excepturi consequatur aspernatur, at enim. Temporibus mollitia dolorum assumenda obcaecati doloribus iure, perferendis provident.',
        ]);

        $task = $user->tasks()->first();

        $this->assertDatabaseHas('files', [
            'task_id' => $task->id
        ]);

        Storage::assertExists("tasks/$task->id/{$file->hashName()}");
        Storage::deleteDirectory("tasks/$task->id");
    }

    /**
     * A test update user task
     *
     * @return void
     */
    public function test_update_user_task()
    {
        $user = User::factory()->create();

        $task = Task::factory()
            ->for($user)
            ->create();

        $oldFile = UploadedFile::fake()->image('oldFile.jpg');
        $pathOldFile = Storage::putFile("tasks/$task->id", $oldFile);

        $task->files()->create([
            'name' => 'oldFile.jpg',
            'path' => $pathOldFile
        ]);

        $newFile = UploadedFile::fake()->image('newFile.jpg');

        $response = $this->actingAs($user)
            ->post("/task/$task->id", [
                'title' => 'Test title',
                'description' => 'Lorem ipsum dolor sit amet consectetur, adipisicing elit. Ab, vero corrupti nihil esse similique, voluptatibus rerum excepturi consequatur aspernatur, at enim. Temporibus mollitia dolorum assumenda obcaecati doloribus iure, perferendis provident.',
                'status' => rand(1, 4),
                'file_1' => $newFile
            ]);

        $response->assertSuccessful();

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Test title',
        ]);

        $this->assertDatabaseHas('files', [
            'task_id' => $task->id,
            'name' => 'newFile.jpg',
        ]);

        $this->assertDatabaseMissing('files', [
            'task_id' => $task->id,
            'name' => 'oldFile.jpg',
        ]);

        Storage::assertExists("tasks/$task->id/{$newFile->hashName()}");
        Storage::assertMissing("tasks/$task->id/{$oldFile->hashName()}");
        Storage::deleteDirectory("tasks/$task->id");

        $newUser = User::factory()->create();

        $response = $this->actingAs($newUser)
            ->post("/task/$task->id", [
                'title' => 'Test title',
                'description' => 'Test description',
                'status' => rand(1, 4)
            ]);

        $response->assertNotFound();

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Test title',
        ]);
    }

    /**
     * A test delete user task
     *
     * @return void
     */
    public function test_delete_user_task()
    {
        $user = User::factory()->create();

        $task = Task::factory()
            ->for($user)
            ->create();

        $response = $this->actingAs($user)
            ->delete("/task/$task->id");

        $response->assertSuccessful();

        $this->assertSoftDeleted($task);

        $newUser = User::factory()->create();

        $newUserTask = Task::factory()
            ->for($newUser)
            ->create();

        $response = $this->actingAs($user)
            ->delete("/task/$newUserTask->id");

        $response->assertNotFound();
    }

    /**
     * A test download task file
     *
     * @return void
     */
    public function test_download_task_file()
    {
        $user = User::factory()->create();

        $task = Task::factory()
            ->for($user)
            ->create();

        $fileForCreate = UploadedFile::fake()->image('file.jpg');
        $pathFile = Storage::putFile("tasks/$task->id", $fileForCreate);

        $task->files()->create([
            'name' => 'file.jpg',
            'path' => $pathFile
        ]);

        $file = $task->files()->first();

        $response = $this->actingAs($user)
            ->get("/task/file/$file->id/download");

        $response->assertSuccessful();

        $newUser = User::factory()->create();

        $response = $this->actingAs($newUser)
            ->get("/task/file/$file->id/download");

        $response->assertNotFound();

        Storage::deleteDirectory("tasks/$task->id");
    }
}
