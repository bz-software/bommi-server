<?php

namespace App\Http\Controllers;
use App\Helpers\ControllerHelper as cHelper;
use App\Http\Requests\StoreCategoryServiceRequest;
use Illuminate\Http\Request;
use App\Services\CategoryServiceService as Service;
use App\DTOs\CategoryServiceDTO;
use App\Http\Resources\CategoryService\CategoryServiceResource;

class CategoryServiceController extends Controller
{
    public function __construct(private Service $service) {}


    public function one(Request $request){
        cHelper::employeeNeedsSelectPlace();
    }

    public function create(StoreCategoryServiceRequest $request){
        cHelper::employeeNeedsSelectPlace();
        return new CategoryServiceResource($this->service->create(CategoryServiceDTO::fromRequest($request)));
    }

    public function update(StoreCategoryServiceRequest $request){
        cHelper::employeeNeedsSelectPlace();
        return new CategoryServiceResource($this->service->update(CategoryServiceDTO::fromRequest($request)));
    }

    public function list(){
        cHelper::employeeNeedsSelectPlace();
        return CategoryServiceResource::collection($this->service->list());
    }

    public function delete($id){
        cHelper::employeeNeedsSelectPlace();
        return $this->service->delete($id);
    }
}