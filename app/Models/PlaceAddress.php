<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlaceAddress extends Model {
    const CREATED_AT = null;
    const UPDATED_AT = 'plad_changed_at';

    protected $primaryKey = "plad_id";

    const REQUIRED_MSG = 'Campo obrigatório';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'places_address';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "plad_cep",
        "plad_uf",
        "plad_city",
        "plad_district",
        "plad_number",
        "plad_complement",
        "plad_lat",
        "plad_lon",
    ];

    public static function rules():array {
        return [
            'plad_cep' => 'required|size:8',
            'plad_uf' => 'required|size:2',
            "plad_city" => 'required|max:150',
            'plad_district' => 'required|max:150',
            'plad_number' => 'required|max:50',
            'plad_complement' => 'required|max:300'
        ];
    }

    public static function messages():array {
        return [
            'plad_cep' => [
                'required' => self::REQUIRED_MSG,
                'size' => 'CEP incompleto'
            ],

            'plad_uf' => [
                'required' => self::REQUIRED_MSG,
                'size' => 'UF Inválido'
            ],

            'plad_city' => [
                'required' => self::REQUIRED_MSG,
                'max' => 'A cidade deve conter no máximo 150 caracteres'
            ],
            
            'plad_district' => [
                'required' => self::REQUIRED_MSG,
                'max' => 'O bairro deve conter no máximo 150 caracteres'
            ],
            
            'plad_number' => [
                'required' => self::REQUIRED_MSG,
                'max' => 'O numero deve conter no máximo 150 caracteres'
            ],

            'plad_complement' => [
                'required' => self::REQUIRED_MSG,
                'max' => 'O complemento deve conter no máximo 300 caracteres'
            ],
        ];
    }
}
