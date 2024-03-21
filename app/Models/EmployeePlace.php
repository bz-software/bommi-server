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


    public static function get($idEmployee, $idPlace){
        $employeePlace = EmployeePlace::select('*')
            ->where('ep_place_id', '=', $idPlace)
            ->where('ep_employee_id', '=', $idEmployee)
            ->where('ep_deleted', '=', '0')
            ->first();

        return $employeePlace;
    }

    public static function exist($idEmployee, $idPlace){
        if(!empty(self::get($idEmployee, $idPlace))){
            return true;
        }else{
            return false;
        }
    }

    public static function create($idEmployee, $idPlace, $type){
        $model = new EmployeePlace();
        $model->ep_place_id = $idPlace;
        $model->ep_emp_id = $idEmployee;
        $model->ep_employee_type = $type;

        return $model->save();
    }
}
