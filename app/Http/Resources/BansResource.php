<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BansResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = User::where('id', $this->bannable_id)->first();
        $type = array_search('company', $user->roles_name) ? 'company' : (array_search('job_seeker', $user->roles_name) ? 'job_seeker' : 'employee');
        return [
            "id"=>  $this->id,
            "bannable_type" => $this->bannable_type,
            "bannable_id" => $this->bannable_id,
            "bannable_name" => $user->user_name,
            "bannable_image" => $type == 'company' ? $user->company->image : ($type == 'job_seeker' ? $user->seeker->image : $user->employee->image) ,
            "created_by_type" => $this->created_by_type,
            "created_by_id" => $this->created_by_id,
            "created_by_name" => User::where('id', $this->created_by_id)->first()->user_name,
            "comment"=> $this->comment,
            "expired_at"=> $this->expired_at ? $this->expired_at : 'for ever',
            "deleted_at"=> $this->deleted_at,
            "created_at"=> $this->created_at,
            "updated_at"=> $this->updated_at
        ];
    }
}
