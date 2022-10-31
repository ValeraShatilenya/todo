<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
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
        $status = rand(1, 4);
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(3),
            'status' => $status,
            'completed' => $completed,
            'created_at' => $date,
            'updated_at' => $date,
        ];
    }
}
