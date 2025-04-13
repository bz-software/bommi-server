<?php

namespace App\Http\Resources\Place;

use App\Http\Resources\CategoryService\CategoryServiceWithServicesResource;
use App\Http\Resources\EmployeePlace\EmployeePlaceWithEmployeeResource;
use App\Http\Resources\PlaceAddress\PlaceAddressResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlaceAllRelationsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->pla_id ?? null,
            "idAdmin" => $this->pla_employee_id ?? null,
            "name" => $this->pla_name ?? null,
            "phoneNumber" => $this->pla_phone_number ?? null,
            "idCategory" => $this->pla_category_id ?? null,
            "cnpj" => $this->pla_cnpj ?? null,
            "hasCnpj" => $this->pla_has_cnpj ?? null,
            "avatar" => $this->pla_avatar ?? null,
            "slugName" => $this->pla_slug_name ?? null,
            "address" => new PlaceAddressResource($this->address) ?? null,
            "categories" => CategoryServiceWithServicesResource::collection($this->categories) ?? null,
            "employees" => EmployeePlaceWithEmployeeResource::collection($this->employeePlaces) ?? null
        ];
    }
}
