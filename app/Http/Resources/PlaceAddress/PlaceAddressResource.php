<?php

namespace App\Http\Resources\PlaceAddress;

use App\Models\PlaceAddress;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlaceAddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->plad_id ?? null,
            "cep" => $this->plad_cep ?? null,
            "uf" => $this->plad_uf ?? null,
            "city" => $this->plad_city ?? null,
            "district" => $this->plad_district ?? null,
            "idPlace" => $this->plad_place_id ?? null,
            "number" => $this->plad_number ?? null,
            "complement" => $this->plad_complement ?? null,
            "lat" => $this->plad_lat ?? null,
            "lon" => $this->plad_lon ?? null,
        ];
    }
}
