<?php

namespace App\Http\Controllers;

use App\DTOs\CreateEmployeeDTO;
use App\DTOs\EmployeePlaceAttendanceDTO;
use App\Http\Requests\EmployeeRequest;
use App\Http\Requests\EmployeeByCpfRequest;
use App\Http\Requests\EmployeePlaceRequest;
use App\Http\Resources\Employee\EmployeePlaceAttendanceResource;
use App\Http\Resources\Employee\EmployeeResource;
use App\Services\EmployeeService as Service;

class EmployeeController extends Controller
{
    public function __construct(private Service $service){}
    
    public function types(){
        return $this->service->types();
    }

    public function one(EmployeeByCpfRequest $request){
        return new EmployeeResource($this->service->byCpf($request->query('cpf')));
    }

    public function create(EmployeeRequest $request){
        return new EmployeeResource($this->service->create(CreateEmployeeDTO::fromRequest($request)));
    }

    public function update(EmployeeRequest $request){
        return new EmployeeResource($this->service->update(CreateEmployeeDTO::fromRequest($request)));
    }

    public function updateAttendance(EmployeePlaceRequest $request){
        return $this->service->updateAttendance(EmployeePlaceAttendanceDTO::fromRequest($request));
    }

    public function listAttendance(){
        return new EmployeePlaceAttendanceResource($this->service->listAttendance());
    }

    public function list(){
        return EmployeeResource::collection($this->service->list());
    }

    public function delete($id){
        return $this->service->delete($id);
    }
}