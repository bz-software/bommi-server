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
        $idPlace = auth('employee')->user()->emp_place_id_logged;


        $categories = CategoryService::getByPlace($idPlace);
        return response()->json(['categories' => $categories], 200);
    }

    public function delete($id){
        cHelper::employeeNeedsSelectPlace();
        
        $idPlace = auth('employee')->user()->emp_place_id_logged;
        $id = intval($id);

        if(empty($id)){
            return response()->json([
                'errors' => ['case_id' => 'Campo obrigatório']
            ], 400);
        }

        $category = CategoryService::getByIdAndPlace($id, $idPlace);
        if(empty($category)){
            return response()->json([
                'errors' => ['case_id' => 'Categoria não pertence ao estabelecimento em uso']
            ], 400);
        }

        $category->case_deleted = true;
        $success = $category->save();

        if($success){
            return response()->json([], 200);
        }else{
            return response()->json([], 500);
        }
    }
}