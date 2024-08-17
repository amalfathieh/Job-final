<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Opportunity;
use App\Models\Post;
use App\Models\Seeker;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'user_name'=> fake()->userName(),
            'email'=> fake()->email(),
            'password'=>'Aa123123',
            'roles_name'=> ['user', 'company'],
            'is_verified'=> 1,
        ]);
        $user->syncRoles(['user', 'company']);
        $company = Company::create([
            'user_id'=>$user->id,
            'company_name'=>fake()->company(),
            'location'=>fake()->country(),
            'about' => fake()->paragraph(),
            'domain' => fake()->word(),
            'contact_info' => fake()->realText(50)
        ]);

            $company->image()->create(['url' => fake()->imageUrl(640, 480)]); {
            Opportunity::create([
                'company_id'=> $company->id,
                'title' => fake()->word(),
                'body' => fake()->sentence(),
                'location' => fake()->address(),
                'job_type' => fake()->randomElement(['full_time', 'part_time', 'contract', 'temporary', 'volunteer']),
                'work_place_type' => fake()->randomElement(['on_site', 'hybrid', 'remote']),
                'qualifications' => [fake()->slug(), fake()->slug()],
                'skills_req' => ["Python","Laravel","Arabic","English"],
                'salary' => fake()->numberBetween(500, 10000),
                'vacant' => 1,
                'job_hours' => fake()->numberBetween(8, 18)
            ]);
        }

        $user = User::create([
            'user_name'=> fake()->userName(),
            'email'=> fake()->email(),
            'password'=>'Aa123123',
            'roles_name'=> ['user', 'job_seeker'],
            'is_verified'=> 1,
        ]);
        $user->syncRoles(['user', 'job_seeker']);
        $seeker = Seeker::create([
            'user_id' => $user->id,
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'gender' => fake()->randomElement(['male', 'female']),
            'birth_day' => fake()->date(),
            'location' => fake()->country(),
            'skills' => ['Python', 'Laravel', 'React JS', 'English'],
            'certificates' => [fake()->word(), fake()->word()],
            'about' => fake()->paragraph(),
            'specialization' => fake()->word()
        ]);
        for ($i=0; $i < 10; $i++) {
            Post::create([
                'seeker_id' => $seeker->id,
                'body' => fake()->realText()
            ]);
        }
        $user = User::create([
            'user_name'=> fake()->userName(),
            'email'=> fake()->email(),
            'password'=>'Aa123123',
            'roles_name'=> ['user', 'company'],
            'is_verified'=> 1,
        ]);
        $user->syncRoles(['user', 'company']);
        $company = Company::create([
            'user_id'=>$user->id,
            'company_name'=>fake()->company(),
            'location'=>fake()->country(),
            'about' => fake()->paragraph(),
            'domain' => fake()->word(),
            'contact_info' => fake()->realText(50)
        ]);

            $company->image()->create(['url' => fake()->imageUrl(640, 480)]); {
            Opportunity::create([
                'company_id'=> $company->id,
                'title' => fake()->word(),
                'body' => fake()->sentence(),
                'location' => fake()->address(),
                'job_type' => fake()->randomElement(['full_time', 'part_time', 'contract', 'temporary', 'volunteer']),
                'work_place_type' => fake()->randomElement(['on_site', 'hybrid', 'remote']),
                'qualifications' => [fake()->slug(), fake()->slug()],
                'skills_req' => ["Python","Laravel","Arabic","English"],
                'salary' => fake()->numberBetween(500, 10000),
                'vacant' => 1,
                'job_hours' => fake()->numberBetween(8, 18)
            ]);
        }

        $user = User::create([
            'user_name'=> fake()->userName(),
            'email'=> fake()->email(),
            'password'=>'Aa123123',
            'roles_name'=> ['user', 'job_seeker'],
            'is_verified'=> 1,
        ]);
        $user->syncRoles(['user', 'job_seeker']);
        $seeker = Seeker::create([
            'user_id' => $user->id,
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'gender' => fake()->randomElement(['male', 'female']),
            'birth_day' => fake()->date(),
            'location' => fake()->country(),
            'skills' => ['Python', 'Laravel', 'React JS', 'English'],
            'certificates' => [fake()->word(), fake()->word()],
            'about' => fake()->paragraph(),
            'specialization' => fake()->word()
        ]);
        for ($i=0; $i < 10; $i++) {
            Post::create([
                'seeker_id' => $seeker->id,
                'body' => fake()->realText()
            ]);
        }
        $user = User::create([
            'user_name'=> fake()->userName(),
            'email'=> fake()->email(),
            'password'=>'Aa123123',
            'roles_name'=> ['user', 'company'],
            'is_verified'=> 1,
        ]);
        $user->syncRoles(['user', 'company']);
        $company = Company::create([
            'user_id'=>$user->id,
            'company_name'=>fake()->company(),
            'location'=>fake()->country(),
            'about' => fake()->paragraph(),
            'domain' => fake()->word(),
            'contact_info' => fake()->realText(50)
        ]);

            $company->image()->create(['url' => fake()->imageUrl(640, 480)]); {
            Opportunity::create([
                'company_id'=> $company->id,
                'title' => fake()->word(),
                'body' => fake()->sentence(),
                'location' => fake()->address(),
                'job_type' => fake()->randomElement(['full_time', 'part_time', 'contract', 'temporary', 'volunteer']),
                'work_place_type' => fake()->randomElement(['on_site', 'hybrid', 'remote']),
                'qualifications' => [fake()->slug(), fake()->slug()],
                'skills_req' => ["Python","Laravel","Arabic","English"],
                'salary' => fake()->numberBetween(500, 10000),
                'vacant' => 1,
                'job_hours' => fake()->numberBetween(8, 18)
            ]);
        }

        $user = User::create([
            'user_name'=> fake()->userName(),
            'email'=> fake()->email(),
            'password'=>'Aa123123',
            'roles_name'=> ['user', 'job_seeker'],
            'is_verified'=> 1,
        ]);
        $user->syncRoles(['user', 'job_seeker']);
        $seeker = Seeker::create([
            'user_id' => $user->id,
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'gender' => fake()->randomElement(['male', 'female']),
            'birth_day' => fake()->date(),
            'location' => fake()->country(),
            'skills' => ['Python', 'Laravel', 'React JS', 'English'],
            'certificates' => [fake()->word(), fake()->word()],
            'about' => fake()->paragraph(),
            'specialization' => fake()->word()
        ]);
        for ($i=0; $i < 10; $i++) {
            Post::create([
                'seeker_id' => $seeker->id,
                'body' => fake()->realText()
            ]);
        }
        $user = User::create([
            'user_name'=> fake()->userName(),
            'email'=> fake()->email(),
            'password'=>'Aa123123',
            'roles_name'=> ['user', 'company'],
            'is_verified'=> 1,
        ]);
        $user->syncRoles(['user', 'company']);
        $company = Company::create([
            'user_id'=>$user->id,
            'company_name'=>fake()->company(),
            'location'=>fake()->country(),
            'about' => fake()->paragraph(),
            'domain' => fake()->word(),
            'contact_info' => fake()->realText(50)
        ]);

            $company->image()->create(['url' => fake()->imageUrl(640, 480)]); {
            Opportunity::create([
                'company_id'=> $company->id,
                'title' => fake()->word(),
                'body' => fake()->sentence(),
                'location' => fake()->address(),
                'job_type' => fake()->randomElement(['full_time', 'part_time', 'contract', 'temporary', 'volunteer']),
                'work_place_type' => fake()->randomElement(['on_site', 'hybrid', 'remote']),
                'qualifications' => [fake()->slug(), fake()->slug()],
                'skills_req' => ["Python","Laravel","Arabic","English"],
                'salary' => fake()->numberBetween(500, 10000),
                'vacant' => 1,
                'job_hours' => fake()->numberBetween(8, 18)
            ]);
        }

        $user = User::create([
            'user_name'=> fake()->userName(),
            'email'=> fake()->email(),
            'password'=>'Aa123123',
            'roles_name'=> ['user', 'job_seeker'],
            'is_verified'=> 1,
        ]);
        $user->syncRoles(['user', 'job_seeker']);
        $seeker = Seeker::create([
            'user_id' => $user->id,
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'gender' => fake()->randomElement(['male', 'female']),
            'birth_day' => fake()->date(),
            'location' => fake()->country(),
            'skills' => ['Python', 'Laravel', 'React JS', 'English'],
            'certificates' => [fake()->word(), fake()->word()],
            'about' => fake()->paragraph(),
            'specialization' => fake()->word()
        ]);
        for ($i=0; $i < 10; $i++) {
            Post::create([
                'seeker_id' => $seeker->id,
                'body' => fake()->realText()
            ]);
        }
        $user = User::create([
            'user_name'=> fake()->userName(),
            'email'=> fake()->email(),
            'password'=>'Aa123123',
            'roles_name'=> ['user', 'company'],
            'is_verified'=> 1,
        ]);
        $user->syncRoles(['user', 'company']);
        $company = Company::create([
            'user_id'=>$user->id,
            'company_name'=>fake()->company(),
            'location'=>fake()->country(),
            'about' => fake()->paragraph(),
            'domain' => fake()->word(),
            'contact_info' => fake()->realText(50)
        ]);

            $company->image()->create(['url' => fake()->imageUrl(640, 480)]); {
            Opportunity::create([
                'company_id'=> $company->id,
                'title' => fake()->word(),
                'body' => fake()->sentence(),
                'location' => fake()->address(),
                'job_type' => fake()->randomElement(['full_time', 'part_time', 'contract', 'temporary', 'volunteer']),
                'work_place_type' => fake()->randomElement(['on_site', 'hybrid', 'remote']),
                'qualifications' => [fake()->slug(), fake()->slug()],
                'skills_req' => ["Python","Laravel","Arabic","English"],
                'salary' => fake()->numberBetween(500, 10000),
                'vacant' => 1,
                'job_hours' => fake()->numberBetween(8, 18)
            ]);
        }

        $user = User::create([
            'user_name'=> fake()->userName(),
            'email'=> fake()->email(),
            'password'=>'Aa123123',
            'roles_name'=> ['user', 'job_seeker'],
            'is_verified'=> 1,
        ]);
        $user->syncRoles(['user', 'job_seeker']);
        $seeker = Seeker::create([
            'user_id' => $user->id,
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'gender' => fake()->randomElement(['male', 'female']),
            'birth_day' => fake()->date(),
            'location' => fake()->country(),
            'skills' => ['Python', 'Laravel', 'React JS', 'English'],
            'certificates' => [fake()->word(), fake()->word()],
            'about' => fake()->paragraph(),
            'specialization' => fake()->word()
        ]);
        for ($i=0; $i < 10; $i++) {
            Post::create([
                'seeker_id' => $seeker->id,
                'body' => fake()->realText()
            ]);
        }
        $user = User::create([
            'user_name'=> fake()->userName(),
            'email'=> fake()->email(),
            'password'=>'Aa123123',
            'roles_name'=> ['user', 'company'],
            'is_verified'=> 1,
        ]);
        $user->syncRoles(['user', 'company']);
        $company = Company::create([
            'user_id'=>$user->id,
            'company_name'=>fake()->company(),
            'location'=>fake()->country(),
            'about' => fake()->paragraph(),
            'domain' => fake()->word(),
            'contact_info' => fake()->realText(50)
        ]);

            $company->image()->create(['url' => fake()->imageUrl(640, 480)]); {
            Opportunity::create([
                'company_id'=> $company->id,
                'title' => fake()->word(),
                'body' => fake()->sentence(),
                'location' => fake()->address(),
                'job_type' => fake()->randomElement(['full_time', 'part_time', 'contract', 'temporary', 'volunteer']),
                'work_place_type' => fake()->randomElement(['on_site', 'hybrid', 'remote']),
                'qualifications' => [fake()->slug(), fake()->slug()],
                'skills_req' => ["Python","Laravel","Arabic","English"],
                'salary' => fake()->numberBetween(500, 10000),
                'vacant' => 1,
                'job_hours' => fake()->numberBetween(8, 18)
            ]);
        }

        $user = User::create([
            'user_name'=> fake()->userName(),
            'email'=> fake()->email(),
            'password'=>'Aa123123',
            'roles_name'=> ['user', 'job_seeker'],
            'is_verified'=> 1,
        ]);
        $user->syncRoles(['user', 'job_seeker']);
        $seeker = Seeker::create([
            'user_id' => $user->id,
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'gender' => fake()->randomElement(['male', 'female']),
            'birth_day' => fake()->date(),
            'location' => fake()->country(),
            'skills' => ['Python', 'Laravel', 'React JS', 'English'],
            'certificates' => [fake()->word(), fake()->word()],
            'about' => fake()->paragraph(),
            'specialization' => fake()->word()
        ]);
        for ($i=0; $i < 10; $i++) {
            Post::create([
                'seeker_id' => $seeker->id,
                'body' => fake()->realText()
            ]);
        }
        $user = User::create([
            'user_name'=> fake()->userName(),
            'email'=> fake()->email(),
            'password'=>'Aa123123',
            'roles_name'=> ['user', 'company'],
            'is_verified'=> 1,
        ]);
        $user->syncRoles(['user', 'company']);
        $company = Company::create([
            'user_id'=>$user->id,
            'company_name'=>fake()->company(),
            'location'=>fake()->country(),
            'about' => fake()->paragraph(),
            'domain' => fake()->word(),
            'contact_info' => fake()->realText(50)
        ]);

            $company->image()->create(['url' => fake()->imageUrl(640, 480)]); {
            Opportunity::create([
                'company_id'=> $company->id,
                'title' => fake()->word(),
                'body' => fake()->sentence(),
                'location' => fake()->address(),
                'job_type' => fake()->randomElement(['full_time', 'part_time', 'contract', 'temporary', 'volunteer']),
                'work_place_type' => fake()->randomElement(['on_site', 'hybrid', 'remote']),
                'qualifications' => [fake()->slug(), fake()->slug()],
                'skills_req' => ["Python","Laravel","Arabic","English"],
                'salary' => fake()->numberBetween(500, 10000),
                'vacant' => 1,
                'job_hours' => fake()->numberBetween(8, 18)
            ]);
        }

        $user = User::create([
            'user_name'=> fake()->userName(),
            'email'=> fake()->email(),
            'password'=>'Aa123123',
            'roles_name'=> ['user', 'job_seeker'],
            'is_verified'=> 1,
        ]);
        $user->syncRoles(['user', 'job_seeker']);
        $seeker = Seeker::create([
            'user_id' => $user->id,
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'gender' => fake()->randomElement(['male', 'female']),
            'birth_day' => fake()->date(),
            'location' => fake()->country(),
            'skills' => ['Python', 'Laravel', 'React JS', 'English'],
            'certificates' => [fake()->word(), fake()->word()],
            'about' => fake()->paragraph(),
            'specialization' => fake()->word()
        ]);
        for ($i=0; $i < 10; $i++) {
            Post::create([
                'seeker_id' => $seeker->id,
                'body' => fake()->realText()
            ]);
        }
        $user = User::create([
            'user_name'=> fake()->userName(),
            'email'=> fake()->email(),
            'password'=>'Aa123123',
            'roles_name'=> ['user', 'company'],
            'is_verified'=> 1,
        ]);
        $user->syncRoles(['user', 'company']);
        $company = Company::create([
            'user_id'=>$user->id,
            'company_name'=>fake()->company(),
            'location'=>fake()->country(),
            'about' => fake()->paragraph(),
            'domain' => fake()->word(),
            'contact_info' => fake()->realText(50)
        ]);

            $company->image()->create(['url' => fake()->imageUrl(640, 480)]); {
            Opportunity::create([
                'company_id'=> $company->id,
                'title' => fake()->word(),
                'body' => fake()->sentence(),
                'location' => fake()->address(),
                'job_type' => fake()->randomElement(['full_time', 'part_time', 'contract', 'temporary', 'volunteer']),
                'work_place_type' => fake()->randomElement(['on_site', 'hybrid', 'remote']),
                'qualifications' => [fake()->slug(), fake()->slug()],
                'skills_req' => ["Python","Laravel","Arabic","English"],
                'salary' => fake()->numberBetween(500, 10000),
                'vacant' => 1,
                'job_hours' => fake()->numberBetween(8, 18)
            ]);
        }

        $user = User::create([
            'user_name'=> fake()->userName(),
            'email'=> fake()->email(),
            'password'=>'Aa123123',
            'roles_name'=> ['user', 'job_seeker'],
            'is_verified'=> 1,
        ]);
        $user->syncRoles(['user', 'job_seeker']);
        $seeker = Seeker::create([
            'user_id' => $user->id,
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'gender' => fake()->randomElement(['male', 'female']),
            'birth_day' => fake()->date(),
            'location' => fake()->country(),
            'skills' => ['Python', 'Laravel', 'React JS', 'English'],
            'certificates' => [fake()->word(), fake()->word()],
            'about' => fake()->paragraph(),
            'specialization' => fake()->word()
        ]);
        for ($i=0; $i < 10; $i++) {
            Post::create([
                'seeker_id' => $seeker->id,
                'body' => fake()->realText()
            ]);
        }
    }
}
