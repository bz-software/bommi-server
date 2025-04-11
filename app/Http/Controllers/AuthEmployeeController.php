<?php

namespace App\Http\Controllers;

use App\DTOs\AuthEmployeeDTO;
use App\DTOs\CreateEmployeeAuthDTO;
use App\Http\Requests\AuthEmployeeRequest;
use App\Http\Requests\CreateEmployeeAuthRequest;
use App\Http\Resources\Auth\AuthResource;
use App\Services\AuthEmployeeService as Service;

class AuthEmployeeController extends Controller
{
    
    public function __construct(private Service $service){
        $this->middleware('auth:employee', ['except' => [
            'create',
            'login'
        ]]);
    }

    public function login(AuthEmployeeRequest $request){
        return new AuthResource($this->service->login(AuthEmployeeDTO::fromRequest($request)));
    }

    public function create(CreateEmployeeAuthRequest $request){
        return new AuthResource($this->service->createAuth(CreateEmployeeAuthDTO::fromRequest($request)));
    }

    public function logout(){
        return $this->service->logout();
    }
}
