<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeePlace extends Model {

    protected $primaryKey = "ep_id";

    const CREATED_AT = NULL;
    const UPDATED_AT = NULL;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'employees_place';
}
