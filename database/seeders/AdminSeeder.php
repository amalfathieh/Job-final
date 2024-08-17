<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::create([
            'user_name'=> 'admin',
            'email'=> 'admin@gmail.com',
            'password'=>'Admin@123',
            'roles_name'=> ['owner'],
            'is_verified'=> 1,
        ]);
        $admin->assignRole('owner');
    }
}
