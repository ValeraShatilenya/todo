<?php

namespace Database\Seeders;

use App\Models\GroupTask;
use Illuminate\Database\Seeder;

class GroupTaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        GroupTask::factory()
            ->count(100)
            ->create();
    }
}
