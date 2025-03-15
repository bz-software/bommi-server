<?php
namespace App\Repositories;

use App\DTOs\ServicePlaceDTO;
use App\Models\ServicePlace as ServicePlaceModel;

class ServicePlaceRepository
{
    /**
     * @param ServicePlaceModel $model
     */
    public function __construct(private ServicePlaceModel $model) {}

    public function store(ServicePlaceDTO $dto){
        $data = $dto->toModel();

        $service = new $this->model($data);

        if(!$service->save()){
            abort(500);
        }

        $dto->sepl_id = $service->sepl_id;
        
        return $dto;
    }

    public function getAllByCategory($idCategory){
        $data = $this->model->select("*")
            ->where('sepl_case_id', $idCategory)
            ->where('sepl_deleted', false)
        ->get();

        return $data;
    }

    public function getByNameAndCategory($idCategory, $name){
        $data = $this->model->select("*")
            ->whereRaw('LOWER(sepl_name) = ?', [$name])
            ->where('sepl_case_id', $idCategory)
            ->where('sepl_deleted', false)
        ->first();

        return $data;
    }

    public function getById($id){
        $data = $this->model->select("*")
            ->where('sepl_id', $id)
            ->where('sepl_deleted', false)
        ->first();

        return $data;
    }

    public function delete($id){
        $model = $this->getById($id);

        $model->sepl_deleted = true;
        return $model->save();
    }
}

