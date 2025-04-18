<?php

namespace App\DTOs;

use Illuminate\Http\Request;

class AppointmentDTO extends AbstractDTO
{
    public $apmt_pla_id;
    public $apmt_ep_id;
    public $apmt_cli_id;
    public $apmt_status;
    public $apmt_date;
    public $apmt_total_time;
    public $apmt_total_value;

    
    // off model
    public $cli_name;
    public $cli_phone_number;
    public $appointmentServices;
    public $date;
    public $hour;

    /**
     * AppointmentDTO constructor.
     * 
     * @param int $idPlace
     * @param string $name
     * @param string $phoneNumber
     * @param array $services
     * @param string $date
     * @param string $hour
     */
    public function __construct(
        $idPlace, 
        $name, 
        $phoneNumber, 
        array $services, 
        $date, 
        $hour,

        $idEmployee, 
        $idClient,
        $status,
        $totalTime,
        $totalValue
    )
    {
        $this->apmt_pla_id = $idPlace;
        $this->cli_name = $name;
        $this->cli_phone_number = $phoneNumber;
        $this->appointmentServices = $services;
        $this->date = $date;
        $this->hour = $hour;
        
        $this->apmt_ep_id = $idEmployee;
        $this->apmt_cli_id = $idClient;
        $this->apmt_status = $status;
        $this->apmt_total_time = $totalTime;
        $this->apmt_total_value = $totalValue;
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
            $request->input('date') ?? null,
            $request->input('hour') ?? null,
            null,
            null,
            null,
            null,
            null
        );
    }
}
