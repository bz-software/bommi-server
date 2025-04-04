<?php

namespace App\DTOs;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class CreateEmployeeDTO extends AbstractDTO
{
    public $emp_id;
    public $emp_first_name;
    public $emp_last_name;
    public $emp_email;
    public $emp_password;
    public $emp_phone_number;
    public $emp_birthdate;
    public $emp_cpf;
    public $emp_type;
    public ?UploadedFile $avatar = null;

    /**
     * CreateEmployeeDTO constructor.
     * 
     * @param int|null $email
     * @param int|string $firstName
     * @param int|string $lastName
     * @param int|string $email
     * @param int|string $password
     * @param int|string $phoneNumber
     * @param int|string $birthdate
     * @param int|string $cpf
     */
    public function __construct(
        $id,
        $firstName,
        $lastName,
        $email, 
        $password,
        $phoneNumber,
        $birthdate,
        $cpf,
        $type,
        $avatar
    )
    {
        $this->emp_id = $id;
        $this->emp_first_name = $firstName;
        $this->emp_last_name = $lastName;
        $this->emp_email = $email;
        $this->emp_password = $password;
        $this->emp_phone_number = $phoneNumber;
        $this->emp_birthdate = $birthdate;
        $this->emp_cpf = $cpf;
        $this->emp_type = $type;
        $this->avatar = $avatar;
    }

    /**
     * Método para criar o DTO a partir de uma requisição.
     * 
     * @param Request $request
     * @return CreateEmployeeDTO
     */
    public static function fromRequest(Request $request): self
    {
        return new self(
            $request->input('id') ?? null,
            $request->input('firstName') ?? null,
            $request->input('lastName') ?? null,
            $request->input('email') ?? null,
            $request->input('password') ?? null,
            $request->input('phoneNumber') ?? null,
            $request->input('birthdate') ?? null,
            $request->input('cpf') ?? null,
            $request->input('type') ?? null,
            $request->file('avatar') ?? null
        );
    }
}
