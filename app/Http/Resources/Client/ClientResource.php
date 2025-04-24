<?php

namespace App\Http\Resources\Client;

use App\Http\Resources\Employee\EmployeeResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->cli_id,
            'name' => $this->cli_name,
            'phoneNumber' => $this->cli_phone_number,
        ];
    }
}
