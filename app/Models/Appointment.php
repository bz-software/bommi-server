<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use SoftDeletes;

    public const DAYS_SCHEDULE_OPEN = 14;
    public const PLACE_CLOCK_IN_HOUR = 8;
    public const PLACE_CLOCK_OUT_HOUR = 18;
    public const SLOT_SCHEDULE_INTERVAL_MINUTES = 30;

    protected $table = 'appointments';
    protected $primaryKey = 'apmt_id';
    public $timestamps = true;

    const CREATED_AT = 'apmt_created_at';
    const UPDATED_AT = null;
    const DELETED_AT = 'apmt_deleted_at';

    protected $appends = ['apmt_date_end'];

    protected $fillable = [
        'apmt_pla_id',
        'apmt_ep_id',
        'apmt_cli_id',
        'apmt_status',
        'apmt_date',
        'apmt_total_time',
        'apmt_total_value',
    ];

    public function getApmtDateEndAttribute()
    {
        return Carbon::parse($this->attributes['apmt_date'])->addMinutes($this->attributes['apmt_total_time'])->format('Y-m-d H:i:s');
    }

    public function services(){
        return $this->hasMany(AppointmentService::class, 'apse_apmt_id');
    }

    public function employeePlace(){
        return $this->belongsTo(EmployeePlace::class, 'apmt_ep_id');
    }

    public function client(){
        return $this->belongsTo(Client::class, 'apmt_cli_id');
    }
}
