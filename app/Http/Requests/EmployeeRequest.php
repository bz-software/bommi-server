<?php

namespace App\Http\Requests;

use App\Enums\EmployeeType;
use App\Http\Requests\AbstractFormRequest;
use Illuminate\Validation\Rule;

class EmployeeRequest extends AbstractFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('put');
        $id = auth('employee')->user()->emp_id;

        return [
            'firstName' => ['required', 'max:100'],
            'lastName' => ['nullable', 'max:100'],
            'cpf' => [
                'required',
                'cpf',
                'size:11',
                Rule::unique('employees', 'emp_cpf')
                    ->ignore($isUpdate ? $id : null, 'emp_id')
                    ->where('emp_deleted', 0),
            ],
            'birthdate' => [
                'required',
                'date',
                'date_format:Y-m-d',
                'before_or_equal:' . now()->subYears(18)->format('Y-m-d')
            ],
            'email' => [
                $isUpdate ? 'nullable' : 'required',
                'email',
                Rule::unique('employees', 'emp_email')
                    ->ignore($isUpdate ? $id : null, 'emp_id')
                    ->where('emp_deleted', 0),
            ],
            'password' => [$isUpdate ? 'nullable' : 'required', 'min:6'],
            'type' => [
                $isUpdate ? 'nullable' : 'required',
                Rule::in(EmployeeType::values())
            ],
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|dimensions:min_width=100,min_height=100|max:2048'
        ];
    }


    public function messages():array {
        return [
            'firstName' => [
                'required' => self::REQUIRED_MSG,
                'max' => 'O nome deve conter no máximo 100 caracteres'
            ],
            'lastName' => [
                'max' => 'O sobrenome deve conter no máximo 100 caracteres'
            ],

            'cpf' => [
                'required' => "Campo obrigatório",
                'size' => 'CPF Incompleto',
                'unique' => 'O CPF fornecido já está em uso'
            ],

            'birthdate' => [
                'required' => self::REQUIRED_MSG,
                'date' => 'Data inválida',
                'before_or_equal' => 'Você precisa ser maior de 18 anos',
                'date_format' => "Formato de data inválida"
            ],

            'email' => [
                'required' => self::REQUIRED_MSG,
                'unique' => 'O e-mail fornecido já está em uso'
            ],

            'password' => [
                'required' => self::REQUIRED_MSG,
                'min' => "A senha deve conter no mínimo 6 caracteres"
            ],

            'avatar' => [
                'image' => 'O arquivo deve ser uma imagem.',
                'mimes' => 'A imagem deve ser nos formatos JPEG, PNG ou JPG.',
                'dimensions' => 'A imagem deve ter pelo menos 100px de largura e 100px de altura.',
                'max' => 'A imagem não pode exceder 2MB.'
            ],

            'type' => [
                'in' => 'O tipo informado não é válido.',
                'required' => self::REQUIRED_MSG,
            ]
        ];
    }
}
