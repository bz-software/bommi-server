<?php
namespace App\Repositories;

use App\DTOs\EmployeePlaceAttendanceDTO;
use App\Exceptions\RepositoryException;
use App\Models\EmployeePlace as Model;
use Carbon\Carbon;

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

    /**
     * @return Model|null
     */
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
            throw new RepositoryException("Falha interna ao salvar funcionário", 500);
        }
        
        return $this->model->find($model->ep_id);
    }

    public function updateAttendance(EmployeePlaceAttendanceDTO $dto){
        $model = $this->get($dto->ep_employee_id, $dto->ep_place_id);
        $model->ep_clock_in = $dto->ep_clock_in;
        $model->ep_clock_out = $dto->ep_clock_out;

        if(!$model->save()){
            throw new RepositoryException("Falha interna ao atualizar carga horária", 500);
        }
        
        return $model;
    }

    public function unset($idEmployee){
        $now = Carbon::now();
        $idOwner = auth('employee')->user()->emp_id;
        $idPlace = auth('employee')->user()->emp_place_id_logged;

        $employeePlace = $this->model::select('employees_place.*')
            ->join('places as p', 'p.pla_id', '=', 'employees_place.ep_place_id')
            ->join('employees as owner', 'owner.emp_id', '=', 'p.pla_employee_id')
            ->where('p.pla_id', '=', $idPlace)
            ->where('owner.emp_id', '=', $idOwner)
            ->where('employees_place.ep_employee_id', '=', $idEmployee)
            ->first();

        if(!empty($employeePlace)){
            $employeePlace->ep_deleted = true;
            $employeePlace->ep_deleted_at = $now->toDateTimeString();
            
            if(!$employeePlace->save()){
                throw new RepositoryException("Falha interna ao atualizar excluir funcionário", 500);
            }
            
            return true;
        }

        throw new RepositoryException("Não foi possível excluir o funcionário, motivo: funcionário não encontrado", 500);
    }

    public function getByServicesPlace(array $ids){
        return $this->model::select('employees_place.*')
        ->join('employees as e', 'e.emp_id', '=', 'employees_place.ep_employee_id')
        ->join('employees_services as es', 'es.emse_ep_id', '=', 'employees_place.ep_id')
        ->whereIn('es.emse_sepl_id', $ids)
        ->get();
    }
}

