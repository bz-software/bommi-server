<?php

namespace App\Http\Resources\Employee;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->emp_id ?? null,
            "firstName" => $this->emp_first_name ?? null,
            "lastName" => $this->emp_last_name ?? null,
            "email" => $this->emp_email ?? null,
            "phoneNumber" => $this->emp_phone_number ?? null,
            "birthdate" => $this->emp_birthdate ?? null,
            "cpf" => $this->emp_cpf ?? null,
            "avatar" => $this->emp_avatar ?? null,
            "avatarUrl" => $this->emp_avatar_url
        ];
    }
}
