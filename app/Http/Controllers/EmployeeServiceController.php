<?php

namespace App\Http\Controllers;

use App\DTOs\EmployeeServiceDTO;
use App\Helpers\ControllerHelper as cHelper;
use App\Http\Requests\EmployeeServiceRequest;
use App\Models\CategoryService;
use App\Models\EmployeePlace;
use App\Models\EmployeeService;
use App\Models\ServicePlace;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\EmployeeServiceService as Service;
use App\Http\Resources\EmployeeService\EmployeeServiceResource;

/**
 * Serviços que o estabelecimento realiza
 */
class EmployeeServiceController extends Controller
{
    public function __construct(private Service $service) {}

    public function create(EmployeeServiceRequest $request){
        return new EmployeeServiceResource($this->service->create(EmployeeServiceDTO::fromRequest($request)));
    }

    public function list(){
        return EmployeeServiceResource::collection($this->service->list());
    }

    public function delete($id){
        return $this->service->destroy($id);
    }

    
}