<?php
namespace App\Services;

use App\DTOs\CategoryServiceDTO;
use App\Repositories\CategoryServiceRepository;

class CategoryServiceService {
    public function __construct(private CategoryServiceRepository $repository) {}

    public function create(CategoryServiceDTO $dto)
    {
        $idPlace = auth('employee')->user()->emp_place_id_logged;

        // $validator = Validator::make($data, CategoryService::rules());
        // if($validator->fails()){
        //     return response()->json([
        //         'errors' => cHelper::getAllFirstMessages($validator->errors()->toArray())
        //     ], 400);
        // }

        $categoryFounded = $this->repository->getByNameAndPlace($idPlace, $dto->name);
        if(!empty($categoryFounded)){
            $message['case_name'] = "Categoria já cadastrada";
            return response()->json([
                'errors' => $message
            ], 400);
        }

        $dto->idPlace = $idPlace;

        if($this->repository->store($dto)){
            return response()->json($dto->toArray());
        }
    }
}

