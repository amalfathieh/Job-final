<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApplyRequest;
use App\Http\Resources\ApplyResource;
use App\Http\Resources\GetAppliesForCompanyResource;
use App\Models\Apply;
use App\Models\Company;
use App\Models\Opportunity;
use App\Models\Seeker;
use App\Models\User;
use App\Notifications\SendNotification;
use App\services\FileService;
use App\Traits\responseTrait;
use App\Traits\NotificationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;

class ApplyController extends Controller
{
    use responseTrait, NotificationTrait;
    public function apply(ApplyRequest $request, $id, FileService $fileService) {
        try {
            $apply = null;
            $opportunity = Opportunity::where('id', $id)->first();
            if (!$opportunity) {
                return $this->apiResponse(null, __('strings.not_found'), 404);
            }
            $company_id= $opportunity->company_id;
            $seeker = Seeker::where('user_id', Auth::user()->id)->first();
            $app = Apply::where('opportunity_id', $id)->where('seeker_id', $seeker->id)->where('company_id', $company_id)->first();

            if (!$opportunity->vacant) {
                return $this->apiResponse(null, __('strings.opportunity_not_vacant'), 400);
            }

            if ($app) {
                return $this->apiResponse(null, __('strings.applied_for_opportunity'), 400);
            }

            $cv = $request->file('cv');
            $cv_path = $fileService->store($cv, 'files/Job_seeker/Applies');
            $seeker = Seeker::where('user_id', Auth::user()->id)->first();
            $apply = Apply::create([
                'opportunity_id' => $id,
                'seeker_id' => $seeker->id,
                'company_id' => $company_id,
            ]);
            $apply->file()->create(['url' => $cv_path]);
            if ($apply) {
                $user = Company::where('id',$company_id)->first()->user;
                $tokens = $user->routeNotificationForFcm();
                // $header = $request->headers->get('accept-language');
                //$body = 'لديكم طلب توظيف جديد لفرصة العمل '.$opportunity->title.' يرجى مراجعة الطلب في أقرب وقت ممكن.';

                // $body = 'You have a new job application for the '.$opportunity->title.' position. Please review the application at your earliest convenience.';

                $data =[
                    'obj_id'=> $apply->id,
                    'title'=> 'New Job request',
                    'body'=> 'There is a new request from '. $seeker->first_name.' ' .$seeker->last_name .' for your opportunity '. $opportunity->title,
                ];
                Notification::send($user,new SendNotification($data));
                $this->sendPushNotification($data['title'],$data['body'],$tokens);
                return $this->apiResponse(new ApplyResource($apply), __('strings.apply_successfully'), 201);
            }
        } catch (\Exception $th) {
            return $this->apiResponse(null, $th->getMessage(), 500);
        }
    }

    public function getMyApplies() {
        $seeker = Seeker::where('user_id', Auth::user()->id)->first();
        $applies = ApplyResource::collection(Apply::where('seeker_id', $seeker->id)->orderBy('status')->get());
        return $this->apiResponse($applies, __('strings.get_my_applies'), 200);
    }

    public function update(Request $request, $id, FileService $fileService) {
        $apply = Apply::where('id', $id)->first();
        $seeker = Seeker::where('user_id', Auth::user()->id)->first();
        if ($apply->seeker_id === $seeker->id) {
            $new_cv = $request->file('cv');
            $cv_path = $fileService->update($new_cv,$apply->file->url, 'job_seeker/applies');
            $apply->file()->update([
                'url' => $cv_path,
            ]);
            $newData = Apply::where('id', $id)->first();
            if ($apply) {
                return $this->apiResponse(new ApplyResource($newData), __('strings.updated_successfully'), 201);
            }
            return $this->apiResponse(null, __('strings.error_occurred'), 400);
        }
        return $this->apiResponse(null, __('strings.not_allowed_action'), 400);
    }

    public function delete($id) {
        try {
            $user = User::where('id', Auth::user()->id)->first();
            $apply = Apply::where('id', $id)->first();
            if ($apply->seeker_id === $user->seeker->id || $apply->company_id === $user->company->id) {
                if ($apply->status === 'waiting' || ($user->company ? $apply->company_id === $user->company->id : false)) {
                    $apply->delete();
                    return $this->apiResponse(null, __('strings.deleted_successfully'), 200);
                }
            return $this->apiResponse(null,  __('strings.cannot_delete_not_waiting'), 400);
        }
        return $this->apiResponse(null, __('strings.not_allowed_action'), 400);
    } catch (\Exception $th) {
            return $this->apiResponse(null, $th->getMessage(), 500);
        }
    }
    // For Companies

    public function updateStatus($id, Request $request) {
        $validate = Validator::make($request->all(), [
            'status' => 'required|in:accepted,waiting,rejected'
        ]);

        if ($validate->fails()){
            return $this->apiResponse(null, $validate->errors(), 400);
        }
        $apply = Apply::where('id', $id)->first();
        $user = User::where('id', Auth::user()->id)->first();

        //send notification to job_seeker
        if ($apply->company_id === $user->company->id) {
            $apply->update([
                'status' => $request->status
            ]);
            if($request->status == 'accepted'){
                $body = 'Congratulations, Your request has benn accepted at '. $user->company->company_name;
            }
            else if($request->status == 'rejected'){
                $body = 'Unfortunately, Your request has been rejected at ' . $user->company->company_name;
            }
            $seeker = Seeker::where('id', $apply->seeker_id)->first();
            $user = User::find($seeker->user_id);
            $tokens = $user->routeNotificationForFcm();
            $data =[
                'obj_id'=> $apply->id,
                'title'=>  'New Job request',
                'body'=> $body,
            ];
            Notification::send($user,new SendNotification($data));
           $this->sendPushNotification($data['title'],$data['body'],$tokens);

            return $this->apiResponse(new ApplyResource($apply),  __('strings.updated_successfully'), 200);
        }
        return $this->apiResponse(null, __('strings.not_allowed_action'), 400);
    }

    public function getApplies() {
        $user = User::where('id', Auth::user()->id)->first();
        $company = Company::where('id', $user->company->id)->first();
        $applies = Apply::where('company_id', $company->id)->orderByRaw("FIELD(status, 'waiting', 'accepted', 'rejected')")->get();
        return $this->apiResponse(GetAppliesForCompanyResource::collection($applies),  __('strings.all_applies'), 200);
    }
}
