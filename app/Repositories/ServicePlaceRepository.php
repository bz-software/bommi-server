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
        $service = $this->getById($id);

        $service->sepl_deleted = true;

        if(!$service->save()){
            abort(500);
        }

        return true;
    }

    public function update(ServicePlaceDTO $dto){
        $service = $this->model->find($dto->sepl_id);
        $service->sepl_name = $dto->sepl_name;
        $service->sepl_description = $dto->sepl_description;
        $service->sepl_price = $dto->sepl_price;
        $service->sepl_duration = $dto->sepl_duration;

        if(!$service->save()){
            abort(500);
        }

        return $dto->fromArray($service->toArray());
    }
}

