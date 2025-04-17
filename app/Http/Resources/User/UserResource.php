<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'token' => $this->when($this->token, $this->token),
            'email' => $this->email,
            'name' => $this->name,
            'email_vefied_at' => $this->email_verified_at,
        ];
    }
}
