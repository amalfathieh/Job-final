<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewsResource extends JsonResource
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
            'title' => $this->title,
            'body' => $this->body,
            'files' => $this->files,
            'images' => $this->images,
            'deleted_at' => $this->deleted_at,
            'created_by' => User::where('id', $this->created_by)->first()->user_name,
            'created_at' => $this->created_at->format('M-d-Y h:i A'),
            'updated_at' => $this->updated_at->format('M-d-Y h:i A'),
        ];
    }
}
