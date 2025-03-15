<?php
namespace App\Services;

use App\DTOs\ServicePlaceDTO;
use App\Repositories\ServicePlaceRepository;
use App\Repositories\CategoryServiceRepository;

class ServicePlaceService {
    public function __construct(
        private ServicePlaceRepository $repository,
        private CategoryServiceRepository $categoryRepository
    ) {}

    public function create(ServicePlaceDTO $dto){
        $idPlace = auth('employee')->user()->emp_place_id_logged;

        $serviceFounded = $this->repository->getByNameAndCategory($dto->sepl_case_id, $dto->sepl_name);
        if(!empty($serviceFounded)){
            $message['sepl_name'] = "Serviço já cadastrado";
            return response()->json(['errors' => $message], 400);
        }

        if(empty($this->categoryRepository->getByIdAndPlace($dto->sepl_case_id, $idPlace))){
            $message['sepl_name'] = "Categoria não pertence ao estabelecimento em uso";
            return response()->json(['errors' => $message], 400);
        }

        $dto = $this->repository->store($dto);

        return response()->json($dto->toArray(), 200);
    }

    public function delete($id){
        $idPlace = auth('employee')->user()->emp_place_id_logged;
        $id = intval($id);

        if(empty($id)){
            return response()->json([ 'errors' => ['sepl_id' => 'Campo obrigatório'] ], 400);
        }

        $service = $this->repository->getById($id);
        if(empty($service)){
            return response()->json([ 'errors' => ['sepl_id' => 'Serviço não encontrado'] ], 400);
        }

        $category = $this->categoryRepository->getByIdAndPlace($service['sepl_case_id'], $idPlace);
        if(empty($category)){
            return response()->json([ 'errors' => ['sepl_id' => 'Serviço não pertence à categoria do estabelecimento em uso'] ], 400);
        }

        $this->repository->delete($id);
        return response('');
    }

    public function update(ServicePlaceDTO $dto){
        $idPlace = auth('employee')->user()->emp_place_id_logged;

        if(empty($this->categoryRepository->getByIdAndPlace($dto->sepl_case_id, $idPlace))){
            $message['sepl_name'] = "Categoria não pertence ao estabelecimento em uso";
            return response()->json( [ 'errors' => $message ], 400);
        }

        $service = $this->repository->getById($dto->sepl_id);
        if(empty($service)){
            $message['sepl_name'] = "Serviço não encontrado";
            return response()->json([ 'errors' => $message ], 400);
        }

        $dto = $this->repository->update($dto);
        return response()->json($dto->toArray());
    }
}

