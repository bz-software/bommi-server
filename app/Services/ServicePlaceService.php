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
}

