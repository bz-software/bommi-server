<?php

namespace App\Http\Controllers;

use App\DTOs\CreatePlaceAddressDTO;
use App\DTOs\PlaceDTO;
use App\Http\Requests\CreatePlaceRequest;
use App\Http\Resources\CategoryPlace\CategoryPlaceResource;
use App\Http\Resources\Employee\EmployeeWithEmployeePlaceResource;
use App\Http\Resources\Place\PlaceAllRelationsResource;
use App\Services\PlaceService as Service;

class PlaceController extends Controller
{
    public function __construct(private Service $service){}

    public function create(CreatePlaceRequest $request){
        return new EmployeeWithEmployeePlaceResource($this->service->create(PlaceDTO::fromRequest($request), CreatePlaceAddressDTO::fromRequest($request)));
    }

    public function categories(){
        return CategoryPlaceResource::collection($this->service->getCategories());
    }

    public function existBySlug($slug){
        return $this->service->existBySlug($slug);
    }

    public function getAllBySlug($slug){
        return new PlaceAllRelationsResource($this->service->bySlug($slug));
    }
}
