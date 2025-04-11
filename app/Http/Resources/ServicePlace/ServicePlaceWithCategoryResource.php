<?php

namespace App\Http\Resources\ServicePlace;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CategoryService\CategoryServiceResource;

class ServicePlaceWithCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->sepl_id ?? null,
            'name' => $this->sepl_name ?? null,
            'description' => $this->sepl_description ?? null,
            'idCategory' => $this->sepl_case_id ?? null,
            'deleted' => $this->sepl_deleted ?? null,
            'price' => $this->sepl_price ?? null,
            'duration' => $this->sepl_duration ?? null,
            'category' => new CategoryServiceResource($this->categoryService)
        ];
    }
}
