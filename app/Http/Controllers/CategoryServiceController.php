<?php

namespace App\Http\Controllers;
use App\Enums\EmployeeType;
use App\Helpers\ControllerHelper as cHelper;
use App\Models\CategoryService;
use App\Models\Employee;
use App\Models\EmployeePlace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class CategoryServiceController extends Controller
{

    public function one(Request $request){
        cHelper::employeeNeedsSelectPlace();
    }

    public function create(Request $request){
        cHelper::employeeNeedsSelectPlace();
        $idPlace = auth('employee')->user()->emp_place_id_logged;

        $data = $request->all();

        $validator = Validator::make($data, CategoryService::rules());
        if($validator->fails()){
            return response()->json([
                'errors' => cHelper::getAllFirstMessages($validator->errors()->toArray())
            ], 400);
        }

        $categoryFounded = CategoryService::getByNameAndPlace($idPlace, $data['case_name']);
        if(!empty($categoryFounded)){
            $message['case_name'] = "Categoria já cadastrada";
            return response()->json([
                'errors' => $message
            ], 400);
        }

        $category = new CategoryService($data);
        $category->case_place_id = $idPlace;

        if($category->save()){
            return response()->json($category);
        }
    }

    public function update(Request $request){
        cHelper::employeeNeedsSelectPlace();

        $idPlace = auth('employee')->user()->emp_place_id_logged;

        $data = $request->all();

        $validator = Validator::make($data, CategoryService::rules(false));
        if($validator->fails()){
            return response()->json([
                'errors' => cHelper::getAllFirstMessages($validator->errors()->toArray())
            ], 400);
        }

        $duplicatedCategory = CategoryService::getByNameAndPlace($idPlace, $data['case_name'], $data['case_id']);
        if(!empty($duplicatedCategory)){
            $message['case_name'] = "Categoria já cadastrada";
            return response()->json([
                'errors' => $message
            ], 400);
        }

        $category = CategoryService::getByIdAndPlace($data['case_id'], $idPlace);
        if(empty($category)){
            $message['case_name'] = "Categoria não pertence ao estabelecimento ou não existe";
            return response()->json([
                'errors' => $message
            ], 400);
        }

        if($category->update($data)){
            return response()->json($category);
        }

        return response()->json([
            'errors' => "Erro inesperado"
        ], 500);
    }

    public function list(){
        cHelper::employeeNeedsSelectPlace();
        $idPlace = auth('employee')->user()->emp_place_id_logged;


        $categories = CategoryService::getByPlace($idPlace);
        return response()->json(['categories' => $categories], 200);
    }

    public function delete($id){
        cHelper::employeeNeedsSelectPlace();
        
        $idEmployee = $id;


        $success = EmployeePlace::unset($idEmployee);
        if($success){
            return response()->json([], 200);
        }else{
            return response()->json([], 500);
        }
    }
}