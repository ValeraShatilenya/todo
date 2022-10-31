<?php

namespace Database\Factories;

use App\Models\GroupUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class GroupTaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $date = $this->faker->date();
        $completed = rand(0, 1) ? $date : null;
        $groupUser = GroupUser::inRandomOrder()->first();
        $status = rand(1, 4);
        return [
            'user_id' => $groupUser->user_id,
            'group_id' => $groupUser->group_id,
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(3),
            'status' => $status,
            'completed' => $completed,
            'completed_user_id' => $completed ? User::inRandomOrder()->first()->id : null,
            'created_at' => $date,
            'updated_at' => $date,
        ];
    }
}
