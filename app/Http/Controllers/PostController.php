<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use App\Models\Post;
use App\Models\Seeker;
use App\Models\User;
use App\Notifications\SendNotification;
use App\services\FileService;
use App\services\PostService;
use App\services\UserService;
use App\Traits\NotificationTrait;
use App\Traits\responseTrait;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

use function PHPUnit\Framework\isEmpty;

class PostController extends Controller
{
    use responseTrait, NotificationTrait;
    protected $postService;
public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    public function create(PostRequest $request){
        $user = User::find(Auth::user()->id);
        $seeker = $user->seeker;

        $post = $this->postService->store(
            $seeker->id, $request->body, $request->file('images'), $request->file('files')
        );

        $followers = $user->followers;
        if ($followers && count($followers) > 0) {
            $tokens = [];
            foreach($followers as $follower){
                $tokens = array_merge($tokens , $follower->routeNotificationForFcm());
            }
            $data =[
                'obj_id'=> $post->id,
                'title'=> 'New Post',
                'body'=> 'New post has been published by '. $seeker->first_name,
            ];

            Notification::send($followers,new SendNotification($data));
           $this->sendPushNotification($data['title'],$data['body'],$tokens);
        }
        return $this->apiResponse(new PostResource($post), __('strings.post_added'), 201);
    }

    public function update(Request $request, $post_id){
        $post = Post::find($post_id);
        $user = User::where('id', Auth::user()->id)->first();
        if (!is_null($post)) {
            if( $post['seeker_id'] == $user->seeker->id ) {
                $post = $this->postService->edit($request, $post);
                return $this->apiResponse(new PostResource($post),__('strings.updated_successfully'),200);
            }
            return $this->apiResponse(null,__('strings.authorization_required'),403);
        }
        return $this->apiResponse(null, __('strings.not_found'), 404);
    }

    public function delete($id, FileService $fileService){
        $post = Post::find($id);
        $user = User::where('id', Auth::user()->id)->first();
        if ($post) {
            if (($user->hasRole('job_seeker') && $post['seeker_id'] == $user->seeker->id) || (($user->hasRole('employee') || $user->hasRole('owner')) && $user->can('post delete'))) {
                $images = $post->images;
                $files = $post->files;
                if (!is_null($images)) {
                    foreach ($images as $value) {
                        $fileService->delete($value->url);
                    }
                    $post->images()->delete();
                }
                if (!is_null($files)) {
                    foreach ($files as $value) {
                        $fileService->delete($value->url);
                    }
                    $post->files()->delete();
                }
                $post->delete();
                return $this->apiResponse(null, __('strings.deleted_successfully'), 200);
            }
            return $this->apiResponse(null,__('strings.authorization_required'),403);
        }
        return $this->apiResponse(null, __('strings.not_found'), 404);
    }

    public function deleteImage($post_id, $img_id, FileService $fileService) {
        $post = Post::where('id', $post_id)->first();

        $img = $post->images()->where('id', $img_id)->first();
        if ($img) {
            $fileService->delete($img->url);
            $img->delete();
            return $this->apiResponse(new PostResource($post), 'Deleted successfully', 200);
        }
        return $this->apiResponse(null, __('strings.not_found'), 404);
    }

    public function deleteFile($post_id, $file_id, FileService $fileService) {
        $post = Post::where('id', $post_id)->first();

        $file = $post->files()->where('id', $file_id)->first();
        if ($file) {
            $fileService->delete($file->url);
            $file->delete();
            return $this->apiResponse(new PostResource($post), 'Deleted successfully', 200);
        }
        return $this->apiResponse(null, __('strings.not_found'), 404);
    }

    public function allPosts(){
        $userId = Auth::user()->id;
        $posts = Post::select('posts.*')->addSelect(DB::raw("EXISTS(SELECT 1 FROM followers WHERE followers.follower_id = posts.seeker_id AND followers.followee_id = $userId) AS is_followed"))
            ->orderByDesc('is_followed')
            ->latest()
            ->get();

        $posts = $posts->reject(function (Post $post){
            return $post->seeker->user->isBanned();
        });
        return $this->apiResponse(PostResource::collection($posts),__('strings.all_posts'),200);
    }

    public function userPosts($id) {
        $seeker = Seeker::where('user_id', $id)->first();
        if ($seeker) {
            $posts = Post::where('seeker_id', $seeker->id)->get();
            $posts = PostResource::collection($posts);
            return $this->apiResponse($posts, __('strings.user_posts'), 200);
        }
        return $this->apiResponse(null, __('strings.not_found'), 404);
    }

    public function postInfo($id) {
        $post = Post::where('id', $id)->first();
        if ($post) {
            $data['post'] = new PostResource($post);
            $data['owner'] = new UserResource(User::where('id', Post::where('id', $id)->first()->seeker->user->id)->first());
            return $this->apiResponse($data, __('strings.post_info'), 200);
        }
        return $this->apiResponse(null, __('strings.not_found'), 200);
    }
}
