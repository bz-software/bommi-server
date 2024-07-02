<?php

namespace App\Http\Controllers;
use App\Helpers\ControllerHelper as cHelper;
use App\Models\CategoryService;
use App\Models\EmployeePlace;
use App\Models\ServicePlace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServicePlaceController extends Controller
{

    public function one(Request $request){
        cHelper::employeeNeedsSelectPlace();
    }

    public function create(Request $request){
        cHelper::employeeNeedsSelectPlace();
        $idPlace = auth('employee')->user()->emp_place_id_logged;
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