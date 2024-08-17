<?php

namespace Database\Seeders;

use App\Models\Reason;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReasonsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reasons = [
            'fake account',
            'pretending to be someone',
            'image or user_name are appropriate',

            'posting inappropriate things',
            'misleading news',
            'another_reason'
        ];

        for ($i=0; $i < sizeof($reasons); $i++) {
            Reason::create([
                'title' => $reasons[$i]
            ]);
        }
    }
}
