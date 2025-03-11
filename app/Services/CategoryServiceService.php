<?php
namespace App\Services;

use App\DTOs\CategoryServiceDTO;
use App\Repositories\CategoryServiceRepository;

class CategoryServiceService {
    public function __construct(private CategoryServiceRepository $repository) {}

    public function create(CategoryServiceDTO $dto)
    {
        $dto->case_place_id = auth('employee')->user()->emp_place_id_logged;

        if($this->exists($dto->case_place_id, $dto->case_name)){
            $message['name'] = "Categoria já cadastrada";
            return response()->json([
                'errors' => $message
            ], 400);
        }

        if($this->repository->store($dto)){
            return response()->json($dto->toArray());
        }
    }

    public function update(CategoryServiceDTO $dto){
        $dto->case_place_id = auth('employee')->user()->emp_place_id_logged;

        if($this->exists($dto->case_place_id, $dto->case_name, $dto->case_id)){
            $message['case_name'] = "Categoria já cadastrada";
            return response()->json([
                'errors' => $message
            ], 400);
        }

        if(empty($this->repository->getByIdAndPlace($dto->case_id, $dto->case_place_id))){
            $message['case_name'] = "Categoria não pertence ao estabelecimento ou não existe";
            return response()->json([
                'errors' => $message
            ], 400);
        }

        if($this->repository->update($dto)){
            return response()->json($dto);
        }

        return response()->json([
            'errors' => "Erro inesperado"
        ], 500);
    }

    public function exists($idPlace, $name, $idExcept = null)
    {
        $category = $this->repository->getByNameAndPlace($idPlace, $name, $idExcept);

        if(!empty($category)){
            return true;
        }

        return false;
    }
}

