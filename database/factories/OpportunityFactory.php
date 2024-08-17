<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Opportunity>
 */
class OpportunityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->title(),
            'body' => fake()->sentence(),
            'location' => fake()->address(),
            'job_type' => fake()->randomElement(['full_time', 'part_time', 'contract', 'temporary', 'volunteer']),
            'work_place_type' => fake()->randomElement(['on_site', 'hybrid', 'remote']),
            'qualifications' => [fake()->slug(), fake()->slug()],
            'skills_req' => ["Python","Laravel","Arabic","English"],
            'salary' => fake()->numberBetween(500, 10000),
            'vacant' => 1,
            'job_hours' => fake()->numberBetween(8, 18)
        ];
    }
}
