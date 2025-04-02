<?php
namespace App\Services;

use App\DTOs\CreateEmployeeDTO;
use App\Enums\EmployeeType;
use App\Exceptions\ServiceException;
use App\Helpers\ControllerHelper as cHelper;
use App\Repositories\EmployeePlaceRepository;
use App\Repositories\EmployeeRepository as Repository;

class EmployeeService
{
    public function __construct(
        private Repository $repository, 
        private EmployeePlaceRepository $epRepository
    ){}

    public function types()
    {
        cHelper::employeeNeedsSelectPlace();

        $types = EmployeeType::array();
        $array = array();

        // Removendo OWNER
        foreach($types as $i => $type){
            if($type['value'] != EmployeeType::Owner->value){
                array_push($array, $type);
            }
        }

        return response()->json($array, 200);
    }

    public function byCpf($cpf)
    {
        cHelper::employeeNeedsSelectPlace();

        $idOwner = auth('employee')->user()->emp_id;
        $owner = $this->repository->getById($idOwner);

        if($cpf == $owner->emp_cpf){
            throw new ServiceException(['cpf' => "Para se denominar um funcionário, ative a opção nas configurações pessoais."], 400);
        }

        if(!empty($cpf)){
            $employee = $this->repository->getByCpf($cpf, $idOwner);
            
            if(!empty($employee)){
                return $employee;
            }else{
                throw new ServiceException([], 404, '');
            }
        }
    }

    public function create(CreateEmployeeDTO $dto){
        cHelper::employeeNeedsSelectPlace();

        $employee = $dto;
 
        if($employee->emp_type == EmployeeType::Owner->value){
            throw new ServiceException(['type' => "O tipo informado não é válido"], 400);
        }

        $idPlace = auth('employee')->user()->emp_place_id_logged;        

        $employee->emp_cpf = preg_replace('/\D/', '', $employee->emp_cpf);

        $employeeFounded = $this->repository->getByCpf($employee->emp_cpf);

        if(!empty($exist)){
            if($this->epRepository->exist($employeeFounded->emp_id, $idPlace)){
                throw new ServiceException(['cpf' => "Funcionário já cadastrado nesse estabelecimento"], 400);
            }else{
                $this->epRepository->store($employeeFounded->emp_id, $idPlace, $employee->emp_type);
                return $this->repository->getById($employeeFounded->emp_id);
            }
        }else{
            $employee = $this->repository->store($dto);

            $this->epRepository->store($employee->emp_id, $idPlace, $employee->emp_type);

            return $employee;
        }
    }
}