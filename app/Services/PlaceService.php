<?php
namespace App\Services;

use App\DTOs\CreatePlaceAddressDTO;
use App\DTOs\PlaceDTO;
use App\Exceptions\ServiceException;
use App\Models\CategoryPlace;
use App\Repositories\CategoryPlaceRepository;
use App\Repositories\PlaceRepository;
use App\Models\Place as Model;

class PlaceService {
    public function __construct(private PlaceRepository $repository, private CategoryPlaceRepository $cpRepository) {}

    public function create(PlaceDTO $place, CreatePlaceAddressDTO $address){
        $this->repository->create($place, $address);
        return response()->noContent(200);
    }

    public function getCategories(){
        return $this->cpRepository->list();
    }

    public function existBySlug($slug){
        if(!empty($this->repository->getBySlug($slug))){
            return response()->noContent(200);
        }else{
            return response()->noContent(404);
        }
    }

    public function bySlug($slug){
        return $this->repository->getBySlug($slug, Model::$WITH_ALL);
    }
}

