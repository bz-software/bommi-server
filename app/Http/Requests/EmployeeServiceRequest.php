<?php

namespace App\Http\Requests;

use App\Http\Requests\AbstractFormRequest;
use Illuminate\Validation\Rule;

class EmployeeServiceRequest extends AbstractFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public static function rules():array {
        return [
            'idService' => [
                'required',
                Rule::exists('services_places', 'sepl_id')->where(function ($query) {
                    $query->where('sepl_deleted', 0);
                }),
            ]
        ];
    }

    public function messages():array {
        return [
            'idService' => [
                'required' => self::REQUIRED_MSG,
                'exists' => "Serviço não encontrado"
            ],

            'idEmployee' => [
                'required' => self::REQUIRED_MSG,
            ],
        ];
    }
}
