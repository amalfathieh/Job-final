<?php

namespace App\Http\Controllers;

use App\Http\Resources\FollowerResource;
use App\Models\User;
use App\Traits\responseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowController extends Controller
{
    use responseTrait;

    public function follow($user_id){
        $user = User::query()->find($user_id);
        if($user_id == Auth::user()->id){
            return $this->apiResponse(null,__('strings.authorization_required'),400);
        }

        if($user) {
            $follower_id = Auth::user()->id;

            if ($user->followers()->where('followee_id', $follower_id)->exists()) {
                return $this->unFollowUser($user);
            }

            return $this->followUser($user);
        }

        return $this->apiResponse(null,__('strings.not_found'),400);
    }

    public function followUser($user)
    {
        $user->followers()->attach(auth()->user()->id);
        return $this->apiResponse(null, __('strings.follow_success'),200);
    }

    public function unFollowUser($user)
    {
        $user->followers()->detach(auth()->user()->id);
        return $this->apiResponse(null, __('strings.unfollow_success'),200);
    }


    public function showFollowers($userId)
    {
        $user = User::find($userId);
        if ($user->hasRole('company')) {
            $data['type']='company';
        }
        if ($user->hasRole('job_seeker')) {
            $data['type']='job_seeker';
        }
        $followers = $user->followers;
        $followers = $followers->reject(function (User $user){
            return $user->isBanned();
        });
        $data['count'] = $followers->count();
        $data['followers'] = FollowerResource::collection($followers);
        return $this->apiResponse($data,__('strings.success'),200);
    }

    public function showFollowings($userId)
    {
        $user = User::find($userId);
        if ($user->hasRole('company')) {
            $data['type']='company';
        }
        if ($user->hasRole('job_seeker')) {
            $data['type']='job_seeker';
        }
        $followings  = $user->followings;
        $followings = $followings->reject(function (User $user){
            return $user->isBanned();
        });
        $data['count']=$followings->count();
        $data['followings'] =FollowerResource::collection($followings);
        return $this->apiResponse($data,__('strings.success'),200);
    }

}
