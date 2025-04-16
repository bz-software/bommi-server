<?php

namespace App\Http\Requests;

use App\Http\Requests\AbstractFormRequest;
use Illuminate\Validation\Rule;

class AvailabilityRequest extends AbstractFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|min:3|max:100',
            'phoneNumber' => 'required|string|regex:/^\d{10,11}$/',
            'idPlace' => [
                'required',
                'integer',
                Rule::exists('places', 'pla_id')->where(function ($query) {
                    $query->where('pla_deleted', 0);
                }),
            ] ,
            'services' => 'required|array|min:1',
            'services.*.id' => [
                'required',
                'integer',
                Rule::exists('services_places', 'sepl_id')->where(function ($query) {
                    $query->where('sepl_deleted', 0);
                }),
            ],

            'services.*.note' => 'nullable|string|max:250'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome é obrigatório.',
            'phoneNumber.required' => 'O número de telefone é obrigatório.',
            'phoneNumber.regex' => 'O número de telefone deve conter apenas números e ter entre 10 e 11 dígitos.',
            'idPlace.required' => 'O estabelecimento é obrigatório.',
            'idPlace.exists' => 'O estabelecimento informado é inválido.',
        
            'services.required' => 'Selecione ao menos um serviço.',
            'services.array' => 'Os serviços devem ser enviados em formato de lista.',
            'services.min' => 'Selecione ao menos um serviço.',
        
            'services.*.id.required' => 'O campo de serviço é obrigatório.',
            'services.*.id.integer' => 'Todos os serviços devem ser números válidos.',
            'services.*.id.exists' => 'Um ou mais serviços selecionados são inválidos.',
        
            'services.*.note.string' => 'A observação do serviço deve ser um texto.',
            'services.*.note.max' => 'A observação do serviço não pode ter mais que 250 caracteres.',
        ];        
    }
}
