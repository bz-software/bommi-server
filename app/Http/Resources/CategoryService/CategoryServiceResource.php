<?php

namespace App\Http\Resources\CategoryService;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryServiceResource extends JsonResource
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
        ];
    }
}
