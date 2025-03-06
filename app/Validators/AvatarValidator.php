<?php
namespace App\Validators;

use Illuminate\Support\Facades\Validator;

class AvatarValidator
{
    /**
     * Valida o campo de avatar.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public static function validate($data)
    {
        $rules = [
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|dimensions:min_width=100,min_height=100|max:2048',
        ];

        $messages = [
            'avatar.image' => 'O arquivo deve ser uma imagem.',
            'avatar.mimes' => 'A imagem deve ser nos formatos JPEG, PNG ou JPG.',
            'avatar.dimensions' => 'A imagem deve ter pelo menos 100px de largura e 100px de altura.',
            'avatar.max' => 'A imagem não pode exceder 2MB.',
        ];

        return Validator::make($data, $rules, $messages);
    }
}
