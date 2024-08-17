<?php

namespace App\Console\Commands;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RestoreUserRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'unban:restore-user-roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restore roles to users whose ban period has expired';


    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::onlyBanned()->get();
        foreach ($users as $user) {
            if ($user->bans[0]->expired_at < Carbon::now()) {
                $user->syncRoles($user->roles_name);
                $user->unBan();
            }
        }
    }
}
