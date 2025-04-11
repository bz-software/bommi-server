<?php

namespace App\DTOs;

use Illuminate\Http\Request;

class EmployeeServiceDTO extends AbstractDTO
{
    public $emse_sepl_id;
    public $emse_ep_id;
    public $ep_id;

    /**
     * EmployeeServiceDTO constructor.
     * 
     * @param int $emse_sepl_id
     * @param int $emse_ep_id
     */
    public function __construct($idServicePlace, $idEmployee, $idEmployeePlace)
    {
        $this->emse_sepl_id = $idServicePlace;
        $this->emse_ep_id = $idEmployee;
        $this->ep_id = $idEmployeePlace;
    }

    /**
     * Método para criar o DTO a partir de uma requisição.
     * 
     * @param Request $request
     * @return EmployeeServiceDTO
     */
    public static function fromRequest(Request $request): self
    {
        return new self(
            $request->input('idService') ?? null,
            $request->input('idEmployee') ?? null,
            null
        );
    }
}
