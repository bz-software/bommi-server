<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

class AbstractFormRequest extends FormRequest
{
    const REQUIRED_MSG = 'Campo obrigatório';
    
    public function failedValidation(Validator $validator)
    {
        // Manipula os erros para transformar no formato desejado
        $errors = (new ValidationException($validator))->errors();

        // Reformata os erros para que os valores sejam strings e não arrays
        $formattedErrors = [];
        foreach ($errors as $field => $messages) {
            $formattedErrors[$field] = $messages[0]; // Pegando a primeira mensagem de erro
        }

        // Lança a exceção com o formato de erro customizado
        throw new HttpResponseException(
            response()->json([
                'errors' => $formattedErrors
            ], 400)
        );
    }
}
