<?php
namespace App\Helpers;

trait EnumToArray {
    public static function names(): array {
        return array_column(self::cases(), 'name');
    }

    public static function values(): array{
        return array_column(self::cases(), 'value');
    }

    public static function array(): array{
        $data = array_combine(self::values(), self::names());

        $arrayReturn = array();

        foreach($data as $i => $value){
            array_push($arrayReturn, ['value' => $i, 'text' => $value]);
        }

        return $arrayReturn;
    }

    public static function name($id){
        foreach (self::cases() as $status) {
            if( $id === $status->value ){
                return $status->name;
            }
        }
    }
}