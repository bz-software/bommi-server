<?php

namespace App\DTOs;

use Illuminate\Http\Request;

class AppointmentDTO extends AbstractDTO
{
    public $apmt_pla_id;
    public $cli_name;
    public $cli_phone_number;
    public $appointmentServices;
    public $date;

    /**
     * AppointmentDTO constructor.
     * 
     * @param int $idPlace
     * @param string $name
     * @param string $phoneNumber
     * @param array $services
     * @param string $date
     */
    public function __construct($idPlace, $name, $phoneNumber, array $services, $date)
    {
        $this->apmt_pla_id = $idPlace;
        $this->cli_name = $name;
        $this->cli_phone_number = $phoneNumber;
        $this->appointmentServices = $services;
        $this->date = $date;
    }

    /**
     * Método para criar o DTO a partir de uma requisição.
     * 
     * @param Request $request
     * @return AppointmentDTO
     */
    public static function fromRequest(Request $request): self
    {
        return new self(
            $request->input('idPlace') ?? null,
            $request->input('name') ?? null,
            $request->input('phoneNumber') ?? null,
            $request->input('services') ?? [],
            $request->input('date') ?? null
        );
    }
}
