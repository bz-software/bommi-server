<?php

namespace App\Http\Resources\EmployeePlace;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeePlaceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->ep_id ?? null,
            "idPlace" => $this->ep_place_id ?? null,
            "idEmployee" => $this->ep_employee_id ?? null,
            "type" => $this->ep_employee_type ?? null,
            "typeName" => $this->ep_employee_type_name ?? null,
            "clockIn" => $this->ep_clock_in ?? null,
            "clockOut" => $this->ep_clock_out ?? null
        ];
    }
}
