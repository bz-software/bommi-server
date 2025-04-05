<?php

namespace App\DTOs;

use Illuminate\Http\Request;

class EmployeePlaceAttendanceDTO extends AbstractDTO
{
    public $ep_employee_id;
    public $ep_place_id;
    public $ep_clock_in;
    public $ep_clock_out;

    /**
     * EmployeePlaceAttendanceDTO constructor.
     * 
     * @param int|null $idEmployee
     * @param string|null $clockIn
     * @param string|null $clockOut
     */
    public function __construct(
        $idEmployee,
        $idPlace,
        $clockIn,
        $clockOut
    )
    {
        $this->ep_employee_id = $idEmployee;
        $this->ep_place_id = $idPlace;
        $this->ep_clock_in = $clockIn;
        $this->ep_clock_out = $clockOut;
    }

    /**
     * Método para criar o DTO a partir de uma requisição.
     * 
     * @param Request $request
     * @return EmployeePlaceAttendanceDTO
     */
    public static function fromRequest(Request $request): self
    {
        return new self(
            null, 
            null,
            $request->input('clockIn') ?? null,
            $request->input('clockOut') ?? null
        );
    }
}
