<?php

namespace Database\Seeders;

use App\Models\Apply;
use App\Models\Opportunity;
use App\Models\Seeker;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ApplySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $seekers = Seeker::all();
        $seekers = $seekers->take(10);
        $opp = Opportunity::all();
        $opp = $opp->take(10);
        foreach ($seekers as $seeker) {
            $time = Carbon::now();
            foreach ($opp as $opportunity) {
                $apply = Apply::create([
                    'seeker_id' => $seeker->id,
                    'opportunity_id' => $opportunity->id,
                    'status' => 'waiting',
                    'company_id' => $opportunity->company_id,
                    'created_at' => $time
                ]);
                $time = $time->addDay();
                }
            }
    }
}
