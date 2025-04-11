<?php

namespace App\Http\Requests;
use App\Http\Requests\AbstractFormRequest;

class EmployeeByCpfRequest extends AbstractFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cpf' => [
                'required',
                'cpf',
                'size:11'
            ],
        ];
    }

    public function messages():array {
        return [
            'cpf' => [
                'required' => "Campo obrigatório",
                'size' => 'CPF Incompleto',
                'cpf' => 'CPF inválido'
            ],
        ];
    }
}
