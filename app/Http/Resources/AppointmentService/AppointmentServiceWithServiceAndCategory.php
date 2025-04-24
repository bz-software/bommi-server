<?php

namespace App\Http\Resources\AppointmentService;

use App\Http\Resources\Employee\EmployeeResource;
use App\Http\Resources\ServicePlace\ServicePlaceWithCategoryResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentServiceWithServiceAndCategory extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->apse_id,
            'idAppointment' => $this->apse_apmt_id,
            'idServicePlace' => $this->apse_sepl_id,
            'observation' => $this->apse_observation,
            'servicePlace' => new ServicePlaceWithCategoryResource($this->servicePlace)
        ];
    }
}
