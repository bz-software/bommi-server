<?php
namespace App\Helpers;

class ControllerHelper {
    static function getAllFirstMessages(array $messages){
        $data = array();

        foreach($messages as $key => $message){
            $data[$key] = $message[0];
        }

        return $data;
    }
}