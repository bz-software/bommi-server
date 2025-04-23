<?php
namespace App\Repositories;

use App\DTOs\AppointmentDTO;
use App\Exceptions\RepositoryException;
use App\Models\Appointment as Model;
use App\Models\EmployeePlace;
use Carbon\Carbon;

class AppointmentRepository
{
    public function __construct(private Model $model, private EmployeePlace $epModel) {}

    public function store(AppointmentDTO $dto)
    {
        $data = $dto->toArray();
        
        $model = new $this->model();
        $model->fill($data);

        if(!$model->save()){
            throw new RepositoryException("Falha interna ao salvar agendamento", 500);
        }
        
        return $this->model->find($model['apmt_id']);
    }


    public function employeeAvaliable($start, $end, $idEmployeePlace){
        $employee = $this->epModel::where('ep_id', $idEmployeePlace)
        ->whereDoesntHave('appointments', function ($query) use ($start, $end) {
            $query->where('apmt_date', '>=', $start)
                  ->where('apmt_date', '<', $end);
        })
        ->first();

        if(!empty($employee)){
            return true;
        }

        return false;
    }

    public function byDate($idEmployeePlace, $date){
        return $this->model::where('apmt_ep_id', $idEmployeePlace)
        ->whereDate('apmt_date', $date)
        ->get();
    }
}

