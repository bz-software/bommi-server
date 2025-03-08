<?php

namespace App\DTOs;

use Illuminate\Http\Request;

class CategoryServiceDTO
{
    public int $id;
    public string $name;
    public bool $deleted;
    public int $idPlace;

    /**
     * CategoryServiceDTO constructor.
     * 
     * @param int $id
     * @param string $name
     * @param bool $deleted
     * @param int $idPlace
     */
    public function __construct(int $id, string $name, bool $deleted, int $idPlace)
    {
        $this->id = $id;
        $this->name = $name;
        $this->deleted = $deleted;
        $this->idPlace = $idPlace;
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
            $data['case_id'],
            $data['case_name'],
            $data['case_deleted'],
            $data['case_place_id']
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
            $request->input('id'),
            $request->input('name'),
            $request->input('deleted'),
            $request->input('idPlace')
        );
    }

    /**
     * Método para converter o DTO em um array.
     * 
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'deleted' => $this->deleted,
            'idPlace' => $this->idPlace,
        ];
    }

    // public function toModel(): array
    // {
    //     return [
    //         'case_id' => $this->id,
    //         'case_name' => $this->name,
    //         'case_deleted' => $this->deleted,
    //         'case_place_id' => $this->idPlace,
    //     ];
    // }
}
