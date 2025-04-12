<?php

namespace App\Http\Controllers;

use App\DTOs\CreatePlaceAddressDTO;
use App\DTOs\PlaceDTO;
use App\Http\Requests\CreatePlaceRequest;
use App\Http\Resources\CategoryPlace\CategoryPlaceResource;
use App\Services\PlaceService as Service;

class PlaceController extends Controller
{
    public function __construct(private Service $service){}

    public function create(CreatePlaceRequest $request){
        return $this->service->create(PlaceDTO::fromRequest($request), CreatePlaceAddressDTO::fromRequest($request));
    }

    public function categories(){
        return CategoryPlaceResource::collection($this->service->getCategories());
    }

    public function existBySlug($slug){
        return $this->service->existBySlug($slug);
    }
}
