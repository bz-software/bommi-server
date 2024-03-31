<?php
namespace App\Helpers;
use App\Models\Place;
use Illuminate\Http\Exceptions\HttpResponseException;

class ControllerHelper {
    static function getAllFirstMessages(array $messages){
        $data = array();

        foreach($messages as $key => $message){
            $data[$key] = $message[0];
        }

        return $data;
    }
    
    public static function employeeNeedsSelectPlace(){
        $id = auth('employee')->user()->emp_id;

        $place = Place::getByIdEmployee($id);

        if(is_countable($place)){
            http_response_code(300);
            exit;
        }else{
            if($place === null){
                http_response_code(206);
                exit;
            }
        }

        return true;
    }
}