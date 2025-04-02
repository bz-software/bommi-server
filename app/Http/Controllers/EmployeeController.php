<?php

namespace App\Http\Controllers;

use App\DTOs\CreateEmployeeDTO;
use App\Enums\EmployeeType;
use App\Helpers\ControllerHelper as cHelper;
use App\Http\Requests\CreateEmployeeRequest;
use App\Http\Requests\EmployeeByCpfRequest;
use App\Http\Resources\Auth\AuthResource;
use App\Http\Resources\Employee\EmployeeResource;
use App\Models\Employee;
use App\Models\EmployeePlace;
use App\Services\EmployeeService as Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Validators\AvatarValidator;

class EmployeeController extends Controller
{
    public function __construct(private Service $service){}
    
    public function types(){
        return $this->service->types();
    }

    public function one(EmployeeByCpfRequest $request){
        return new EmployeeResource($this->service->byCpf($request->query('cpf')));
    }

    public function create(CreateEmployeeRequest $request){
        return new EmployeeResource($this->service->create(CreateEmployeeDTO::fromRequest($request)));
    }

    public function update(Request $request){
        cHelper::employeeNeedsSelectPlace();        

        $data = $request->except('avatar');
        $avatar = $request->file('avatar');

        $id = auth('employee')->user()->emp_id;

        $data['emp_cpf'] = !empty($data['emp_cpf']) ? preg_replace('/\D/', '', $data['emp_cpf']) : null;

        $validator = Validator::make($data, Employee::rules(false, $id), Employee::messages());
        $avatarValidator = AvatarValidator::validate($request->all());
        if($validator->fails() || $avatarValidator->fails()){
            $messages = cHelper::getAllFirstMessages($validator->errors()->toArray());
            $avatarMessages = cHelper::getAllFirstMessages($avatarValidator->errors()->toArray());
            
            return response()->json([
                'errors' => array_merge($messages, $avatarMessages)
            ], 400);
        }

        $employee = Employee::getById($id);
        $employee->emp_first_name = $data['emp_first_name'];
        $employee->emp_last_name = $data['emp_last_name'];
        $employee->emp_cpf = $data['emp_cpf'];
        $employee->emp_birthdate = Carbon::parse($data['emp_birthdate'])->translatedFormat('Y-m-d');

        // TODO: ver outra forma de buscar o tipo de usuário
        unset($employee->emp_type_name);

         // Se o arquivo de imagem foi enviado
        if ($request->hasFile('avatar')) {
            $avatarName = $id . '_' . rand(0, 9999) . '.' . $avatar->getClientOriginalExtension();
            $avatar->storeAs('public/employee_avatar', $avatarName);
            $employee->emp_avatar = $avatarName;
        }

        $employee->update();

        return response()->json(['employee' => $employee]);
    }

    public function updateAttendance(Request $request){
        cHelper::employeeNeedsSelectPlace();

        $data = $request->all();

        $idPlace = auth('employee')->user()->emp_place_id_logged;
        $idEmployee = auth('employee')->user()->emp_id;

        $employeePlace = EmployeePlace::get($idEmployee, $idPlace);
        abort_if(empty($employeePlace), 404);

        $validator = Validator::make($request->all(), EmployeePlace::rules(), EmployeePlace::messages());
        if($validator->fails()){
            $messages = cHelper::getAllFirstMessages($validator->errors()->toArray());
            
            return response()->json([
                'errors' => $messages
            ], 400);
        }

        $employeePlace->ep_clock_in = $data['ep_clock_in'];
        $employeePlace->ep_clock_out = $data['ep_clock_out'];
        
        if($employeePlace->update()){
            return response('', 200);
        }else{
            abort(500);
        }
    }

    public function listAttendance(){
        cHelper::employeeNeedsSelectPlace();

        $idPlace = auth('employee')->user()->emp_place_id_logged;
        $idEmployee = auth('employee')->user()->emp_id;

        $employeePlace = EmployeePlace::get($idEmployee, $idPlace);
        abort_if(empty($employeePlace), 404);

        return response()->json($employeePlace);
    }

    public function list(){
        cHelper::employeeNeedsSelectPlace();
        $idUser = auth('employee')->user()->emp_id;
        $idPlace = auth('employee')->user()->emp_place_id_logged;


        $employees = Employee::getAllActiveByPlace($idPlace, $idUser);
        return response()->json(['employees' => $employees], 200);
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