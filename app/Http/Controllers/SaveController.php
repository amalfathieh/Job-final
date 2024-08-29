<?php

namespace App\Http\Controllers;

use App\Http\Resources\OpportunityResource;
use App\Models\Opportunity;
use App\Traits\responseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SaveController extends Controller
{
    //
    use responseTrait;
    public function saveOpportunity($opportunityId)
    {
        $user = Auth::user();
        $opportunity = Opportunity::find($opportunityId);

        if($opportunity) {
            if ($user->savedOpportunities()->where('opportunity_id', $opportunityId)->exists()) {
                $user->savedOpportunities()->detach($opportunity);
                $message = __('strings.opportunity_deleted');
            }
            else {
                $user->savedOpportunities()->attach($opportunity);
                $message = __('strings.opportunity_added');
            }
            return $this->apiResponse(null, $message,200);
        }
            return $this->apiResponse(null, __('strings.not_found'), 404);
    }
    public function getSavedItems(){
        $user = Auth::user();
        $savedItems = $user->savedOpportunities;
        $data = OpportunityResource::collection($savedItems);
        return $this->apiResponse($data , 'success' , 200);
    }
}
