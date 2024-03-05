<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Place extends Model {
    const CREATED_AT = 'pla_created_at';
    const UPDATED_AT = 'pla_changed_at';

    protected $primaryKey = "pla_id";

    const REQUIRED_MSG = 'Campo obrigatório';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "pla_name",
        "pla_phone_number",
        "pla_category_id",
        "pla_cnpj",
        "pla_has_cnpj",
        "pla_avatar_url"
    ];

    public static function rules():array {
        return [
            'pla_name' => 'required|max:200',
            'pla_phone_number' => 'required|max:11|phonenumber',
            'pla_category_id' => 'required|exists:categories_places,capl_id',
            'pla_cnpj' => 'cnpj',
            'pla_has_cnpj' => 'required|boolean',
        ];
    }

    public static function messages():array {
        return [
            'pla_name' => [
                'required' => self::REQUIRED_MSG,
                'max' => 'O nome deve conter no máximo 200 caracteres'
            ],

            'pla_phone_number' => [
                'max' => 'Telefone incorreto',
                'required' => self::REQUIRED_MSG
            ],

            'pla_category_id' => [
                'required' => self::REQUIRED_MSG,
                'exists' => "Categoria inválida"
            ],

            'pla_has_cnpj' => [
                'required' => self::REQUIRED_MSG
            ],
        ];
    }
}
