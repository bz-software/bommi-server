<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\AbstractFormRequest;

class StoreCategoryServiceRequest extends AbstractFormRequest
{
    const REQUIRED_MSG = 'Campo obrigatório';
    
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'case_name' => 'required|max:300'
        ];
    }

    public function messages():array {
        return [
            'case_name' => [
                'required' => self::REQUIRED_MSG,
                'max' => 'O nome deve conter no máximo 300 caracteres'
            ]
        ];
    }
}
