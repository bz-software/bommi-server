<?php

namespace App\Http\Controllers;
use App\Helpers\ControllerHelper as cHelper;
use App\Models\CategoryService;
use App\Models\EmployeePlace;
use App\Models\EmployeeService;
use App\Models\ServicePlace;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\VarDumper\VarDumper;

/**
 * Serviços que o estabelecimento realiza
 */
class EmployeeServiceController extends Controller
{

    public function one($id){
        
    }

    public function create(Request $request){
        cHelper::employeeNeedsSelectPlace();
        $data = $request->get('employeeService');

        $messages = array();

        $idEmployee = auth('employee')->user()->emp_id;
        $idPlace = auth('employee')->user()->emp_place_id_logged;

        $employeePlace = EmployeePlace::get($idEmployee, $idPlace);
        abort_if(!$employeePlace, 400);

        $service = ServicePlace::getById($data['emse_sepl_id']);
        abort_if(!$service, 400);

        $category = CategoryService::getByIdAndPlace($service['sepl_case_id'], $idPlace);
        if(empty($category)){
            $messages['emse_sepl_id'] = "Serviço não encontrado";
            return response()->json(['errors' => $messages], 400);
        }

        $data['emse_ep_id'] = $employeePlace['ep_id'];

        $empValidator = Validator::make($data, EmployeeService::rules(), EmployeeService::messages());
        if($empValidator->fails()){
            $messages = cHelper::getAllFirstMessages($empValidator->errors()->toArray());
            return response()->json(['errors' => $messages], 400);
        }

        if(!empty(EmployeeService::getByEmployeePlaceAndIdService($employeePlace['ep_id'], $service['sepl_id']))){
            $messages['emse_sepl_id'] = "Serviço já cadastrado";
            return response()->json(['errors' => $messages], 400);
        }

        $model = new EmployeeService();
        $model->emse_ep_id = $employeePlace['ep_id'];
        $model->emse_sepl_id = $data['emse_sepl_id'];

        abort_if(!$model->save(), 500);

        $employeeService = EmployeeService::getById($model->emse_id, EmployeeService::$ALL_RELATIONS);

        return response()->json($employeeService, 200);
    }

    public function list(){
        cHelper::employeeNeedsSelectPlace();
        
        $idEmployee = auth('employee')->user()->emp_id;
        $idPlace = auth('employee')->user()->emp_place_id_logged;

        $employeePlace = EmployeePlace::get($idEmployee, $idPlace);
        abort_if(!$employeePlace, 400);

        $employeeServices = EmployeeService::getAll($employeePlace->ep_id, EmployeeService::$ALL_RELATIONS);

        return response()->json(['employeeServices' => $employeeServices], 200);
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

    public function update(Request $request){
        
    }

    public function byCategory($idCategory){
        
    }

    
}