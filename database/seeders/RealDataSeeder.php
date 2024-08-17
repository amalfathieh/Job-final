<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Opportunity;
use App\Models\User;
use Illuminate\Database\Seeder;


class RealDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void {

        for ($i=0; $i < 10; $i++) {
            $role = 'company';
            $body = '';
            $domain = fake()->randomElement([
                'Information Technology and Communications',
                'Health and Medicine',
                'Banking and Financial Services',
                'Engineering and Construction',
                'Market Research and Marketing',
                'Science and Technology',
                'Safety and Security',
                'Media and Publishing'
            ]);
            $qualifications = [];
            $opp_name = '';
            $skills_req = [];
            $user = User::create([
                'user_name'=> fake()->userName(),
                'email'=> fake()->unique()->safeEmail(),
                'password'=>'Aa123123',
                'roles_name'=> ['user', $role],
                'is_verified'=> 1,
            ]);
            $user->syncRoles(['user', 'company']);
                $company = Company::create([
                    'user_id' => $user->id,
                    'company_name' => fake()->company(),
                    'location'=>fake()->country(),
                    'about' => fake()->realText(60),
                    'domain' => $domain,
                ]);
                $image = fake()->image('public/images/Company/Logos', 640, 480, null, false);
                $path = 'images/Company/Logos/' . $image;
                $user->contactInfo()->create([
                    'email' => $user->email,
                    'phone' => fake()->phoneNumber(),
                    'website' => fake()->url()
                ]);
                $company->image()->create([
                    'url' => $path
                ]);
                    if ($domain == 'Information Technology and Communications') {
                        $opp_name = fake()->randomElement([
                            'Software Developer',
                            'Data Scientist',
                            'Chief Automation Officer',
                            'Human-Machine Matchmaker'
                        ]);
                    } elseif ($domain == 'Health and Medicine') {
                        $opp_name = fake()->randomElement([
                            'Nurse Practitioner',
                            'Physician Assistant',
                            'Dentist',
                            'Virtual Health Coach'
                        ]);
                    } elseif ($domain == 'Banking and Financial Services') {
                        $opp_name = fake()->randomElement([
                            'Financial Manager'
                        ]);
                    } elseif ($domain == 'Engineering and Construction') {
                        $opp_name = fake()->randomElement([
                            'Civil Engineer',
                            'Mechanical Engineer',
                            'Electrical Engineer'
                        ]);
                    } elseif ($domain == 'Market Research and Marketing') {
                        $opp_name = fake()->randomElement([
                            'Marketing Manager'
                        ]);
                    } elseif ($domain == 'Science and Technology') {
                        $opp_name = fake()->randomElement([
                            'Metaverse Research Scientist',
                            'AI Ethicist',
                            'Quantum Computing Scientist',
                            'Climate Impact Analyst'
                        ]);
                    } elseif ($domain == 'Safety and Security') {
                        $opp_name = fake()->randomElement([
                            'Cybersecurity Threat Attribution Analyst'
                        ]);
                    } elseif ($domain == 'Media and Publishing') {
                        $opp_name = fake()->randomElement([
                            'Digital Reputation Defender'
                        ]);
                    }
                    if ($opp_name == 'Software Developer') {
                        $qualifications = [
                            'Bachelor\'s degree in Computer Science or related field',
                            'Proficiency in programming languages (e.g., Java, Python, C++)',
                            'Experience with software development frameworks and tools',
                            'Problem-solving skills'
                        ];
                        $skills_req = [
                            'Programming languages',
                            'Problem-solving',
                            'Debugging and testing',
                            'Version control',
                            'Team collaboration'
                        ];
                        $body = 'Designs, codes, and tests software applications.';
                    } elseif ($opp_name == 'Nurse Practitioner') {
                        $qualifications = [
                            'Master\'s degree in Nursing',
                            'Nurse Practitioner certification',
                            'Clinical experience in a healthcare setting',
                            'Strong communication and patient care skills'
                        ];
                        $skills_req = [
                            'Clinical skills',
                            'Patient care',
                            'Medical knowledge',
                            'Communication',
                            'Empathy'
                        ];
                        $body = 'Provides advanced nursing care and can prescribe medications.';
                    } elseif ($opp_name == 'Financial Manager') {
                        $qualifications = [
                            'Bachelor\'s degree in Finance, Accounting, or related field',
                            'CPA or CFA certification (preferred)',
                            'Experience in financial planning and analysis',
                            'Strong analytical and leadership skills'
                        ];
                        $skills_req = [
                            'Financial analysis',
                            'Budgeting',
                            'Risk management',
                            'Leadership',
                            'Strategic planning'
                        ];
                        $body = 'Oversees financial activities and strategies of an organization.';
                    } elseif ($opp_name == 'Data Scientist') {
                        $qualifications = [
                            'Bachelor\'s or Master\'s degree in Data Science, Statistics, or related field',
                            'Proficiency in data analysis tools (e.g., Python, R)',
                            'Experience with machine learning algorithms',
                            'Strong analytical and problem-solving skills'
                        ];
                        $skills_req = [
                            'Data analysis',
                            'Machine learning',
                            'Statistical analysis',
                            'Programming',
                            'Data visualization'
                        ];
                        $body = 'Analyzes complex data to help make business decisions.';
                    } elseif ($opp_name == 'Marketing Manager') {
                        $qualifications = [
                            'Bachelor\'s degree in Marketing, Business, or related field',
                            'Experience in marketing strategy and campaign management',
                            'Strong communication and leadership skills',
                            'Knowledge of digital marketing tools and techniques'
                        ];
                        $skills_req = [
                            'Marketing strategy',
                            'Digital marketing',
                            'Content creation',
                            'SEO/SEM',
                            'Communication'
                        ];
                        $body = 'Plans and executes marketing strategies to promote products or services.';
                    } elseif ($opp_name == 'Civil Engineer') {
                        $qualifications = [
                            'Bachelor\'s degree in Civil Engineering',
                            'Professional Engineer (PE) license (preferred)',
                            'Experience in construction project management',
                            'Strong analytical and problem-solving skills'
                        ];
                        $skills_req = [
                            'Structural analysis',
                            'Project management',
                            'CAD software',
                            'Problem-solving',
                            'Attention to detail'
                        ];
                        $body = 'Designs and supervises construction projects like roads and bridges.';
                    } elseif ($opp_name == 'Mechanical Engineer') {
                        $qualifications = [
                            'Bachelor\'s degree in Mechanical Engineering',
                            'Experience with CAD software and mechanical design',
                            'Strong analytical and problem-solving skills',
                            'Knowledge of manufacturing processes'
                        ];
                        $skills_req = [
                            'Mechanical design',
                            'CAD software',
                            'Problem-solving',
                            'Attention to detail'
                        ];
                        $body = 'Designs and develops mechanical systems and devices.';
                    } elseif ($opp_name == 'Electrical Engineer') {
                        $qualifications = [
                            'Bachelor\'s degree in Electrical Engineering',
                            'Experience with electrical systems design and analysis',
                            'Strong analytical and problem-solving skills',
                            'Knowledge of industry standards and regulations'
                        ];
                        $skills_req = [
                            'Circuit design',
                            'Electrical systems',
                            'Problem-solving',
                            'Analytical skills',
                            'Project management'
                        ];
                        $body = 'Designs and tests electrical systems and equipment.';
                    }  if ($opp_name == 'Physician Assistant') {
                        $qualifications = [
                            'Master\'s degree in Physician Assistant Studies',
                            'Physician Assistant certification',
                            'Clinical experience in a healthcare setting',
                            'Strong communication and patient care skills'
                        ];
                        $skills_req = [
                            'Clinical skills',
                            'Patient care',
                            'Medical knowledge',
                            'Communication',
                            'Empathy'
                        ];
                        $body = 'Provides medical care under the supervision of a physician.';
                    } elseif ($opp_name == 'Dentist') {
                        $qualifications = [
                            'Doctor of Dental Surgery (DDS) or Doctor of Medicine in Dentistry (DMD)',
                            'State dental license',
                            'Clinical experience in dentistry',
                            'Strong manual dexterity and patient care skills'
                        ];
                        $skills_req = [
                            'Dental procedures',
                            'Patient care',
                            'Manual dexterity',
                            'Communication',
                            'Attention to detail'
                        ];
                        $body = 'Diagnoses and treats issues related to teeth and gums.';
                    } elseif ($opp_name == 'Chief Automation Officer') {
                        $qualifications = [
                            'MBA/Master\'s degree',
                            'Experience in automation',
                            'Strong leadership skills'
                        ];
                        $skills_req = [
                            'Strategy',
                            'Leadership',
                            'Change management',
                            'Technological expertise (automation and AI)',
                            'Business acumen',
                            'Communication'
                        ];
                        $body = 'Leads automation initiatives to improve productivity and work-life balance.';
                    } elseif ($opp_name == 'Metaverse Research Scientist') {
                        $qualifications = [
                            'PhD in Computer Science or related field',
                            'Experience in AR/VR/MR technologies',
                            'Strong research skills'
                        ];
                        $skills_req = [
                            'Extended reality (XR) technologies',
                            'Research and development',
                            'Programming',
                            'Problem-solving',
                            'Creativity'
                        ];
                        $body = 'Designs and develops virtual environments and experiences.';
                    } elseif ($opp_name == 'Human-Machine Matchmaker') {
                        $qualifications = [
                            'Bachelor\'s degree in Human-Computer Interaction or related field',
                            'Experience with AI and robotics',
                            'Strong analytical skills'
                        ];
                        $skills_req = [
                            'Human-computer interaction',
                            'AI and robotics',
                            'Data analysis',
                            'Communication',
                            'Problem-solving'
                        ];
                        $body = 'Facilitates effective collaboration between humans and machines.';
                    } elseif ($opp_name == 'AI Ethicist') {
                        $qualifications = [
                            'Master\'s degree in Ethics, Philosophy, or related field',
                            'Experience with AI technologies',
                            'Strong ethical reasoning skills'
                        ];
                        $skills_req = [
                            'Ethical reasoning',
                            'AI technologies',
                            'Policy development',
                            'Communication',
                            'Critical thinking'
                        ];
                        $body = 'Ensures ethical considerations are integrated into AI development and deployment.';
                    } elseif ($opp_name == 'Cybersecurity Threat Attribution Analyst') {
                        $qualifications = [
                            'Bachelor\'s degree in Cybersecurity or related field',
                            'Experience in threat analysis',
                            'Strong analytical skills'
                        ];
                        $skills_req = [
                            'Cybersecurity',
                            'Threat analysis',
                            'Forensics',
                            'Problem-solving',
                            'Attention to detail'
                        ];
                        $body = 'Identifies and attributes sources of cybersecurity threats.';
                    } elseif ($opp_name == 'Digital Reputation Defender') {
                        $qualifications = [
                            'Bachelor\'s degree in Public Relations or related field',
                            'Experience in digital marketing',
                            'Strong communication skills'
                        ];
                        $skills_req = [
                            'Digital marketing',
                            'Crisis management',
                            'Communication',
                            'Social media management',
                            'Analytical skills'
                        ];
                        $body = 'Manages and protects the online reputation of individuals and organizations.';
                    } elseif ($opp_name == 'Climate Impact Analyst') {
                        $qualifications = [
                            'Bachelor\'s degree in Environmental Science or related field',
                            'Experience in climate analysis',
                            'Strong research skills'
                        ];
                        $skills_req = [
                            'Climate science',
                            'Data analysis',
                            'Research',
                            'Problem-solving',
                            'Communication'
                        ];
                        $body = 'Analyzes the impact of climate change and develops mitigation strategies.';
                    } elseif ($opp_name == 'Virtual Health Coach') {
                        $qualifications = [
                            'Bachelor\'s degree in Health Sciences or related field',
                            'Certification in health coaching',
                            'Experience in virtual coaching'
                        ];
                        $skills_req = [
                            'Health coaching',
                            'Communication',
                            'Motivational skills',
                            'Technology proficiency',
                            'Empathy'
                        ];
                        $body = 'Provides health and wellness coaching through virtual platforms.';
                    } elseif ($opp_name == 'Quantum Computing Scientist') {
                        $qualifications = [
                            'PhD in Physics, Computer Science, or related field',
                            'Experience in quantum computing',
                            'Strong research skills'
                        ];
                        $skills_req = [
                            'Quantum computing',
                            'Research and development',
                            'Programming',
                            'Problem-solving',
                            'Analytical skills'
                        ];
                        $body = 'Conducts research and development in quantum computing technologies.';
                    }
                    $opp = Opportunity::create([
                        'company_id'=> $user->company->id,
                        'title' => $opp_name,
                        'body' => $body,
                        'location' => fake()->country(),
                        'job_type' => fake()->randomElement(['full_time', 'part_time', 'contract', 'temporary', 'volunteer']),
                        'work_place_type' => fake()->randomElement(['on_site', 'hybrid', 'remote']),
                        'qualifications' => $qualifications,
                        'skills_req' => $skills_req,
                        'salary' => fake()->numberBetween(500, 10000),
                        'vacant' => 1,
                        'job_hours' => fake()->numberBetween(8, 18)
                    ]);
                        $image = fake()->image('public/images/Company/Opportunities', 640, 480, null, false);
                        $path = 'images/Company/Opportunities/' . $image;
                        $opp->images()->create([
                            'url' => $path
                        ]);
                        $image = fake()->image('public/images/Company/Opportunities', 640, 480, null, false);
                        $path = 'images/Company/Opportunities/' . $image;
                        $opp->images()->create([
                            'url' => $path
                        ]);
                        $image = fake()->image('public/images/Company/Opportunities', 640, 480, null, false);
                        $path = 'images/Company/Opportunities/' . $image;
                        $opp->images()->create([
                            'url' => $path
                        ]);
                        if ($domain == 'Information Technology and Communications') {
                            $opp_name = fake()->randomElement([
                                'Software Developer',
                                'Data Scientist',
                                'Chief Automation Officer',
                                'Human-Machine Matchmaker'
                            ]);
                        } elseif ($domain == 'Health and Medicine') {
                            $opp_name = fake()->randomElement([
                                'Nurse Practitioner',
                                'Physician Assistant',
                                'Dentist',
                                'Virtual Health Coach'
                            ]);
                        } elseif ($domain == 'Banking and Financial Services') {
                            $opp_name = fake()->randomElement([
                                'Financial Manager'
                            ]);
                        } elseif ($domain == 'Engineering and Construction') {
                            $opp_name = fake()->randomElement([
                                'Civil Engineer',
                                'Mechanical Engineer',
                                'Electrical Engineer'
                            ]);
                        } elseif ($domain == 'Market Research and Marketing') {
                            $opp_name = fake()->randomElement([
                                'Marketing Manager'
                            ]);
                        } elseif ($domain == 'Science and Technology') {
                            $opp_name = fake()->randomElement([
                                'Metaverse Research Scientist',
                                'AI Ethicist',
                                'Quantum Computing Scientist',
                                'Climate Impact Analyst'
                            ]);
                        } elseif ($domain == 'Safety and Security') {
                            $opp_name = fake()->randomElement([
                                'Cybersecurity Threat Attribution Analyst'
                            ]);
                        } elseif ($domain == 'Media and Publishing') {
                            $opp_name = fake()->randomElement([
                                'Digital Reputation Defender'
                            ]);
                        }
                        if ($opp_name == 'Software Developer') {
                            $qualifications = [
                                'Bachelor\'s degree in Computer Science or related field',
                                'Proficiency in programming languages (e.g., Java, Python, C++)',
                                'Experience with software development frameworks and tools',
                                'Problem-solving skills'
                            ];
                            $skills_req = [
                                'Programming languages',
                                'Problem-solving',
                                'Debugging and testing',
                                'Version control',
                                'Team collaboration'
                            ];
                            $body = 'Designs, codes, and tests software applications.';
                        } elseif ($opp_name == 'Nurse Practitioner') {
                            $qualifications = [
                                'Master\'s degree in Nursing',
                                'Nurse Practitioner certification',
                                'Clinical experience in a healthcare setting',
                                'Strong communication and patient care skills'
                            ];
                            $skills_req = [
                                'Clinical skills',
                                'Patient care',
                                'Medical knowledge',
                                'Communication',
                                'Empathy'
                            ];
                            $body = 'Provides advanced nursing care and can prescribe medications.';
                        } elseif ($opp_name == 'Financial Manager') {
                            $qualifications = [
                                'Bachelor\'s degree in Finance, Accounting, or related field',
                                'CPA or CFA certification (preferred)',
                                'Experience in financial planning and analysis',
                                'Strong analytical and leadership skills'
                            ];
                            $skills_req = [
                                'Financial analysis',
                                'Budgeting',
                                'Risk management',
                                'Leadership',
                                'Strategic planning'
                            ];
                            $body = 'Oversees financial activities and strategies of an organization.';
                        } elseif ($opp_name == 'Data Scientist') {
                            $qualifications = [
                                'Bachelor\'s or Master\'s degree in Data Science, Statistics, or related field',
                                'Proficiency in data analysis tools (e.g., Python, R)',
                                'Experience with machine learning algorithms',
                                'Strong analytical and problem-solving skills'
                            ];
                            $skills_req = [
                                'Data analysis',
                                'Machine learning',
                                'Statistical analysis',
                                'Programming',
                                'Data visualization'
                            ];
                            $body = 'Analyzes complex data to help make business decisions.';
                        } elseif ($opp_name == 'Marketing Manager') {
                            $qualifications = [
                                'Bachelor\'s degree in Marketing, Business, or related field',
                                'Experience in marketing strategy and campaign management',
                                'Strong communication and leadership skills',
                                'Knowledge of digital marketing tools and techniques'
                            ];
                            $skills_req = [
                                'Marketing strategy',
                                'Digital marketing',
                                'Content creation',
                                'SEO/SEM',
                                'Communication'
                            ];
                            $body = 'Plans and executes marketing strategies to promote products or services.';
                        } elseif ($opp_name == 'Civil Engineer') {
                            $qualifications = [
                                'Bachelor\'s degree in Civil Engineering',
                                'Professional Engineer (PE) license (preferred)',
                                'Experience in construction project management',
                                'Strong analytical and problem-solving skills'
                            ];
                            $skills_req = [
                                'Structural analysis',
                                'Project management',
                                'CAD software',
                                'Problem-solving',
                                'Attention to detail'
                            ];
                            $body = 'Designs and supervises construction projects like roads and bridges.';
                        } elseif ($opp_name == 'Mechanical Engineer') {
                            $qualifications = [
                                'Bachelor\'s degree in Mechanical Engineering',
                                'Experience with CAD software and mechanical design',
                                'Strong analytical and problem-solving skills',
                                'Knowledge of manufacturing processes'
                            ];
                            $skills_req = [
                                'Mechanical design',
                                'CAD software',
                                'Problem-solving',
                                'Attention to detail'
                            ];
                            $body = 'Designs and develops mechanical systems and devices.';
                        } elseif ($opp_name == 'Electrical Engineer') {
                            $qualifications = [
                                'Bachelor\'s degree in Electrical Engineering',
                                'Experience with electrical systems design and analysis',
                                'Strong analytical and problem-solving skills',
                                'Knowledge of industry standards and regulations'
                            ];
                            $skills_req = [
                                'Circuit design',
                                'Electrical systems',
                                'Problem-solving',
                                'Analytical skills',
                                'Project management'
                            ];
                            $body = 'Designs and tests electrical systems and equipment.';
                        }  if ($opp_name == 'Physician Assistant') {
                            $qualifications = [
                                'Master\'s degree in Physician Assistant Studies',
                                'Physician Assistant certification',
                                'Clinical experience in a healthcare setting',
                                'Strong communication and patient care skills'
                            ];
                            $skills_req = [
                                'Clinical skills',
                                'Patient care',
                                'Medical knowledge',
                                'Communication',
                                'Empathy'
                            ];
                            $body = 'Provides medical care under the supervision of a physician.';
                        } elseif ($opp_name == 'Dentist') {
                            $qualifications = [
                                'Doctor of Dental Surgery (DDS) or Doctor of Medicine in Dentistry (DMD)',
                                'State dental license',
                                'Clinical experience in dentistry',
                                'Strong manual dexterity and patient care skills'
                            ];
                            $skills_req = [
                                'Dental procedures',
                                'Patient care',
                                'Manual dexterity',
                                'Communication',
                                'Attention to detail'
                            ];
                            $body = 'Diagnoses and treats issues related to teeth and gums.';
                        } elseif ($opp_name == 'Chief Automation Officer') {
                            $qualifications = [
                                'MBA/Master\'s degree',
                                'Experience in automation',
                                'Strong leadership skills'
                            ];
                            $skills_req = [
                                'Strategy',
                                'Leadership',
                                'Change management',
                                'Technological expertise (automation and AI)',
                                'Business acumen',
                                'Communication'
                            ];
                            $body = 'Leads automation initiatives to improve productivity and work-life balance.';
                        } elseif ($opp_name == 'Metaverse Research Scientist') {
                            $qualifications = [
                                'PhD in Computer Science or related field',
                                'Experience in AR/VR/MR technologies',
                                'Strong research skills'
                            ];
                            $skills_req = [
                                'Extended reality (XR) technologies',
                                'Research and development',
                                'Programming',
                                'Problem-solving',
                                'Creativity'
                            ];
                            $body = 'Designs and develops virtual environments and experiences.';
                        } elseif ($opp_name == 'Human-Machine Matchmaker') {
                            $qualifications = [
                                'Bachelor\'s degree in Human-Computer Interaction or related field',
                                'Experience with AI and robotics',
                                'Strong analytical skills'
                            ];
                            $skills_req = [
                                'Human-computer interaction',
                                'AI and robotics',
                                'Data analysis',
                                'Communication',
                                'Problem-solving'
                            ];
                            $body = 'Facilitates effective collaboration between humans and machines.';
                        } elseif ($opp_name == 'AI Ethicist') {
                            $qualifications = [
                                'Master\'s degree in Ethics, Philosophy, or related field',
                                'Experience with AI technologies',
                                'Strong ethical reasoning skills'
                            ];
                            $skills_req = [
                                'Ethical reasoning',
                                'AI technologies',
                                'Policy development',
                                'Communication',
                                'Critical thinking'
                            ];
                            $body = 'Ensures ethical considerations are integrated into AI development and deployment.';
                        } elseif ($opp_name == 'Cybersecurity Threat Attribution Analyst') {
                            $qualifications = [
                                'Bachelor\'s degree in Cybersecurity or related field',
                                'Experience in threat analysis',
                                'Strong analytical skills'
                            ];
                            $skills_req = [
                                'Cybersecurity',
                                'Threat analysis',
                                'Forensics',
                                'Problem-solving',
                                'Attention to detail'
                            ];
                            $body = 'Identifies and attributes sources of cybersecurity threats.';
                        } elseif ($opp_name == 'Digital Reputation Defender') {
                            $qualifications = [
                                'Bachelor\'s degree in Public Relations or related field',
                                'Experience in digital marketing',
                                'Strong communication skills'
                            ];
                            $skills_req = [
                                'Digital marketing',
                                'Crisis management',
                                'Communication',
                                'Social media management',
                                'Analytical skills'
                            ];
                            $body = 'Manages and protects the online reputation of individuals and organizations.';
                        } elseif ($opp_name == 'Climate Impact Analyst') {
                            $qualifications = [
                                'Bachelor\'s degree in Environmental Science or related field',
                                'Experience in climate analysis',
                                'Strong research skills'
                            ];
                            $skills_req = [
                                'Climate science',
                                'Data analysis',
                                'Research',
                                'Problem-solving',
                                'Communication'
                            ];
                            $body = 'Analyzes the impact of climate change and develops mitigation strategies.';
                        } elseif ($opp_name == 'Virtual Health Coach') {
                            $qualifications = [
                                'Bachelor\'s degree in Health Sciences or related field',
                                'Certification in health coaching',
                                'Experience in virtual coaching'
                            ];
                            $skills_req = [
                                'Health coaching',
                                'Communication',
                                'Motivational skills',
                                'Technology proficiency',
                                'Empathy'
                            ];
                            $body = 'Provides health and wellness coaching through virtual platforms.';
                        } elseif ($opp_name == 'Quantum Computing Scientist') {
                            $qualifications = [
                                'PhD in Physics, Computer Science, or related field',
                                'Experience in quantum computing',
                                'Strong research skills'
                            ];
                            $skills_req = [
                                'Quantum computing',
                                'Research and development',
                                'Programming',
                                'Problem-solving',
                                'Analytical skills'
                            ];
                            $body = 'Conducts research and development in quantum computing technologies.';
                        }
                        $opp = Opportunity::create([
                            'company_id'=> $user->company->id,
                            'title' => $opp_name,
                            'body' => $body,
                            'location' => fake()->country(),
                            'job_type' => fake()->randomElement(['full_time', 'part_time', 'contract', 'temporary', 'volunteer']),
                            'work_place_type' => fake()->randomElement(['on_site', 'hybrid', 'remote']),
                            'qualifications' => $qualifications,
                            'skills_req' => $skills_req,
                            'salary' => fake()->numberBetween(500, 10000),
                            'vacant' => 1,
                            'job_hours' => fake()->numberBetween(8, 18)
                        ]);
                            $image = fake()->image('public/images/Company/Opportunities', 640, 480, null, false);
                            $path = 'images/Company/Opportunities/' . $image;
                            $opp->images()->create([
                                'url' => $path
                            ]);
                            $image = fake()->image('public/images/Company/Opportunities', 640, 480, null, false);
                            $path = 'images/Company/Opportunities/' . $image;
                            $opp->images()->create([
                                'url' => $path
                            ]);
                            $image = fake()->image('public/images/Company/Opportunities', 640, 480, null, false);
                            $path = 'images/Company/Opportunities/' . $image;
                            $opp->images()->create([
                                'url' => $path
                            ]);
                }
            }
        }
