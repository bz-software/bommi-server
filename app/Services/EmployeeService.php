<?php
namespace App\Services;
use App\Enums\EmployeeType;
use App\Exceptions\ServiceException;
use App\Helpers\ControllerHelper as cHelper;
use App\Repositories\EmployeeRepository as Repository;

class EmployeeService
{
    public function __construct(private Repository $repository){}

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
}