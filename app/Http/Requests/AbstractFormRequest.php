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
        // Pega os erros da validação
        $errors = (new ValidationException($validator))->errors();

        // Reformata os erros para estrutura aninhada
        $formattedErrors = [];

        foreach ($errors as $field => $messages) {
            data_set($formattedErrors, $field, $messages[0]); // pega só a primeira mensagem
        }

        throw new HttpResponseException(
            response()->json([
                'errors' => $formattedErrors
            ], 400)
        );
    }
}
