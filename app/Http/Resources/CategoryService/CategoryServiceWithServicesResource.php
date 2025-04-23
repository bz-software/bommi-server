<?php

namespace App\Http\Resources\CategoryService;

use App\Http\Resources\ServicePlace\ServicePlaceResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryServiceWithServicesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->case_id ?? null,
            'name' => $this->case_name ?? null,
            'deleted' => $this->case_deleted ?? null,
            'idPlace' => $this->case_place_id ?? null,
            'services' => ServicePlaceResource::collection($this->services) ?? null
        ];
    }
}
