<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LogsResource extends JsonResource
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
            'causer_id' => $this->causer_id,
            'causer_name' => User::find($this->causer_id)->user_name ?? null,
            'log_name' => $this->log_name,
            'causer_type' => $this->causer_type,
            'description' => $this->description,
            'event' => $this->event,
            'properties' => $this->properties,
            'subject_id' => $this->subject_id,
            'subject_type' => $this->subject_type,
            'created_at' => $this->created_at->format('M-d-Y h:i A'),
            'updated_at' => $this->updated_at->format('M-d-Y h:i A'),
        ];
    }
}
