<?php

namespace App\Http\Requests;

use App\Http\Requests\AbstractFormRequest;

class AuthEmployeeRequest extends AbstractFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required'
        ];
    }

    public function messages():array {
        return [
            'email' => [
                'required' => self::REQUIRED_MSG,
            ],

            'password' => [
                'required' => self::REQUIRED_MSG,
            ],
        ];
    }
}
