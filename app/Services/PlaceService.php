<?php
namespace App\Services;

use App\DTOs\CreatePlaceAddressDTO;
use App\DTOs\PlaceDTO;
use App\Exceptions\ServiceException;
use App\Models\CategoryPlace;
use App\Repositories\CategoryPlaceRepository;
use App\Repositories\PlaceRepository;

class PlaceService {
    public function __construct(private PlaceRepository $repository, private CategoryPlaceRepository $cpRepository) {}

    public function create(PlaceDTO $place, CreatePlaceAddressDTO $address){
        $this->repository->create($place, $address);
        return response()->noContent(200);
    }

    public function getCategories(){
        return $this->cpRepository->list();
    }
}

