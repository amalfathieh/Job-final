<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Report;
use App\Models\Seeker;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReportsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::inRandomOrder()->take(20)->get();
        $users = $users->reject(function(User $user) {
            $roles = $user->roles_name;
            foreach ($roles as $value) {
                if ($value === 'owner' || $value === 'employee') {
                    return true;
                }
            }
        });
        foreach ($users as $user) {
            $someOne = User::inRandomOrder()->take(1)->first();
            while (!$someOne->hasRole('user')) {
                $someOne = User::inRandomOrder()->take(1)->first();
            }
            if ($user->id != $someOne->id) {
                $reason_id = fake()->randomElement([1, 2, 3, 6]);
                $report = Report::create([
                'created_by' => $user->id,
                'user_id' => $someOne->id,
                'reason_id' => $reason_id,
                'another_reason' => $reason_id == 6 ?  fake()->sentence(4) : null,
                'notes' => $reason_id == 2 ? "Pretending to be " . fake()->firstName() : null,
                'is_viewed' => fake()->randomElement([0, 1]),
                'created_at' => now(),
                'updated_at' => now(),
                ]);
            }
        }

        $companies = Company::all();
        $companies = $companies->take(20);
        foreach ($companies as $company) {
            $someOne = Company::inRandomOrder()->take(1)->first();
            $opps = $someOne->opportunities;
            while(sizeof($opps) < 1) {
                $someOne = Company::inRandomOrder()->take(1)->first();
                $opps = $someOne->opportunities;
            }
            if ($company->id != $someOne->id) {
                $reason_id = fake()->randomElement([4, 5, 6]);

                Report::create([
                    'created_by' => $company->user->id,
                    'user_id' => $someOne->user->id,
                    'reason_id' => $reason_id,
                    'another_reason' => $reason_id == 6 ?  fake()->sentence(4) : null,
                    'notes' => 'Opportunity\'s id is ' . $opps[fake()->randomElement(range(0, sizeof($opps) > 0 ? sizeof($opps) - 1 : sizeof($opps)))]->id,
                    'is_viewed' => fake()->randomElement([0, 1]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $seekers = Seeker::all();
        $seekers = $seekers->take(20);
        foreach ($seekers as $seeker) {
            $someOne = Seeker::inRandomOrder()->take(1)->first();
            $posts = $someOne->posts;
            while(sizeof($posts) < 1) {
                $someOne = Seeker::inRandomOrder()->take(1)->first();
                $posts = $someOne->posts;
            }
            if ($seeker->id != $someOne->id) {
                $reason_id = fake()->randomElement([4, 5, 6]);
                Report::create([
                    'created_by' => $seeker->user->id,
                    'user_id' => $someOne->user->id,
                    'reason_id' => $reason_id,
                    'another_reason' => $reason_id == 6 ?  fake()->sentence(4) : null,
                    'notes' => 'Post\'s id is ' . $posts[fake()->randomElement(range(0, sizeof($posts) > 0 ? sizeof($posts) - 1 : sizeof($posts)))]->id,
                    'is_viewed' => fake()->randomElement([0, 1]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
