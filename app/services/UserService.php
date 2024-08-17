<?php


namespace App\services;


use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserService
{
    public function storeEmployee($request,$user_id){
        return Employee::create([
            'user_id'=> $user_id,
            'first_name'=> $request['first_name'],
            'middle_name'=> $request['middle_name'],
            'last_name'=> $request['last_name'],
            'gender'=> $request['gender'],
            'birth_day'=> $request['birth_day'],
        ]);
    }

    public function updateUser( $request , $user ){
       $user->update([
            'user_name' =>$request['user_name'] ?? $user['user_name'],
            'email' =>$request['email'] ?? $user['email'],
            'password' =>bcrypt($request['password'] )?? $user['password'],
        ]);
       return $user;
    }

//    public function getTokensForFollowers(){
//        $user = User::find(Auth::user()->id);
//        $tokens = [];
//        $followers = $user->followers;
//
//        foreach($followers as $follower){
//            $tokens = array_merge($tokens , $follower->routeNotificationForFcm());
//        }
//        return $tokens;
//    }
}
