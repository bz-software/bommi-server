<?php
namespace App\Services;

use App\DTOs\CategoryServiceDTO;
use App\Exceptions\RepositoryException;
use App\Repositories\CategoryServiceRepository;
use App\Exceptions\ServiceException;

class CategoryServiceService {
    public function __construct(private CategoryServiceRepository $repository) {}

    public function create(CategoryServiceDTO $dto)
    {
        $dto->case_place_id = auth('employee')->user()->emp_place_id_logged;

        if($this->exists($dto->case_place_id, $dto->case_name)){
            $message['name'] = "Categoria já cadastrada";
            throw new ServiceException($message, 400, 'Categoria já cadastrada');
        }

        return $this->repository->store($dto);
    }

    public function update(CategoryServiceDTO $dto){
        $dto->case_place_id = auth('employee')->user()->emp_place_id_logged;

        if($this->exists($dto->case_place_id, $dto->case_name, $dto->case_id)){
            $message['name'] = "Categoria já cadastrada";
            throw new ServiceException($message, 400);
        }

        if(empty($this->repository->getByIdAndPlace($dto->case_id, $dto->case_place_id))){
            $message['name'] = "Categoria não pertence ao estabelecimento ou não existe";
            throw new ServiceException($message, 400);
        }

        return $this->repository->update($dto);
    }

    public function list(){
        $idPlace = auth('employee')->user()->emp_place_id_logged;
        return $this->repository->getByPlace($idPlace);
    }

    public function delete($id){
        $idPlace = auth('employee')->user()->emp_place_id_logged;
        $id = intval($id);

        if(empty($id)){
            throw new ServiceException(['id' => 'Campo obrigatório'], 400);
        }

        $category = $this->repository->getByIdAndPlace($id, $idPlace);
        if(empty($category)){
            throw new ServiceException(['id' => 'Categoria não pertence ao estabelecimento em uso'], 400);
        }

        $category->case_deleted = true;
        $success = $category->save();

        if($success){
            return response()->noContent(200);
        }
        
        throw new RepositoryException("Falha interna ao excluir categoria", 500);
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

