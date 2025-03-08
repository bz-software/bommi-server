<?php
namespace App\Repositories;

use App\DTOs\CategoryServiceDTO;
use App\Models\CategoryService as CategoryServiceModel;

class CategoryServiceRepository
{
    public function __construct(private CategoryServiceModel $model) {}

    public function store(CategoryServiceDTO $dto)
    {
        $category = new $this->model($dto);

        $category->save();

        return $category;
    }

    public static function getByNameAndPlace($idPlace, $name, $idExcept = null){
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

    public static function getByPlace($idPlace){
        return CategoryServiceModel::select("*")
            ->where('case_place_id', '=', $idPlace)
            ->where('case_deleted', '=', false)
        ->get();
    }

    public static function getById($id){
        return CategoryServiceModel::select("*")
            ->where('case_id', '=', $id)
            ->where('case_deleted', '=', false)
        ->get();
    }

    public static function getByIdAndPlace($id, $idPlace){
        return CategoryServiceModel::select("*")
            ->where('case_id', '=', $id)
            ->where('case_place_id', $idPlace)
            ->where('case_deleted', '=', false)
        ->first();
    }
}

