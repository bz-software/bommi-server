<?php

namespace App\Http\Resources\Appointment;

use App\Http\Resources\AppointmentService\AppointmentServiceWithServiceAndCategory;
use App\Http\Resources\Client\ClientResource;
use App\Http\Resources\Employee\EmployeeResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentWithFullServiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->apmt_id,
            'idPlace' => $this->apmt_pla_id,
            'idEmployeePlace' => $this->apmt_ep_id,
            'idClient' => $this->apmt_cli_id,
            'status' => $this->apmt_status,
            'createdAt' => $this->apmt_created_at,
            'date' => $this->apmt_date,
            'totalMinutes' => $this->apmt_total_time,
            'totalValue' => $this->apmt_total_value,
            'dateEnd' => $this->apmt_date_end,
            'client' => new ClientResource($this->client),
            'services' => AppointmentServiceWithServiceAndCategory::collection($this->services)
        ];
    }
}
