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
    public function update(ServicePlaceRequest $request){
        cHelper::employeeNeedsSelectPlace();
        return $this->service->update(ServicePlaceDTO::fromRequest($request));
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