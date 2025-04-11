<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\ControllerHelper as cHelper;
use App\Models\Place;

class DashboardController extends Controller
{
    public function init(Request $request){
        cHelper::employeeNeedsSelectPlace();
        
        $place = Place::getByIdEmployee(auth('employee')->user()->emp_id);  

        return response()->json([
            'place' => $place,
        ], 200);
    }
}
