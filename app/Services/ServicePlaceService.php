<?php
namespace App\Services;

use App\DTOs\ServicePlaceDTO;
use App\Repositories\ServicePlaceRepository;
use App\Repositories\CategoryServiceRepository;
use App\Exceptions\ServiceException;

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
            throw new ServiceException($message, 400);
        }

        if(empty($this->categoryRepository->getByIdAndPlace($dto->sepl_case_id, $idPlace))){
            $message['sepl_name'] = "Categoria não pertence ao estabelecimento em uso";
            throw new ServiceException($message, 400);
        }

        return $this->repository->store($dto);
    }

    public function delete($id){
        $idPlace = auth('employee')->user()->emp_place_id_logged;
        $id = intval($id);

        if(empty($id)){
            throw new ServiceException(['id' => 'Campo obrigatório'], 400);
        }

        $service = $this->repository->getById($id);
        if(empty($service)){
            throw new ServiceException(['id' => 'Serviço não encontrado'], 400);
        }

        $category = $this->categoryRepository->getByIdAndPlace($service['sepl_case_id'], $idPlace);
        if(empty($category)){
            throw new ServiceException(['id' => 'Serviço não pertence à categoria do estabelecimento em uso'], 400);
        }

        if($this->repository->delete($id)){
            return response()->noContent(200);
        }
    }

    public function update(ServicePlaceDTO $dto){
        $idPlace = auth('employee')->user()->emp_place_id_logged;

        if(empty($this->categoryRepository->getByIdAndPlace($dto->sepl_case_id, $idPlace))){
            $message['sepl_name'] = "Categoria não pertence ao estabelecimento em uso";
            throw new ServiceException($message, 400);
        }

        $service = $this->repository->getById($dto->sepl_id);
        if(empty($service)){
            $message['sepl_name'] = "Serviço não encontrado";
            throw new ServiceException($message, 400);
        }

        return $this->repository->update($dto);
    }

    public function byCategory($idCategory){
        $category = $this->categoryRepository->getById($idCategory);

        if(empty($category)){
            $message['sepl_case_id'] = "Categoria inválida";
            throw new ServiceException($message, 400);
        }

        return $this->repository->getAllByCategory($idCategory);
    }
}

