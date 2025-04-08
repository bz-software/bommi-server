<?php

namespace App\Http\Resources\EmployeeService;

use App\Http\Resources\Employee\EmployeeResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ServicePlace\ServicePlaceWithCategoryResource;

class EmployeeServiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->emse_id ?? null,
            'idEmployeePlace' => $this->emse_ep_id ?? null,
            'service' => new ServicePlaceWithCategoryResource($this->servicePlace) ?? null
        ];
    }
}
