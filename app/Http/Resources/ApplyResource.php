<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplyResource extends JsonResource
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
            'company_name' => $this->opportunity->company->company_name,
            'company_logo' => $this->opportunity->company->image->url ?? null,
            'status' => $this->status,
            'cv_path' => $this->file->url ?? null,
            'created_at' => $this->created_at->format('M-d-Y h:i A')
        ];
    }
}
