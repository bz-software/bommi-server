<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'nome' => 'required|max:255',
            'cpf' => 'required|cpf',
            'dataNascimento' => 'required|date|before_or_equal:'.now()->subYears(18)->format('Y-m-d'),
            'email' => 'required|email|unique:users',
            'senha' => 'required|min:6',
        ];
    }
}
