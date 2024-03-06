<?php

namespace App\Http\Controllers;

use App\Enums\Category;
use App\Enums\EmployeeType;
use Illuminate\Http\Request;
use App\Helpers\ControllerHelper as cHelper;
use App\Models\Employee;
use App\Models\Place;

class DashboardController extends Controller
{
    public function init(Request $request){
        $idEmployee = auth('employee')->user()->emp_id;

        $employee = Employee::find($idEmployee);
        $employee['hierarchy'] = EmployeeType::name($employee['emp_type']);

        if($employee['emp_type'] == EmployeeType::Owner->value){
            $place = Place::where('pla_employee_id', $idEmployee)->first();
        }

        return response()->json([
            'place' => $place->load('address'),
        ], 200);
    }
}
