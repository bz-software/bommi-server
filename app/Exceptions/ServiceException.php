<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class ServiceException extends Exception
{
    protected array $errors;

    public function __construct(array $errors = [], int $code = 400, string $message = '')
    {
        parent::__construct($message, $code);
        $this->errors = $errors ?: ['message' => $message];
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'errors' => $this->errors
        ], $this->getCode());
    }
}
