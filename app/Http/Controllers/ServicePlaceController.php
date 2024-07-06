<?php

namespace App\Http\Controllers;
use App\Helpers\ControllerHelper as cHelper;
use App\Models\CategoryService;
use App\Models\EmployeePlace;
use App\Models\ServicePlace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\VarDumper\VarDumper;

class ServicePlaceController extends Controller
{

    public function one(Request $request){
        cHelper::employeeNeedsSelectPlace();
    }

    public function create(Request $request){
        cHelper::employeeNeedsSelectPlace();

        $idPlace = auth('employee')->user()->emp_place_id_logged;

        $data = $request->all();
        $validator = Validator::make($data, ServicePlace::rules(), ServicePlace::messages());
        if($validator->fails()){
            return response()->json([
                'errors' => cHelper::getAllFirstMessages($validator->errors()->toArray())
            ], 400);
        }

        $serviceFounded = ServicePlace::getByNameAndCategory($data['sepl_case_id'], $data['sepl_name']);
        if(!empty($serviceFounded)){
            $message['sepl_name'] = "Serviço já cadastrado";
            return response()->json([
                'errors' => $message
            ], 400);
        }

        if(empty(CategoryService::getByIdAndPlace($data['sepl_case_id'], $idPlace))){
            $message['sepl_name'] = "Categoria não pertence ao estabelecimento em uso";
            return response()->json([
                'errors' => $message
            ], 400);
        }

        $service = new ServicePlace($data);
        if($service->save()){
            return response()->json($service);
        }
    }

    public function list(){
        cHelper::employeeNeedsSelectPlace();
        $idPlace = auth('employee')->user()->emp_place_id_logged;
    }

    public function delete($id){
        cHelper::employeeNeedsSelectPlace();
        $idEmployee = $id;
    }

    public function byCategory($idCategory){
        cHelper::employeeNeedsSelectPlace();
        $category = CategoryService::getById($idCategory);

        if(empty($category)){
            $message['errors']['sepl_case_id'] = "Categoria inválida";
            return response()->json([
                'errors' => $message
            ], 400);
        }

        $services = ServicePlace::getAllByCategory($idCategory);
    
        return response()->json(['services' => $services], 200);
    }
}