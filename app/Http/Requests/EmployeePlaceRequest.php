<?php

namespace App\Http\Requests;

use App\Http\Requests\AbstractFormRequest;

class EmployeePlaceRequest extends AbstractFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'clockIn' => [
                'nullable',
                'regex:/^(?:[01]\d|2[0-3]):[0-5]\d$/',
                function ($attribute, $value, $fail) {
                    if (request('clockOut') && $value > request('clockOut')) {
                        $fail('O horário de entrada não pode ser maior que o de saída.');
                    }
                },
            ],
            'clockOut' => [
                'nullable',
                'regex:/^(?:[01]\d|2[0-3]):[0-5]\d$/',
                function ($attribute, $value, $fail) {
                    if (request('clockIn') && $value < request('clockIn')) {
                        $fail('O horário de saída não pode ser menor que o de entrada.');
                    }
                },
            ],
        ];
    }

    public function messages():array {
        return [
            'clockIn.required' => self::REQUIRED_MSG,
            'clockIn.regex' => 'O horário de entrada deve estar no formato HH:MM.',

            'clockOut.required' => self::REQUIRED_MSG,
            'clockOut.regex' => 'O horário de saída deve estar no formato HH:MM.',
        ];
    }
}
