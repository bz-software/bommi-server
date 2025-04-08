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
        $now = Carbon::now();
        cHelper::employeeNeedsSelectPlace();
        abort_if(!$id, 400);

        $idEmployee = auth('employee')->user()->emp_id;
        $idPlace = auth('employee')->user()->emp_place_id_logged;

        $employeePlace = EmployeePlace::get($idEmployee, $idPlace);
        abort_if(!$employeePlace, 400);

        $data = EmployeeService::findOrFail($id);

        $service = ServicePlace::getById($data['emse_sepl_id']);
        abort_if(!$service, 400);

        $category = CategoryService::getByIdAndPlace($service['sepl_case_id'], $idPlace);
        abort_if(!$category, 400);

        // same employee and place
        if($data['emse_ep_id'] == $employeePlace['ep_id'] && $category['case_place_id'] == $employeePlace['ep_place_id']){
            $data['emse_deleted'] = 1;
            $data['emse_deleted_at'] = $now->toDateTimeString();

            if(!$data->update()){
                abort(500);
            }

            return response()->json([], 200);
        }else{
            abort(400);
        }

    }

    
}