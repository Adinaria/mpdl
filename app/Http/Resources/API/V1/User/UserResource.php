<?php

namespace App\Http\Resources\API\V1\User;

use App\Http\Resources\API\V1\Role\RoleShortResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid'      => $this->uuid,
            'name'      => $this->name,
            'last_name' => $this->last_name,
            'email'     => $this->email,
            'roles'     => RoleShortResource::collection($this->whenLoaded('roles')),
        ];
    }
}
