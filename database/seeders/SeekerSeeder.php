<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\Seeker;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SeekerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i=0; $i < 10; $i++) {
            $skills = '';
            $specialization = fake()->randomElement([
                'Information Technology and Communications',
                'Health and Medicine',
                'Banking and Financial Services',
                'Engineering and Construction',
                'Market Research and Marketing',
                'Science and Technology',
                'Safety and Security',
                'Media and Publishing'
            ]);

            if ($specialization == 'Information Technology and Communications') {
                $skills = fake()->randomElements([
                    'Network configuration',
                    'Cybersecurity',
                    'Cloud computing',
                    'Technical writing',
                    'Data analytics'
                ], mt_rand(2, 5)); // Selects between 2 to 5 random skills
            }

            if ($specialization == 'Health and Medicine') {
                $skills = fake()->randomElements([
                    'Patient care',
                    'Medical coding',
                    'Pharmacology',
                    'Clinical research',
                    'Telemedicine'
                ], mt_rand(2, 5)); // Selects between 2 to 5 random skills
            }

            if ($specialization == 'Banking and Financial Services') {
                $skills = fake()->randomElements([
                    'Financial analysis',
                    'Risk management',
                    'Investment strategies',
                    'Accounting',
                    'Regulatory compliance'
                ], mt_rand(2, 5)); // Selects between 2 to 5 random skills
            }

            if ($specialization == 'Engineering and Construction') {
                $skills = fake()->randomElements([
                    'AutoCAD',
                    'Project management',
                    'Structural analysis',
                    'Safety standards',
                    'Cost estimation'
                ], mt_rand(2, 5)); // Selects between 2 to 5 random skills
            }

            if ($specialization == 'Market Research and Marketing') {
                $skills = fake()->randomElements([
                    'Data analysis',
                    'Consumer behavior',
                    'Survey design',
                    'Digital marketing',
                    'Statistical analysis'
                ], mt_rand(2, 5)); // Selects between 2 to 5 random skills
            }

            if ($specialization == 'Science and Technology') {
                $skills = fake()->randomElements([
                    'Laboratory techniques',
                    'Data modeling',
                    'Research methodologies',
                    'Technical writing',
                    'Innovation management'
                ], mt_rand(2, 5)); // Selects between 2 to 5 random skills
            }

            if ($specialization == 'Safety and Security') {
                $skills = fake()->randomElements([
                    'Threat assessment',
                    'Emergency response',
                    'Risk analysis',
                    'Surveillance systems',
                    'Crisis management'
                ], mt_rand(2, 5)); // Selects between 2 to 5 random skills
            }

            if ($specialization == 'Media and Publishing') {
                $skills = fake()->randomElements([
                    'Content creation',
                    'Editing',
                    'Digital publishing',
                    'SEO',
                    'Graphic design'
                ], mt_rand(2, 5)); // Selects between 2 to 5 random skills
            }

            if ($specialization == 'Information Technology and Communications') {
                $certificates = fake()->randomElements([
                    'CompTIA A+',
                    'Cisco Certified Network Associate (CCNA)',
                    'Certified Information Systems Security Professional (CISSP)',
                    'AWS Certified Solutions Architect',
                    'Certified Ethical Hacker (CEH)'
                ], mt_rand(2, 5));
            }

            if ($specialization == 'Health and Medicine') {
                $certificates = fake()->randomElements([
                    'Certified Coding Specialist (CCS)',
                    'Certified Medical Assistant (CMA)',
                    'Certified Nursing Assistant (CNA)',
                    'Certified Phlebotomy Technician (CPT)',
                    'Certified Clinical Research Coordinator (CCRC)'
                ], mt_rand(2, 5));
            }

            if ($specialization == 'Banking and Financial Services') {
                $certificates = fake()->randomElements([
                    'Chartered Financial Analyst (CFA)',
                    'Certified Public Accountant (CPA)',
                    'Certified Financial Planner (CFP)',
                    'Financial Risk Manager (FRM)',
                    'Certified Regulatory Compliance Manager (CRCM)'
                ], mt_rand(2, 5));
            }

            if ($specialization == 'Engineering and Construction') {
                $certificates = fake()->randomElements([
                    'Certified Construction Manager (CCM)',
                    'Project Management Professional (PMP)',
                    'LEED Green Associate',
                    'Certified Safety Manager (CSM)',
                    'National Council of Examiners for Engineering and Surveying (NCEES)'
                ], mt_rand(2, 5));
            }

            if ($specialization == 'Market Research and Marketing') {
                $certificates = fake()->randomElements([
                    'Professional Certified Marketer (PCM)',
                    'Certified Research Expert (CRE)',
                    'Google Data Analytics Professional Certificate',
                    'Certified Business Intelligence Professional (CBIP)',
                    'Professional Researcher Certification (PRC)'
                ], mt_rand(2, 5));
            }

            if ($specialization == 'Science and Technology') {
                $certificates = fake()->randomElements([
                    'Certified Lab Technician (CLT)',
                    'Certified Data Professional (CDP)',
                    'Certified Information Systems Security Professional (CISSP)',
                    'Certified Ethical Hacker (CEH)',
                    'Certified Cloud Security Professional (CCSP)'
                ], mt_rand(2, 5));
            }

            if ($specialization == 'Safety and Security') {
                $certificates = fake()->randomElements([
                    'Certified Safety Professional (CSP)',
                    'Certified Protection Professional (CPP)',
                    'Certified Information Systems Security Professional (CISSP)',
                    'Certified Emergency Manager (CEM)',
                    'Certified Security Project Manager (CSPM)'
                ], mt_rand(2, 5));
            }

            if ($specialization == 'Media and Publishing') {
                $certificates = fake()->randomElements([
                    'Certified Digital Marketing Professional (CDMP)',
                    'Google Analytics Certification',
                    'Certified Content Marketing Specialist (CCMS)',
                    'Certified Social Media Marketing Specialist (CSMMS)',
                    'Certified Graphic Designer (CGD)'
                ], mt_rand(2, 5));
            }

            $user = User::create([
                'user_name'=> fake()->userName(),
                'email'=> fake()->unique()->safeEmail(),
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
                'skills' => $skills,
                'certificates' => $certificates,
                'about' => fake()->realText('60'),
                'specialization' => $specialization
            ]);

            $image = fake()->image('public/images/job_seeker/profilePhoto', 640, 480, null, false);
            $path = 'images/job_seeker/profilePhoto/' . $image;
            $seeker->image()->create([
                'url' => $path
            ]);

            for ($i=0; $i < 10; $i++) {
                $post = Post::create([
                    'seeker_id' => $seeker->id,
                    'body' => fake()->realText()
                ]);
                $image = fake()->image('public/images/job_seeker/posts', 640, 480, null, false);
                $path = 'images/job_seeker/posts/' . $image;
                $post->images()->create([
                    'url' => $path
                ]);
                $image = fake()->image('public/images/job_seeker/posts', 640, 480, null, false);
                $path = 'images/job_seeker/posts/' . $image;
                $post->images()->create([
                    'url' => $path
                ]);
                $image = fake()->image('public/images/job_seeker/posts', 640, 480, null, false);
                $path = 'images/job_seeker/posts/' . $image;
                $post->images()->create([
                    'url' => $path
                ]);
            }
        }

    }
}
