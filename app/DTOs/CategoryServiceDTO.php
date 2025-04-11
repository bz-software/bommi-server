<?php

namespace App\DTOs;

use Illuminate\Http\Request;

class CategoryServiceDTO extends AbstractDTO
{
    public $case_id;
    public $case_name;
    public $case_deleted;
    public $case_place_id;

    /**
     * CategoryServiceDTO constructor.
     * 
     * @param int $id
     * @param string $name
     * @param bool $deleted
     * @param int $idPlace
     */
    public function __construct($id, $name, $deleted, $idPlace)
    {
        $this->case_id = $id;
        $this->case_name = $name;
        $this->case_deleted = $deleted;
        $this->case_place_id = $idPlace;
    }

    /**
     * Método para criar um DTO a partir de um array.
     * 
     * @param array $data
     * @return CategoryServiceDTO
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['case_id'] ?? null,
            $data['case_name'] ?? null,
            $data['case_deleted'] ?? null,
            $data['case_place_id'] ?? null
        );
    }

    /**
     * Método para criar o DTO a partir de uma requisição.
     * 
     * @param Request $request
     * @return CategoryServiceDTO
     */
    public static function fromRequest(Request $request): self
    {
        return new self(
            $request->input('id') ?? null,
            $request->input('name') ?? null,
            $request->input('deleted') ?? null,
            $request->input('idPlace') ?? null
        );
    }

    public function toModel(): array
    {
        return [
            'case_id' => $this->case_id,
            'case_name' => $this->case_name,
            'case_deleted' => $this->case_deleted,
            'case_place_id' => $this->case_place_id,
        ];
    }
}
