<?php

namespace Database\Seeders;

use App\Models\News;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('user_name', 'admin')->first();
        // $user = User::find(16);
        for ($i=0; $i < 25; $i++) {
            News::create([
                'created_by' => $user->id,
                'title' => fake()->word(),
                'body' => fake()->realText(300),
            ])->images(['url' => fake()->imageUrl(400, 600)]);
        }
    }
}
