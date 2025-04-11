<?php

namespace App\DTOs;

use Illuminate\Http\Request;

class CreatePlaceAddressDTO extends AbstractDTO
{
    public $plad_id;
    public $plad_cep;
    public $plad_uf;
    public $plad_city;
    public $plad_district;
    public $plad_place_id;
    public $plad_number;
    public $plad_complement;
    public $plad_lat;
    public $plad_lon;

    /**
     * CreatePlaceAddressDTO constructor.
     * 
     * @param int $id
     * @param string $cep
     * @param string $uf
     * @param string $city
     * @param string $district
     * @param int $idPlace
     * @param string $number
     * @param string $complement
     * @param string $lat
     * @param string $lon
     */
    public function __construct($id, $cep, $uf, $city, $district, $idPlace, $number, $complement, $lat, $lon)
    {
        $this->plad_id = $id;
        $this->plad_cep = $cep;
        $this->plad_uf = $uf;
        $this->plad_city = $city;
        $this->plad_district = $district;
        $this->plad_place_id = $idPlace;
        $this->plad_number = $number;
        $this->plad_complement = $complement;
        $this->plad_lat = $lat;
        $this->plad_lon = $lon;
    }

    /**
     * Método para criar o DTO a partir de uma requisição.
     * 
     * @param Request $request
     * @return CreatePlaceAddressDTO
     */
    public static function fromRequest(Request $request): self
    {
        return new self(
            $request->input('address.id') ?? null,
            $request->input('address.cep') ?? null,
            $request->input('address.uf') ?? null,
            $request->input('address.city') ?? null,
            $request->input('address.district') ?? null,
            $request->input('address.idPlace') ?? null,
            $request->input('address.number') ?? null,
            $request->input('address.complement') ?? null,
            $request->input('address.lat') ?? null,
            $request->input('address.lon') ?? null,
        );
    }
}
