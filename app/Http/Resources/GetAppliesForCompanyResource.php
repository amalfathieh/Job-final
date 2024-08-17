<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GetAppliesForCompanyResource extends JsonResource
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
            'opportunity_name' => $this->opportunity->title,
            'seeker_id' => $this->seeker->id,
            'seeker_name' => $this->seeker->first_name . ' ' . $this->seeker->last_name,
            'seeker_email' => $this->seeker->user->email,
            'cv' => $this->file->url ?? null,
            'status' => $this->status,
            'created_at' => $this->created_at->format('M-d-Y h:i A'),
            'updated_at' => $this->updated_at->format('M-d-Y h:i A'),
        ];
    }
}
