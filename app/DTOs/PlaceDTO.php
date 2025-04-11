<?php

namespace App\DTOs;

use Illuminate\Http\Request;

class PlaceDTO extends AbstractDTO
{
    public $pla_id;
    public $pla_name;
    public $pla_phone_number;
    public $pla_category_id;
    public $pla_cnpj;
    public $pla_has_cnpj;
    public $pla_avatar;

    /**
     * PlaceDTO constructor.
     * 
     * @param string $name
     * @param string $phoneNumber
     * @param int $idCategory
     * @param string $cnpj
     * @param int $hasCnpj
     * @param string $avatar
     */
    public function __construct($id, $name, $phoneNumber, $idCategory, $cnpj, $hasCnpj, $avatar)
    {
        $this->pla_id = $id;
        $this->pla_name = $name;
        $this->pla_phone_number = $phoneNumber;
        $this->pla_category_id = $idCategory;
        $this->pla_cnpj = $cnpj;
        $this->pla_has_cnpj = $hasCnpj;
        $this->pla_avatar = $avatar;
    }

    /**
     * Método para criar o DTO a partir de uma requisição.
     * 
     * @param Request $request
     * @return PlaceDTO
     */
    public static function fromRequest(Request $request): self
    {
        return new self(
            $request->input('id') ?? null,
            $request->input('name') ?? null,
            $request->input('phoneNumber') ?? null,
            $request->input('idCategory') ?? null,
            $request->input('cnpj') ?? null,
            $request->input('hasCnpj') ?? null,
            $request->input('avatar') ?? 'default.png'
        );
    }
}
