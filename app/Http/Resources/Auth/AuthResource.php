<?php

namespace App\Http\Resources\Auth;

use App\Http\Resources\Employee\EmployeeWithEmployeePlaceResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'token' => $this->token ?? null,
            'user' => new EmployeeWithEmployeePlaceResource($this->user) ?? null
        ];
    }
}
