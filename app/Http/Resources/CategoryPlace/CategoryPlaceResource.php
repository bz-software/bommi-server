<?php

namespace App\Http\Resources\CategoryPlace;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryPlaceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->capl_id ?? null,
            'name' => $this->capl_name ?? null
        ];
    }
}
