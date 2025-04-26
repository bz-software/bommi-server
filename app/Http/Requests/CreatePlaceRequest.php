<?php

namespace App\Http\Requests;

use App\Http\Requests\AbstractFormRequest;
use Illuminate\Validation\Rule;

class CreatePlaceRequest extends AbstractFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "name" => "required|max:200",
            "phoneNumber" => "required|max:11|phonenumber",
            "idCategory" => "required|exists:categories_places,capl_id",
            "cnpj" => [
                Rule::requiredIf(fn () => (bool) $this->input('hasCnpj', null)),
                Rule::when($this->filled('cnpj'), [
                    Rule::unique('places', 'pla_cnpj')->where(fn ($query) =>
                        $query->where('pla_deleted', '!=', 1)
                    ),
                    'cnpj',
                ]),
            ],
            "hasCnpj" => "required|boolean",
            "address.cep" => "required|size:8",
            "address.uf" => "required|size:2",
            "address.city" => "required|max:150",
            "address.district" => "required|max:150",
            "address.number" => "required|max:50",
            "address.complement" => "required|max:300"
        ];
    }

    public function messages():array {
        return [
            'name' => [
                'required' => self::REQUIRED_MSG,
                'max' => 'O nome deve conter no máximo 200 caracteres'
            ],

            'phoneNumber' => [
                'max' => 'Telefone incorreto',
                'required' => self::REQUIRED_MSG
            ],

            'idCategory' => [
                'required' => self::REQUIRED_MSG,
                'exists' => "Categoria inválida"
            ],

            'hasCnpj' => [
                'required' => self::REQUIRED_MSG
            ],

            'cnpj' => [
                'required' => self::REQUIRED_MSG,
                'unique' => "CNPJ já está em uso"
            ],

            'address.cep' => [
                'required' => self::REQUIRED_MSG,
                'size' => 'CEP incompleto'
            ],

            'address.uf' => [
                'required' => self::REQUIRED_MSG,
                'size' => 'UF Inválido'
            ],

            'address.city' => [
                'required' => self::REQUIRED_MSG,
                'max' => 'A cidade deve conter no máximo 150 caracteres'
            ],
            
            'address.district' => [
                'required' => self::REQUIRED_MSG,
                'max' => 'O bairro deve conter no máximo 150 caracteres'
            ],
            
            'address.number' => [
                'required' => self::REQUIRED_MSG,
                'max' => 'O numero deve conter no máximo 150 caracteres'
            ],

            'address.complement' => [
                'required' => self::REQUIRED_MSG,
                'max' => 'O complemento deve conter no máximo 300 caracteres'
            ],
        ];
    }
}
