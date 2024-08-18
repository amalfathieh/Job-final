<?php

namespace App\Http\Controllers;

use App\Http\Requests\OpportunityRequest;
use App\Http\Resources\OpportunityResource;
use App\Models\Apply;
use App\Models\Company;
use App\Models\Opportunity;
use App\Models\Post;
use App\Models\User;
use App\Notifications\SendNotification;
use App\services\FileService;
use App\Traits\responseTrait;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use App\services\OpportunityService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;

use function PHPUnit\Framework\isEmpty;
use function PHPUnit\Framework\isNull;

class OpportunityController extends Controller
{
    use responseTrait;
    public function addOpportunity(OpportunityRequest $request, OpportunityService $service) {
        try {
            $files = $request->file('files');
            $images = $request->file('images');
            $user = User::find(Auth::user()->id);
            $company_id =$user->company->id;
            $location = $user->company->location;
            $qualifications = json_decode($request->qualifications);
            $skills_req = json_decode($request->skills_req);
            $opportunity = $service->createOpportunity(
                $company_id, $request->title, $request->body,
                $files, $images, $location, $request->job_type,
                $request->work_place_type, $request->job_hours, $qualifications,
                $skills_req, $request->salary, $request->vacant
            );
            // get followers tokens
            $followers = $user->followers;
            if ($followers) {
                $tokens = [];
                foreach($followers as $follower){
                    $tokens = array_merge($tokens , $follower->routeNotificationForFcm());
                }
                $data =[
                    'obj_id'=> $opportunity->id,
                    'title'=> 'New Opportunity',
                    'body'=> 'New opportunity has been published by '. $user->company->company_name,
                ];

                Notification::send($followers,new SendNotification($data));
               $this->sendPushNotification($data['title'],$data['body'],$tokens);
            }
            return $this->apiResponse(new OpportunityResource($opportunity), __('strings.opportunity_added_successfully'), 201);
        }catch (\Exception $ex) {
            return $this->apiResponse(null, $ex->getMessage(), 500);
        }
    }

    public function updateOpportunity(Request $request, OpportunityService $opportunityService, $id){
        try {
            $opportunity = $opportunityService->update($request, $id);
            return $this->apiResponse(new OpportunityResource($opportunity), __('strings.updated_successfully'), 201);
        } catch (\Exception $ex) {
            return $this->apiResponse(null, $ex->getMessage(), 500);
        }
    }

    public function deleteImage($opp_id, $img_id, FileService $fileService) {
        $opp = Opportunity::where('id', $opp_id)->first();

        $img = $opp->images()->where('id', $img_id)->first();
        if ($img) {
            $fileService->delete($img->url);
            $img->delete();
            return $this->apiResponse(new OpportunityResource($opp), __('strings.deleted_successfully'), 200);
        }
        return $this->apiResponse(null, __('strings.not_found'), 404);
    }

    public function deleteFile($opp_id, $file_id, FileService $fileService) {
        $opp = Opportunity::where('id', $opp_id)->first();

        $file = $opp->files()->where('id', $file_id)->first();
        if ($file) {
            $fileService->delete($file->url);
            $file->delete();
            return $this->apiResponse(new OpportunityResource($opp), __('strings.deleted_successfully'), 200);
        }
        return $this->apiResponse(null, __('strings.not_found'), 404);
    }

    public function delete($id, FileService $fileService){
        $opportunity = Opportunity::find($id);
        $user = User::where('id', Auth::user()->id)->first();
        if ($opportunity) {
            if (($user->hasRole('company') && $opportunity['company_id'] == $user->company->id) || (($user->hasRole('employee') && $user->hasPermissionTo('opportunity delete')) || $user->hasRole('owner'))) {
                $images = $opportunity->images;
                $files = $opportunity->files;
                if (!is_null($images)) {
                    foreach ($images as $value) {
                        $fileService->delete($value->url);
                    }
                    $opportunity->images()->delete();
                }
                if (!is_null($files)) {
                    foreach ($files as $value) {
                        $fileService->delete($value->url);
                    }
                    $opportunity->files()->delete();
                }
                $opportunity->delete();
                return $this->apiResponse(null, __('strings.deleted_successfully'), 200);
            }
            return $this->apiResponse(null,__('strings.authorization_required'),403);
        }
        return $this->apiResponse(null, __('strings.not_found'), 404);
    }

    public function getMyOpportunities() {
        $user = User::where('id', Auth::user()->id)->first();
        $company = Company::where('id', $user->company->id)->first();
        $opportunities = OpportunityResource::collection($company->opportunities);
        return $this->apiResponse($opportunities, __('strings.all_my_opportunities'), 200);
    }

    public function allOpportunities() {
        $userId = Auth::user()->id;
        $opportunities = Opportunity::select('opportunities.*')->addSelect(DB::raw("EXISTS(SELECT 1 FROM followers WHERE followers.follower_id = opportunities.company_id AND followers.followee_id = $userId) AS is_followed"))
            ->orderByDesc('is_followed')
            ->latest()
            ->get();

            $opportunities = $opportunities->reject(function (Opportunity $opp){
                return $opp->company->user->isBanned();
            });

            $opportunities = OpportunityResource::collection($opportunities);
            return $this->apiResponse($opportunities, 'successfully', 200);
        }

        public function getAllOpp() {
            $opportunities = Opportunity::latest()->get();
            $groupedOpportunities = [];
            $chunkSize = 3;

            foreach ($opportunities as $index => $opportunity) {
                $groupIndex = (int) ($index / $chunkSize);
                $groupedOpportunities[$groupIndex][] = $opportunity;
            }
            $data = [];
            foreach ($groupedOpportunities as $group) {
                $data[] = OpportunityResource::collection($group);
            }
            return $data;
        }

    public function getCompanyOpportunities($id) {
        $user = User::where('id', $id)->first();
        if ($user) {
            $company = Company::where('id', $user->company->id)->first();
            if ($company) {

                $opportunities = OpportunityResource::collection($company->opportunities);
                return $this->apiResponse($opportunities, __('strings.all_my_opportunities'), 200);
            }
        }
        return $this->apiResponse(null, __('strings.not_found'), 404);
    }

    public function getOpportunityInfo($id) {
        $opportunity = Opportunity::where('id', $id)->get();
        if (!isNull($opportunity)) {
            $opportunity = OpportunityResource::collection($opportunity);
            return $this->apiResponse($opportunity, 'These are all information about this opportunity', 200);
        }
        return $this->apiResponse(null, __('strings.not_found'), 404);
    }

    public function counts() {
        $data['opportunitiesCount'] = Opportunity::count();
        $data['opportunitiesVacantCount'] = Opportunity::where('vacant', 1)->count();
        $data['appliesCount'] = Apply::count();
        $data['appliesAccepted'] = Apply::where('status', 'accepted')->count();
        $data['appliesRejected'] = Apply::where('status', 'rejected')->count();
        $data['posts'] = Post::count();

        return $this->apiResponse($data, 'Success', 200);
    }
    //الفرص المقترحة
    public function proposed_Jobs(){
        $seeker = User::find(Auth::user()->id)->seeker;
        $companies = Company::where('domain', $seeker->specialization)->get();
        $companies = $companies->reject(function (Company $company){
            return $company->user->isBanned();
        });
        $opportunities = [];
        foreach($companies as $company){
            $companyOpportunities = $company->opportunities;
            foreach($companyOpportunities as $opportunity){
                $opportunities[] = new OpportunityResource($opportunity);
            }
        }
        return $this->apiResponse($opportunities , 'proposed Jobs' ,200);
    }
}
