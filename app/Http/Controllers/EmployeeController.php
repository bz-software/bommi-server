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

    public function getByOwner(Request $request){
        cHelper::employeeNeedsSelectPlace();
        $cpf = $request->query('cpf');

        if(!empty($cpf)){
            $idOwner = auth('employee')->user()->emp_id;

            // SELECT * FROM employees e
            // INNER JOIN employees_place ep ON ep.ep_employee_id = e.emp_id
            // INNER JOIN places p ON p.pla_id = ep.ep_place_id
            // WHERE p.pla_employee_id = 65
            // AND e.emp_id != 65
    
            $employees =  Employee::select('*')
                ->join('employees_place', 'employees_place.ep_employee_id', '=', 'employees.emp_id')
                ->join('places', 'places.pla_id', '=', 'employees_place.ep_place_id')
            ->where('places.pla_employee_id', $idOwner)
            ->where('employees.emp_id', '!=', $idOwner)
            ->where('employees.emp_cpf', 'LIKE', $cpf.'%')
            ->get();
    
            return response()->json(['employees' => $employees], 200);
        }
    }
}