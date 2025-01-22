<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryService extends Model {
    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $primaryKey = "case_id";
    
    const REQUIRED_MSG = 'Campo obrigatório';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'categories_services';

    protected $hidden = [
        'case_deleted',
        'case_place_id'
    ]; 

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "case_name"
    ];

    public static function rules($insert = true):array {
        $rules = [
            'case_name' => 'required|max:300',
        ];

        if(!$insert){
            $rules['case_id'] = 'required';
        }

        return $rules;
    }

    public static function messages():array {
        return [
            'case_name' => [
                'required' => self::REQUIRED_MSG,
                'max' => 'O nome deve conter no máximo 300 caracteres'
            ]
        ];
    }

    public static function getByNameAndPlace($idPlace, $name, $idExcept = null){
        $name = strtolower($name);

        $data = CategoryService::select("*")
            ->whereRaw('LOWER(case_name) = ?', [$name])
            ->where('case_place_id', $idPlace)
            ->where('case_deleted', false)
            ->when($idExcept !== null, function ($query) use ($idExcept) {
                $query->where('case_id', '!=', $idExcept);
            })
        ->first();

        return $data;
    }

    public static function getByPlace($idPlace){
        return CategoryService::select("*")
            ->where('case_place_id', '=', $idPlace)
            ->where('case_deleted', '=', false)
        ->get();
    }

    public static function getById($id){
        return CategoryService::select("*")
            ->where('case_id', '=', $id)
            ->where('case_deleted', '=', false)
        ->get();
    }

    public static function getByIdAndPlace($id, $idPlace){
        return CategoryService::select("*")
            ->where('case_id', '=', $id)
            ->where('case_place_id', $idPlace)
            ->where('case_deleted', '=', false)
        ->first();
    }
}
