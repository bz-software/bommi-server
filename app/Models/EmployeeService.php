<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\EmployeeService
 *
 * @property int $emse_id
 * @property int $emse_sepl_id
 * @property int $emse_ep_id
 * @property int $emse_deleted
 * @property string $emse_deleted_at
 */
class EmployeeService extends Model {
    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $primaryKey = "emse_id";
    
    const REQUIRED_MSG = 'Campo obrigatório';

    static $WITH_ALL = [
        'servicePlace.categoryService',
        'employeePlace.employee'
    ];

    static $WITH_CATEGORY_AND_SERVICE = [
        'servicePlace.categoryService'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'employees_services';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "emse_id",
        "emse_sepl_id",
        "emse_ep_id",
        "emse_deleted",
        "emse_deleted_at"
    ];

    public function employeePlace(): BelongsTo
    {
        return $this->belongsTo(EmployeePlace::class, 'emse_ep_id', 'ep_id');
    }

    public function servicePlace(): BelongsTo
    {
        return $this->belongsTo(ServicePlace::class, 'emse_sepl_id', 'sepl_id');
    }
}
