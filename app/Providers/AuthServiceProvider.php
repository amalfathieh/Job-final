<?php

namespace App\Providers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {

        // Passport::refreshTokensExpireIn(Carbon::now()->addDays(30));
        // Passport::personalAccessTokensExpireIn(Carbon::now()->addSeconds(40));
        
        Gate::define('isCompany', function ($user) {
            foreach ($user->roles_name as $role) {
                if ($role === 'company') {
                    return true;
                }
            }
        });

        Gate::define('isJobSeeker', function ($user) {
            foreach ($user->roles_name as $role) {
                if ($role === 'job_seeker') {
                    return true;
                }
            }
        });

        Gate::define('isVerified', function ($user) {
            return $user->is_verified === 1;
        });

    }
}
