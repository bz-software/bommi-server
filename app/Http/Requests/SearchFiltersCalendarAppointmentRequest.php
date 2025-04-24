<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SearchFiltersCalendarAppointmentRequest extends AbstractFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Defina como true para permitir a requisição sem verificação de autorização
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'start' => 'required|date_format:Y-m-d H:i:s', // Data e hora no formato 'Y-m-d H:i:s'
            'end' => 'required|date_format:Y-m-d H:i:s|after:start', // A data de "end" deve ser após "start"
            'idEmployeePlace' => [
                'nullable',
                Rule::exists('employees_place', 'ep_id')->where(function ($query) {
                    $query->where('ep_deleted_at', null);
                }),
            ]
        ];
    }

    /**
     * Customize the error messages for validation.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'start' => [
                'required' => 'A data e hora de início são obrigatórias.',
                'date_format' => 'A data e hora de início devem estar no formato YYYY-MM-DD HH:MM:SS.'
            ],
            'end' => [
                'required' => 'A data e hora de término são obrigatórias.',
                'date_format' => 'A data e hora de término devem estar no formato YYYY-MM-DD HH:MM:SS.',
                'after' => 'A data e hora de término devem ser após a data e hora de início.',
            ],
            'idEmployeePlace' => [
                'exists' => 'O ID do profissional fornecido não é válido.'
            ]
        ];
    }
}
