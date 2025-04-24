<?php

namespace App\DTOs;

use Illuminate\Http\Request;

class SearchFiltersCalendarAppointmentDTO extends AbstractDTO
{
    public $start;
    public $end;
    public $idEmployeePlace;

    /**
     * SearchFiltersCalendarAppointmentDTO constructor.
     * 
     * @param string $start
     * @param string $end
     * @param int|null $idEmployeePlace
     */
    public function __construct($start, $end, $idEmployeePlace = null)
    {
        $this->start = $start;
        $this->end = $end;
        $this->idEmployeePlace = $idEmployeePlace;
    }

    /**
     * Método para criar o DTO a partir de uma requisição.
     * 
     * @param Request $request
     * @return SearchFiltersCalendarAppointmentDTO
     */
    public static function fromRequest(Request $request): self
    {
        return new self(
            $request->input('start') ?? null,
            $request->input('end') ?? null,
            $request->input('idEmployeePlace') ?? null
        );
    }
}
