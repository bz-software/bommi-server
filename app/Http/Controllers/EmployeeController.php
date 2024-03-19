<?php

namespace App\Http\Controllers;
use App\Enums\EmployeeType;
use App\Helpers\ControllerHelper as cHelper;
use App\Models\Employee;
use Illuminate\Http\Request;
use PDO;

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
            $messages['cpf'] = "Para se denominar um funcionário, ative a opção nas configurações pessoais.";
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
}