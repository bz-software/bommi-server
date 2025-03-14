<?php

namespace App\Http\Controllers;
use App\Helpers\ControllerHelper as cHelper;
use App\Http\Requests\StoreCategoryServiceRequest;
use App\Models\CategoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\CategoryServiceService as Service;
use App\DTOs\CategoryServiceDTO;

class CategoryServiceController extends Controller
{
    public function __construct(private Service $service) {}


    public function one(Request $request){
        cHelper::employeeNeedsSelectPlace();
    }

    public function create(StoreCategoryServiceRequest $request){
        cHelper::employeeNeedsSelectPlace();
        return $this->service->create(CategoryServiceDTO::fromRequest($request));
    }

    public function update(Request $request){
        cHelper::employeeNeedsSelectPlace();
        return $this->service->update(CategoryServiceDTO::fromRequest($request));
    }

    public function list(){
        cHelper::employeeNeedsSelectPlace();
        return $this->service->list();
    }

    public function delete($id){
        cHelper::employeeNeedsSelectPlace();
        return $this->service->delete($id);
    }
}