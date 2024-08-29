<?php

namespace App\Http\Controllers;

use App\Http\Resources\OpportunityResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use App\Models\Apply;
use App\Models\Opportunity;
use App\Models\Post;
use App\Models\Report;
use App\Models\User;
use App\Notifications\SendNotification;
use App\Traits\responseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

use function Symfony\Component\String\u;

class NotificationController extends Controller
{
    use responseTrait;
    //
    public function displayNotification()
    {
        try {
            $data = [];
            $notifications = DB::table('notifications')
                ->where('notifiable_id',Auth::user()->id)->latest()->get();
            foreach ($notifications as $notification) {
                $notificationData = json_decode($notification->data);
                $data [] = [
                    'id'=>$notification->id,
                    'obj_id' => $notificationData->obj_id,
                    'title' => $notificationData->title,
                    'body' => $notificationData->body,
                    'read_at' => $notification->read_at,
                    'created_at' => $notification->created_at,
                ];
            }
        } catch (\Exception $ex) {
            return $this->apiResponse(null, $ex->getMessage(), 500);
        }
        return $this->apiResponse($data, "successfully", 200);
    }

    public function getNotificationContent(Request $request){
        $request->validate([
            'id'=>'required',
        ]);
         $notification = DB::table('notifications')->where('id',$request->id)->first();

         if ($notification){
             $data = [
                 'type'=>null,
                 'content'=>null,
             ];

             $notificationData = json_decode($notification->data);
             $title = $notificationData->title;

             DB::table('notifications')->where('id',$request->id)->update(["read_at"=>now()]);

             if($title == 'New Post'){
                 $data['type']='post';
                 $post = Post::find($notificationData->obj_id);
                 if($post){
                     $data['content'] = new PostResource($post);
                 }
             }
             //فوصة عمل
             else if($title == 'Job Opportunity'){
                 $data['type']='opportunity';
                 $opportunity = Opportunity::find($notificationData->obj_id);

                 if($opportunity) {
                     $data['content'] = new OpportunityResource($opportunity);
                 }
             }
             //طلب توظيف
             else if($title == 'Job Application'){
                 $data['type']='application';
                 $job_application = Apply::find($notificationData->obj_id);
                 if($job_application) {
                     $data['content'] = $job_application;
                 }
             }
             //تنبيه الدخول الى الداشبورد
             else if($title == 'Login Alert'){
                 $data['type']='login';
                 $user = User::find($notificationData->obj_id);
                 if($user) {
                     $data['content'] = new UserResource($user);
                 }
             }

            else if($title == 'Report'){
                 $data['type']='report';
                 $report = Report::find($notificationData->obj_id);
                 if($report) {
                     $data['content'] = $report;
                 }
             }

             if($data['content'] !=null){
                 return $this->apiResponse($data , __('strings.success') ,200);
             }
             return $this->apiResponse(null , __('strings.not found notification') ,404);

         }
        return $this->apiResponse(null , __('strings.not_found'),400);

    }
    //delete all notification
    public function delete(){
        $user = User::find(Auth::user()->id);
        //get unRead notification    or read
        //$user->unreadNotifications || readNotifications;
        foreach($user->notifications as $notification){
            $notification->delete();
        }
        return $this->apiResponse(null,__('strings.deleted_successfully'),200);
    }

    public function makeAsRead(){
        $userid=User::find(auth()->user()->id);
        foreach($userid->unreadNotifications as $notification) {
            $notification->markAsRead();
        }
        return $this->apiResponse(null,__('strings.success'),200);
    }

    public function testStore(){
        try {
            $users=User::where('id','!=',auth()->user()->id)->get();
            $user2 = User::find(1);
            $noti =[
                'obj_id'=>Auth::user()->id,
                'title'=>'Login',
                'body'=>'to22 notification',
            ];

            Notification::send($users,new SendNotification($noti));

            $notifications = DB::table('notifications')
                ->where('notifiable_id',Auth::user()->id)->get();

            $data = [];
            foreach ($notifications as $notification){
                $notificationData = json_decode($notification->data);
                $data [] = [
                    'obj_id' => $notificationData->obj_id,
                    'title' => $notificationData->title,
                    'body' => $notificationData->body,
                    'read_at' => $notification->read_at,
                    'created_at' => $notification->created_at,
                ];
            }
        } catch (\Exception $ex) {
            return $this->apiResponse(null, $ex->getMessage(), 500);
        }
        return $this->apiResponse($data, "successfully", 200);
    }

}
