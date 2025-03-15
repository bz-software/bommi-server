<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\AbstractFormRequest;

class ServicePlaceRequest extends AbstractFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|max:200',
            'description' => 'max:400',
            'idCategory' => 'required',
            'price' => [
                'required',
                'regex:/^\d+(\.\d{1,2})?$/'
            ],
            'duration' => [
                'required',
                'regex:/^\d{2,}:[0-5]\d$/'
            ]
        ];
    }

    public function messages():array {
        return [
            'sepl_id' => [
                'required' => self::REQUIRED_MSG,
            ],

            'name' => [
                'required' => self::REQUIRED_MSG,
                'max' => 'O nome deve conter no máximo 200 caracteres'
            ],

            'description' => [
                'max' => 'A descrição deve conter no máximo 400 caracteres'
            ],

            'idCategory' => [
                'required' => self::REQUIRED_MSG,
                'max' => 'O nome deve conter no máximo 200 caracteres'
            ],

            'price' => [
                'required' => self::REQUIRED_MSG,
                'regex' => 'Informe um valor válido'
            ],

            'duration' => [
                'required' => self::REQUIRED_MSG,
                'regex' => 'Informe uma duração válida'
            ]
        ];
    }
}
