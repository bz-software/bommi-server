<?php
namespace App\Repositories;

use App\DTOs\CategoryServiceDTO;
use App\Models\CategoryService as CategoryServiceModel;

class CategoryServiceRepository
{
    public function __construct(private CategoryServiceModel $model) {}

    public function store(CategoryServiceDTO $dto)
    {
        $data = $dto->toModel();

        $category = new $this->model($data);

        if(!$category->save()){
            abort(500);
        }
        
        $dto->case_id = $category->case_id;
        
        return $dto;
    }

    public function update(CategoryServiceDTO $dto)
    {
        $data = $dto->toModel();
        
        $category = $this->model->find($dto->case_id);
        $category->fill($data);

        if(!$category->save()){
            abort(500);
        }

        return $dto->fromArray($category->toArray());
    }

    public function getByNameAndPlace($idPlace, $name, $idExcept = null){
        $name = strtolower($name);

        $data = CategoryServiceModel::select("*")
            ->whereRaw('LOWER(case_name) = ?', [$name])
            ->where('case_place_id', $idPlace)
            ->where('case_deleted', false)
            ->when($idExcept !== null, function ($query) use ($idExcept) {
                $query->where('case_id', '!=', $idExcept);
            })
        ->first();

        return $data;
    }

    public function getByPlace($idPlace){
        return CategoryServiceModel::select("*")
            ->where('case_place_id', '=', $idPlace)
            ->where('case_deleted', '=', false)
        ->get();
    }

    public function getById($id){
        return CategoryServiceModel::select("*")
            ->where('case_id', '=', $id)
            ->where('case_deleted', '=', false)
        ->get();
    }

    public function getByIdAndPlace($id, $idPlace){
        return CategoryServiceModel::select("*")
            ->where('case_id', '=', $id)
            ->where('case_place_id', $idPlace)
            ->where('case_deleted', '=', false)
        ->first();
    }
}

