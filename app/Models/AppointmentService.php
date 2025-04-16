<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppointmentService extends Model
{
    use SoftDeletes;

    protected $table = 'appointment_services';
    protected $primaryKey = 'apse_id';
    public $timestamps = false;

    const DELETED_AT = 'apse_deleted_at';

    protected $fillable = [
        'apse_apmt_id',
        'apse_sepl_id',
        'apse_observation',
    ];

    // Relacionamento com o agendamento
    public function appointment()
    {
        return $this->belongsTo(Appointment::class, 'apse_apmt_id', 'apmt_id');
    }

    // Relacionamento com o serviço do local
    public function servicePlace()
    {
        return $this->belongsTo(ServicePlace::class, 'apse_sepl_id', 'sepl_id');
    }
}
