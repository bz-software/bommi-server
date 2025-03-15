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

/**
 * Serviços que o estabelecimento realiza
 */
class ServicePlaceController extends Controller
{
    public function __construct(private Service $service) {}

    public function one($id){
        cHelper::employeeNeedsSelectPlace();

        return response()->json(ServicePlace::getById($id));
    }

    public function create(ServicePlaceRequest $request){
        cHelper::employeeNeedsSelectPlace();
        return $this->service->create(ServicePlaceDTO::fromRequest($request));
    }

    public function list(){
        cHelper::employeeNeedsSelectPlace();
    }

    public function delete($id){
        cHelper::employeeNeedsSelectPlace();
        return $this->service->delete($id);
    }

    /**
     * TODO: validate whether the updated service name already exists in another service
     */
    public function update(Request $request){
        cHelper::employeeNeedsSelectPlace();

        $idPlace = auth('employee')->user()->emp_place_id_logged;

        $data = $request->all();

        $validator = Validator::make($data, ServicePlace::rules(false), ServicePlace::messages());
        if($validator->fails()){
            return response()->json([
                'errors' => cHelper::getAllFirstMessages($validator->errors()->toArray())
            ], 400);
        }

        if(empty(CategoryService::getByIdAndPlace($data['sepl_case_id'], $idPlace))){
            $message['sepl_name'] = "Categoria não pertence ao estabelecimento em uso";
            return response()->json([
                'errors' => $message
            ], 400);
        }

        $service = ServicePlace::getById($data['sepl_id']);
        if(empty($service)){
            $message['sepl_name'] = "Serviço não encontrado";
            return response()->json([
                'errors' => $message
            ], 400);
        }


        if($service->update($data)){
            return response()->json($service);
        }

        return response()->json([
            'errors' => "Erro inesperado"
        ], 500);
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