<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       // app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            //Admin employee
            'role control',
            'admin report control',

            'logs view',
            'employee control', 'employee view',

            'block user',

            'news create', 'news view', 'news delete',

            'user view','user edit', 'user delete',

            //profile seeker
            'seeker create',
            //profile company
            'company create',
           //
            'user report create', 'user report view', 'user report delete', 'response for report',

            'opportunity create', 'opportunities view', 'opportunity delete',

            'post create', 'posts view', 'post delete',

            'request create', 'request view', 'request edit', 'status edit', 'request delete',
            ];
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        $owner = Role::create(['name' => 'owner'])->givePermissionTo($permissions);

        $userRole = Role::create(['name' => 'user'])->givePermissionTo([
            'user report create', 'news view','posts view', 'opportunities view','user view','user delete','user edit',
        ]);

        $companyRole = Role::create(['name' => 'company'])->givePermissionTo([
            'company create',
            'opportunity create', 'opportunity delete',
            'request view', 'status edit', 'request delete',
        ]);

        $jobSeekerRole = Role::create(['name' => 'job_seeker'])->givePermissionTo([
            'seeker create',
            'post create', 'post delete',
            'request create', 'request view', 'request edit', 'request delete',
        ]);

        $employeeRole = Role::create(['name' => 'employee'])->givePermissionTo([
            'user report create', 'news view', 'posts view', 'opportunities view', 'user view', 'user delete', 'user edit',
        ]);

        $techSupportTeam = Role::create(['name' => 'tech_support_team'])->givePermissionTo([
            'post delete',
            'block user',
            'user report view', 'user report delete',
            'logs view'
        ]);
    }
}

