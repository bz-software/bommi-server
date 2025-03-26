<?php
namespace App\Services;

use App\DTOs\AuthEmployeeDTO;
use App\DTOs\CreateEmployeeAuthDTO;
use App\Exceptions\ServiceException;
use App\Repositories\EmployeeRepository;

class AuthEmployeeService {
    public function __construct(private EmployeeRepository $repository) {}

    public function login(AuthEmployeeDTO $dto){
        $employeeFoundedByEmail = $this->repository->getByEmail($dto->emp_email);

        if(empty($employeeFoundedByEmail)){
            throw new ServiceException(['emp_email' => 'Usuário não encontrado'], 400);
        }

        $login = auth('employee')->attempt(
            [
                'emp_email' => $dto->emp_email,
                'password' => $dto->emp_password
            ], true
        );
        
        if(!$login){
            throw new ServiceException(['emp_password' => 'Senha inválida'], 400);
        }else{
            $id = auth('employee')->user()->emp_id;
            $employee =  $this->repository->getById($id);

            return (object) [
                'token' => $login,
                'user' => $employee
            ];
        }
    }

    public function createAuth(CreateEmployeeAuthDTO $dto){

        $employee = $this->repository->storeAuth($dto);

        $login = auth('employee')->attempt(
            [
                'emp_email' => $dto->emp_email,
                'password' => $dto->emp_password
            ], true
        );

        if(!$login){
            throw new ServiceException(['errors' => "error"], 400);
        }else{
            return (object) [
                'token' => $login,
                'user' => $employee
            ];
        }
    }

    public function logout(){
        auth('employee')->logout();
        return response()->noContent(200);
    }
}

