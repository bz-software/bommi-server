<?php
namespace App\Repositories;

use App\DTOs\EmployeeServiceDTO;
use App\Exceptions\RepositoryException;
use App\Models\EmployeeService as Model;

class EmployeeServiceRepository
{
    public function __construct(private Model $model) {}

    public function getByEmployeePlaceAndIdService($idEmployeePlace, $idService){
        return $this->model::select("*")
            ->where('emse_ep_id', $idEmployeePlace)
            ->where('emse_sepl_id', $idService)
            ->where('emse_deleted', 0)
            ->first();
    }

    public function store(EmployeeServiceDTO $dto){
        $model = new $this->model;
        $model->emse_ep_id = $dto->ep_id;
        $model->emse_sepl_id = $dto->emse_sepl_id;
        
        if(!$model->save()){
            throw new RepositoryException("Falha interna ao serviço do funcionário", 500);
        }
        
        return $this->model->find($model->emse_id);
    }

    public function getById($id, $with = [])
    {
        return $this->model::with($with)
            ->where('emse_id', $id)
            ->where('emse_deleted', 0)
            ->first();
    }

    public function getAll($idEmployeePlace, $with = [])
    {
        return $this->model::with($with)
            ->where('emse_ep_id', $idEmployeePlace)
            ->where('emse_deleted', 0)
            ->get();
    }
}

