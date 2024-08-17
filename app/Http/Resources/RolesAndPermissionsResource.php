<?php

namespace App\Http\Resources;

use App\Traits\responseTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RolesAndPermissionsResource extends JsonResource
{
    use responseTrait;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'permissions' =>
            $this->permissions->map(function ($permission) {
                return $permission;
            }),
        ];

    }
}
