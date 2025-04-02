<?php
namespace App\Repositories;
use App\Models\EmployeePlace as Model;

class EmployeePlaceRepository
{
    public function __construct(private Model $model) {}

    public function exist($idEmployee, $idPlace){
        if(!empty($this->get($idEmployee, $idPlace))){
            return true;
        }else{
            return false;
        }
    }

    public function get($idEmployee, $idPlace){
        $employeePlace = $this->model->select('*')
            ->where('ep_place_id', '=', $idPlace)
            ->where('ep_employee_id', '=', $idEmployee)
            ->where('ep_deleted', '=', '0')
            ->first();

        return $employeePlace;
    }

    public function store($idEmployee, $idPlace, $type){
        $model = new $this->model;
        $model->ep_place_id = $idPlace;
        $model->ep_employee_id = $idEmployee;
        $model->ep_employee_type = $type;

        if(!$model->save()){
            abort(500);
        }
        
        return $this->model->find($model->ep_id);
    }
}

