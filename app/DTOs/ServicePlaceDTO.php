<?php

namespace App\DTOs;

use Illuminate\Http\Request;

class ServicePlaceDTO extends AbstractDTO
{
    public $sepl_id;
    public $sepl_name;
    public $sepl_description;
    public $sepl_case_id;
    public $sepl_deleted;
    public $sepl_price;
    public $sepl_duration;

    /**
     * ServicePlaceDTO constructor.
     * 
     * @param int $id
     * @param string $name
     * @param string $description
     * @param int $idCategory
     * @param bool $deleted
     * @param float $price
     * @param string $duration
     */
    public function __construct($id, $name, $description, $idCategory, $deleted, $price, $duration)
    {
        $this->sepl_id = $id;
        $this->sepl_name = $name;
        $this->sepl_description = $description; 
        $this->sepl_case_id = $idCategory;
        $this->sepl_deleted = $deleted;
        $this->sepl_price = $price;
        $this->sepl_duration = $duration;
    }

    /**
     * Método para criar um DTO a partir de um array.
     * 
     * @param array $data
     * @return ServicePlaceDTO
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['sepl_id'] ?? null,
            $data['sepl_name'] ?? null,
            $data['sepl_description'] ?? null,
            $data['sepl_case_id'] ?? null,
            $data['sepl_deleted'] ?? null,
            $data['sepl_price'] ?? null,
            $data['sepl_duration'] ?? null
        );
    }

    /**
     * Método para criar o DTO a partir de uma requisição.
     * 
     * @param Request $request
     * @return ServicePlaceDTO
     */
    public static function fromRequest(Request $request): self
    {
        return new self(
            $request->input('id') ?? null,
            $request->input('name') ?? null,
            $request->input('description') ?? null,
            $request->input('idCategory') ?? null,
            $request->input('deleted') ?? null,
            $request->input('price') ?? null,
            $request->input('duration') ?? null             
        );
    }

    public function toModel(): array
    {
        return [
            'sepl_id' => $this->sepl_id,
            'sepl_name' => $this->sepl_name,
            'sepl_description' => $this->sepl_description,
            'sepl_case_id' => $this->sepl_case_id,
            'sepl_deleted' => $this->sepl_deleted,
            'sepl_price' => $this->sepl_price,
            'sepl_duration' => $this->sepl_duration            
        ];
    }
}
