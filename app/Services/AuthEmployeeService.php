<?php
namespace App\Services;

use App\DTOs\AuthEmployeeDTO;
use App\DTOs\CreateEmployeeAuthDTO;
use App\Exceptions\ServiceException;
use App\Repositories\EmployeePlaceRepository;
use App\Repositories\EmployeeRepository;

class AuthEmployeeService {
    public function __construct(
        private EmployeeRepository $repository,
        private EmployeePlaceRepository $epRepository
    ) {}

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
            $idEmployeePlace = auth('employee')->user()->emp_place_id_logged;

            $this->setUser($id, $idEmployeePlace);

            return (object) [
                'token' => $login,
                'user' => auth('employee')->user()
            ];
        }
    }

    public function createAuth(CreateEmployeeAuthDTO $dto){

        $employee = $this->repository->storeAuth($dto);
        $employee->employeePlace = $this->epRepository->get($employee->emp_id, $employee->emp_place_id_logged);

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

    public function setUser($idEmployee, $idEmployeePlace){
        $employee =  $this->repository->getById($idEmployee);
        $employee->employeePlace = $this->epRepository->get($idEmployee, $idEmployeePlace);

        auth('employee')->setUser($employee);
    }

    public function logout(){
        auth('employee')->logout();
        return response()->noContent(200);
    }
}

