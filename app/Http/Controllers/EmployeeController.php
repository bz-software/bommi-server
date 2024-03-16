<?php

namespace App\Http\Controllers;
use App\Enums\EmployeeType;
use App\Helpers\ControllerHelper as cHelper;


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
}