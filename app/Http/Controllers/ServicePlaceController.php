<?php

namespace App\Http\Controllers;
use App\Helpers\ControllerHelper as cHelper;
use App\Models\CategoryService;
use App\Models\ServicePlace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\ServicePlaceService as Service;
use App\DTOs\ServicePlaceDTO;
use App\Http\Requests\ServicePlaceRequest;
use App\Http\Resources\ServicePlace\ServicePlaceResource;

/**
 * Serviços que o estabelecimento realiza
 */
class ServicePlaceController extends Controller
{
    public function __construct(private Service $service) {}

    public function create(ServicePlaceRequest $request){
        cHelper::employeeNeedsSelectPlace();
        return new ServicePlaceResource($this->service->create(ServicePlaceDTO::fromRequest($request)));
    }

    public function delete($id){
        cHelper::employeeNeedsSelectPlace();
        return $this->service->delete($id);
    }

    /**
     * TODO: validate whether the updated service name already exists in another service
     */
    public function update(ServicePlaceRequest $request){
        cHelper::employeeNeedsSelectPlace();
        return new ServicePlaceResource($this->service->update(ServicePlaceDTO::fromRequest($request)));
    }

    public function byCategory($idCategory){
        cHelper::employeeNeedsSelectPlace();
        return ServicePlaceResource::collection($this->service->byCategory($idCategory));
    }
}