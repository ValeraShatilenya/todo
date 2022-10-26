<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Group::factory()
            ->count(100)
            ->create();

        $groups = Group::all();

        $users = User::select('id')
            ->inRandomOrder()
            ->limit(100)
            ->get()
            ->pluck('id');

        foreach ($groups as $group) {
            $randUsers =  $users->random(6);
            $group->users()->syncWithoutDetaching([
                $randUsers[0] => ['admin' => 1],
                $randUsers[1] => ['admin' => 0],
                $randUsers[2] => ['admin' => 0],
                $randUsers[3] => ['admin' => 0],
                $randUsers[4] => ['admin' => 0],
                $randUsers[5] => ['admin' => 0],
            ]);
        }
    }
}
