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

        $dto = $this->repository->store($dto);

        return response()->json($dto->toArray());
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

        $dto = $this->repository->update($dto);

        return response()->json($dto->toArray());
    }

    public function list(){
        $idPlace = auth('employee')->user()->emp_place_id_logged;
        $categories = $this->repository->getByPlace($idPlace);

        $dtos = collect();

        if ($categories->isNotEmpty()) {
            $dtos = $categories->map(fn($category) => CategoryServiceDTO::fromArray($category->toArray()));
        }

        return response()->json(['categories' => $dtos->map(fn($dto) => $dto->toArray())], 200);
    }

    public function delete($id){
        $idPlace = auth('employee')->user()->emp_place_id_logged;
        $id = intval($id);

        if(empty($id)){
            return response()->json([
                'errors' => ['case_id' => 'Campo obrigatório']
            ], 400);
        }

        $category = $this->repository->getByIdAndPlace($id, $idPlace);
        if(empty($category)){
            return response()->json([
                'errors' => ['case_id' => 'Categoria não pertence ao estabelecimento em uso']
            ], 400);
        }

        $category->case_deleted = true;
        $success = $category->save();

        if($success){
            return response()->json([], 200);
        }
        
        abort(500);
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

