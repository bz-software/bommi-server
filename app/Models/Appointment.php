<?php

namespace App\Models;

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

    protected $fillable = [
        'apmt_pla_id',
        'apmt_ep_id',
        'apmt_cli_id',
        'apmt_status',
        'apmt_date',
        'apmt_total_time',
        'apmt_total_value',
    ];
}
