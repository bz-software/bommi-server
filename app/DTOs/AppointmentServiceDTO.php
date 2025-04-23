<?php

namespace App\DTOs;

use Illuminate\Http\Request;

class AppointmentServiceDTO extends AbstractDTO
{
    public $apse_id;
    public $apse_apmt_id;
    public $apse_sepl_id;
    public $apse_observation;
    public $apse_deleted_at;

    /**
     * AppointmentServiceDTO constructor.
     * 
     * @param int|null $apse_id
     * @param int $apse_apmt_id
     * @param int $apse_sepl_id
     * @param string|null $apse_observation
     * @param string|null $apse_deleted_at
     */
    public function __construct($apse_id, $apse_apmt_id, $apse_sepl_id, $apse_observation = null, $apse_deleted_at = null)
    {
        $this->apse_id = $apse_id;
        $this->apse_apmt_id = $apse_apmt_id;
        $this->apse_sepl_id = $apse_sepl_id;
        $this->apse_observation = $apse_observation;
        $this->apse_deleted_at = $apse_deleted_at;
    }

    /**
     * Create DTO from Request using camelCase field names.
     * 
     * @param Request $request
     * @return AppointmentServiceDTO
     */
    public static function fromRequest(Request $request): self
    {
        return new self(
            $request->input('id') ?? null,
            $request->input('idAppointment') ?? null,
            $request->input('idServicePlace') ?? null,
            $request->input('observation') ?? null,
            $request->input('deletedAt') ?? null
        );
    }
}
