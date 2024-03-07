<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Helpers\ControllerHelper as cHelper;
use App\Models\CategoryPlace;
use App\Models\Place;
use App\Models\PlaceAddress;
use Illuminate\Support\Facades\DB;

class PlaceController extends Controller
{
    public function create(Request $request){
        $place = $request->input('place');
        $address = $request->input('placeAddress');

        $placeValidator = Validator::make($place, Place::rules(), Place::messages());
        $placeAddressValidator = Validator::make($address, PlaceAddress::rules(), PlaceAddress::messages());

        if($placeValidator->fails()){
            $messages['place'] = cHelper::getAllFirstMessages($placeValidator->errors()->toArray());
        }

        if($placeAddressValidator->fails()){
            $messages['placeAddress'] = cHelper::getAllFirstMessages($placeAddressValidator->errors()->toArray());
        }
        
        if(!empty($messages)){
            return response()->json(['errors' => $messages], 400);
        }

        try {
            DB::beginTransaction();
                $placeSave = new Place($place);
                $placeSave->pla_employee_id = auth('employee')->user()->emp_id;

                if($placeSave->save()){
                    $placeAddressSave = new PlaceAddress($address);
                    $placeAddressSave->plad_place_id = $placeSave->pla_id;
        
                    $placeAddressSave->save();
                }
            DB::commit();
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json(['errors' => $e->getMessage()], 500);
        }

        return response()->json();
    }

    public function categories(){
        $categories = CategoryPlace::all();
        return response()->json(['categories' => $categories], 200);
    }
}
