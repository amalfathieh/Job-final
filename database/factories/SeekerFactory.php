<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Seeker>
 */
class SeekerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "first_name" => fake()->firstName(),
            "last_name" => fake()->lastName(),
            "gender" => fake()->randomElement(['male', 'female']),
            "birth_day" => fake()->date(),
            "location" => fake()->address(),
            "skills" => [fake()->title(), fake()->title(), fake()->title(), fake()->title()],
            "certificates" => [fake()->title(), fake()->title(), fake()->title(), fake()->title()],
            "about" => fake()->sentence(),
        ];
    }
}
