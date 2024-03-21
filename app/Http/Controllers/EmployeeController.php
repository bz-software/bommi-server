<?php

namespace App\Http\Controllers;
use App\Enums\EmployeeType;
use App\Helpers\ControllerHelper as cHelper;
use App\Models\Employee;
use App\Models\EmployeePlace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class EmployeeController extends Controller
{
    public function init(){
        cHelper::employeeNeedsSelectPlace();

        $types = EmployeeType::array();
        $array = array();

        // Removendo OWNER
        foreach($types as $i => $type){
            if($type['value'] != EmployeeType::Owner->value){
                array_push($array, $type);
            }
        }

        return response()->json(['employeeTypes' => $array], 200);
    }

    public function one(Request $request){
        $messages = array();

        cHelper::employeeNeedsSelectPlace();
        $idOwner = auth('employee')->user()->emp_id;
        $owner = Employee::find($idOwner);
        $cpf = $request->query('cpf');

        if($cpf == $owner->emp_cpf){
            $messages['emp_cpf'] = "Para se denominar um funcionário, ative a opção nas configurações pessoais.";
            return response()->json(['errors' => $messages], 400);
        }

        $cpfValidator = Validator::make(['emp_cpf' => $cpf], ['emp_cpf' => 'required|cpf|size:11'], Employee::messages());
        if($cpfValidator->fails()){
            $messages = cHelper::getAllFirstMessages($cpfValidator->errors()->toArray());
            return response()->json(['errors' => $messages], 400);
        }

        if(!empty($cpf)){
            $employee = Employee::getByCpf($cpf, $idOwner);
            
            if(!empty($employee)){
                return response()->json(['employee' => $employee], 200);
            }else{
                return response()->json([], 404);
            }
        }
    }

    public function create(Request $request){
        cHelper::employeeNeedsSelectPlace();

        $messages = array();
        $employee = $request->get('employee');
        $reuse = $request->get('reuse'); // Define se deve criar um funcionario ou apenas a referencia para o estabelecimento
        $idPlace = auth('employee')->user()->emp_place_id_logged;

        if(boolval($reuse)){
            $messageEmpty = 'Esse funcionário não existe';
            if(empty($employee['emp_id'])){
                $messages['emp_cpf'] = $messageEmpty;
                return response()->json(['errors' => $messages], 400);
            }

            // Verificando se não está excluido
            $idEmp = $employee['emp_id'];
            $employeeFounded = Employee::getById($idEmp);

            if(empty($employeeFounded)){
                $messages['emp_cpf'] = $messageEmpty;
                return response()->json(['errors' => $messages], 400);
            }

            if(EmployeePlace::exist($employeeFounded->emp_id, $idPlace)){
                $messages['emp_cpf'] = "Funcionário já cadastrado nesse estabelecimento";
                return response()->json(['errors' => $messages], 400);
            }else{
                EmployeePlace::create($employeeFounded->emp_id, $idPlace, $employee['emp_type']);

                return response()->json(['employee' => Employee::getById($employeeFounded->emp_id, $idPlace)], 200);
            }

        }else{
            $empValidator = Validator::make($employee, Employee::rules(), Employee::messages());
            if($empValidator->fails()){
                $messages = cHelper::getAllFirstMessages($empValidator->errors()->toArray());
                return response()->json(['errors' => $messages], 400);
            }
    
            $employeeSave = new Employee($request->all());
            $employeeSave->emp_birthdate = Carbon::parse($employeeSave->emp_birthdate)->translatedFormat('Y-m-d');
            $employeeSave->emp_password = Hash::make($request->input('emp_password'));
            $employeeSave->emp_type = EmployeeType::Owner->value;
            $employeeSave->save();
        }


    }
}