<?php

namespace App\DTOs;

use Illuminate\Http\Request;

class AuthEmployeeDTO extends AbstractDTO
{
    public $emp_email;
    public $emp_password;

    /**
     * AuthEmployeeDTO constructor.
     * 
     * @param string $email
     * @param string $password
     */
    public function __construct($email, $password)
    {
        $this->emp_email = $email;
        $this->emp_password = $password;
    }

    /**
     * Método para criar um DTO a partir de um array.
     * 
     * @param array $data
     * @return AuthEmployeeDTO
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['emp_email'] ?? null,
            $data['emp_password'] ?? null
        );
    }

    /**
     * Método para criar o DTO a partir de uma requisição.
     * 
     * @param Request $request
     * @return AuthEmployeeDTO
     */
    public static function fromRequest(Request $request): self
    {
        return new self(
            $request->input('email') ?? null,
            $request->input('password') ?? null
        );
    }

    public function toModel(): array
    {
        return [
            'emp_email' => $this->emp_email,
            'emp_password' => $this->emp_password
        ];
    }
}
