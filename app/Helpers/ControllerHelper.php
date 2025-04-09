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

        if (is_countable($place)) {
            abort(300, 'Múltiplos locais encontrados');
        } elseif ($place === null) {
            abort(206);
        }

        return true;
    }
}