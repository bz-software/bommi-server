<?php

namespace App\Models;

use Carbon\Carbon;
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

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "ep_place_id",
        "ep_employee_id",
        "ep_deleted"
    ];


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
        $model->ep_employee_id = $idEmployee;
        $model->ep_employee_type = $type;

        return $model->save();
    }

    public static function unset($idEmployee){
        $now = Carbon::now();
        $idOwner = auth('employee')->user()->emp_id;
        $idPlace = auth('employee')->user()->emp_place_id_logged;

        // SELECT ep.* FROM employees_place ep
        // INNER JOIN places p ON p.pla_id = ep.ep_place_id
        // INNER JOIN employees owner ON owner.emp_id = p.pla_employee_id
        // WHERE p.pla_id = 7
        // AND owner.emp_id = 65
        // AND ep.ep_employee_id = 66;

        $employeePlace = EmployeePlace::select('employees_place.*')
            ->join('places as p', 'p.pla_id', '=', 'employees_place.ep_place_id')
            ->join('employees as owner', 'owner.emp_id', '=', 'p.pla_employee_id')
            ->where('p.pla_id', '=', $idPlace)
            ->where('owner.emp_id', '=', $idOwner)
            ->where('employees_place.ep_employee_id', '=', $idEmployee)
            ->first();

        if(!empty($employeePlace)){
            $employeePlace->ep_deleted = true;
            $employeePlace->ep_deleted_at = $now->toDateTimeString();
            
            return $employeePlace->save();
        }

        return false;
    }
}
