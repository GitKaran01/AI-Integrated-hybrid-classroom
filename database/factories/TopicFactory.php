<?php

namespace Database\Factories;

use App\Models\Topic;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Topic>
 */
class TopicFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
 public function definition()
{
    return [
        'name' => $this->faker->word(),
        'classroom_id' => \App\Models\Classroom::inRandomOrder()->first()->id,
    ];
}
}
