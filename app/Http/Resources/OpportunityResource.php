<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OpportunityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' =>$this->company->user_id,
            'company_id' => $this->company_id,
            'company_name' => $this->company->company_name,
            'company_logo' => $this->company->image->url ?? null,
            'title' => $this->title,
            'body' => $this->body,
            'files' => $this->files,
            'images' => $this->images,
            'location' => $this->location,
            'job_type' => $this->job_type,
            'work_place_type' => $this->work_place_type,
            'job_hours' => $this->job_hours,
            'qualifications' => $this->qualifications,
            'skills_req' => $this->skills_req,
            'salary' => $this->salary,
            'vacant' => $this->vacant,
            'updated_at' => $this->created_at->format('M-d-Y'),
            'updated_at' => $this->updated_at->format('M-d-Y'),
            'created_at_with_time' => $this->created_at->format('M-d-Y h:i A'),
            'updated_at_with_time' => $this->updated_at->format('M-d-Y h:i A')
        ];
    }
}
