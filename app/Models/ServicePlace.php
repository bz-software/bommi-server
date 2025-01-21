<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Symfony\Component\VarDumper\VarDumper;

class ServicePlace extends Model {
    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $primaryKey = "sepl_id";
    
    const REQUIRED_MSG = 'Campo obrigatório';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'services_places';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "sepl_name",
        "sepl_description",
        "sepl_case_id",
        "sepl_price",
        "sepl_duration"
    ];

    public static function rules($insert = true):array {
        $rules = [
            'sepl_name' => 'required|max:200',
            'sepl_description' => 'max:400',
            'sepl_case_id' => 'required',
            'sepl_price' => 'required',
            'sepl_duration' => 'required'
        ];

        if($insert){
            return $rules;
        }else{
            $rules['sepl_id'] = 'required';

            return $rules;
        } 
    }

    public static function messages():array {
        return [
            'sepl_id' => [
                'required' => self::REQUIRED_MSG,
            ],

            'sepl_name' => [
                'required' => self::REQUIRED_MSG,
                'max' => 'O nome deve conter no máximo 200 caracteres'
            ],

            'sepl_description' => [
                'max' => 'A descrição deve conter no máximo 400 caracteres'
            ],

            'sepl_case_id' => [
                'required' => self::REQUIRED_MSG,
                'max' => 'O nome deve conter no máximo 200 caracteres'
            ],

            'sepl_price' => [
                'required' => self::REQUIRED_MSG
            ],

            'sepl_duration' => [
                'required' => self::REQUIRED_MSG
            ]
        ];
    }

    public static function getAllByCategory($idCategory){
        $data = ServicePlace::select("*")
            ->where('sepl_case_id', $idCategory)
            ->where('sepl_deleted', false)
        ->get();

        return $data;
    }

    public static function getByNameAndCategory($idCategory, $name){
        $data = ServicePlace::select("*")
            ->whereRaw('LOWER(sepl_name) = ?', [$name])
            ->where('sepl_case_id', $idCategory)
            ->where('sepl_deleted', false)
        ->first();

        return $data;
    }

    public static function getById($id){
        $data = ServicePlace::select("*")
            ->where('sepl_id', $id)
            ->where('sepl_deleted', false)
        ->first();

        return $data;
    }
}
