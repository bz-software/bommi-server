<?php
namespace App\Repositories;

use App\DTOs\ServicePlaceDTO;
use App\Exceptions\RepositoryException;
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
            throw new RepositoryException("Falha interna ao salvar serviço", 500);
        }
        
        return $this->model->find($service['sepl_id']);
    }

    public function getAllByCategory($idCategory){
        $data = $this->model->select("*")
            ->where('sepl_case_id', $idCategory)
            ->where('sepl_deleted', false)
        ->get();

        return $data;
    }

    public function getByNameAndCategory($idCategory, $name, $idExcept = null){
        $data = $this->model->select("*")
            ->join('categories_services as cs', 'cs.case_id', '=', 'services_places.sepl_case_id')
            ->whereRaw('LOWER(sepl_name) = ?', [$name])
            ->where('cs.case_deleted', 0)
            ->where('sepl_case_id', $idCategory)
            ->where('sepl_deleted', false)
            ->when($idExcept !== null, function ($query) use ($idExcept) {
                $query->where('sepl_id', '!=', $idExcept);
            })
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

    /**
     * Calculates the total duration (in minutes) and total value of services
     * based on the given service place IDs.
     *
     * The duration is stored in "HH:MM" format and converted to total minutes.
     * The price is summed directly from the 'sepl_price' column.
     *
     * @param array $ids The list of service place IDs to calculate totals for.
     * @return object An object containing 'totalDuration' and 'totalValue'.
     */
    public function totalByIds(array $ids)
    {
        return $this->model->whereIn('sepl_id', $ids)
            ->selectRaw('
                FLOOR(SUM(TIME_TO_SEC(sepl_duration)) / 60) AS duration,
                SUM(sepl_price) AS value
            ')
            ->first();
    }

    public function getByIdAndPlace($id, $idPlace){
        return $this->model->select('*')
            ->join('categories_services as cs', 'cs.case_id', '=', 'services_places.sepl_case_id')
            ->join('places as p', 'cs.case_place_id', '=', 'p.pla_id')
            ->where('services_places.sepl_id', $id)
            ->where('p.pla_id', $idPlace)
            ->where('cs.case_deleted', 0)
            ->where('p.pla_deleted', 0)
            ->where('services_places.sepl_deleted', 0)
            ->first();
    }

    public function delete($id){
        $service = $this->getById($id);

        $service->sepl_deleted = true;

        if(!$service->save()){
            throw new RepositoryException("Falha interna ao excluir serviço", 500);
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
            throw new RepositoryException("Falha interna ao alterar serviço", 500);
        }

        return $service;
    }
}

