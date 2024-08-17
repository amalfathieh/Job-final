<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirectToProvider()
    {
        return Socialite::driver('google')->redirect();
    }
    public function handleCallback()
    {
        try {
            $google_user = Socialite::driver('google')->user();
            $user = User::where('google_id', $google_user->id)->first();
            if (!$user) {
                $user = User::Create([
                    'google_id' => $google_user->id,
                    'user_name' => $google_user->name,
                    'email' => $google_user->email,
                    'roles_name' => ['user'],
                    'is_verified' => 1,
                    'google_token' => $google_user->token,
                ]);
                $user->assignRole('user');
                Auth::login($user);
                return $user;
            } else {
                Auth::login($user);
                return $user;
            }
        } catch (\Exception $th) {
            return response()->json([
                'error' => $th->getMessage(),
            ]);
        }
    }
}
