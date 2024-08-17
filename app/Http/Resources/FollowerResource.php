<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FollowerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = User::where('id' , $this->id)->first();
        $result = [
            'id' => $this->id,
        ];

        if ($user->hasRole('company')) {
            $result['roles_name'] = 'company';
            $result['name'] = $this->company->company_name;
            $result['image'] = $this->company->image->url ?? null;
        } else if ($user->hasRole('job_seeker')){
            $result['roles_name'] = 'job_seeker';
            $result['name'] = $this->seeker->first_name. ' ' . $this->seeker->last_name;
            $result['image'] = $this->seeker->image->url ?? null;
        }
        return $result;
    }
}
