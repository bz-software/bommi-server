<?php
namespace App\Services;

use App\DTOs\EmployeeServiceDTO;
use App\Exceptions\ServiceException;
use App\Helpers\ControllerHelper as cHelper;
use App\Models\EmployeeService as EmployeeServiceModel;
use App\Repositories\CategoryServiceRepository;
use App\Repositories\EmployeePlaceRepository;
use App\Repositories\EmployeeServiceRepository as Repository;
use App\Repositories\ServicePlaceRepository;

class EmployeeServiceService
{
    public function __construct(
        private Repository $repository, 
    ){}

    public function create(EmployeeServiceDTO $dto){
        cHelper::employeeNeedsSelectPlace();
        $data = $dto;

        $idEmployee = auth('employee')->user()->emp_id;
        $idPlace = auth('employee')->user()->emp_place_id_logged;

        /** @var EmployeePlaceRepository $ep */
        $ep = app(EmployeePlaceRepository::class);

        $employeePlace = $ep->get($idEmployee, $idPlace);
        if(empty($employeePlace)){
            throw new ServiceException([], 400, "Funcionário não existe no estabelecimento informado.");
        }

        /** @var ServicePlaceRepository $sp */
        $sp = app(ServicePlaceRepository::class);

        $service = $sp->getById($data->emse_sepl_id);
        if(empty($service)){
            throw new ServiceException([], 400, "Serviço não existe no estabelecimento informado.");
        }

        /** @var CategoryServiceRepository $cs */
        $cs = app(CategoryServiceRepository::class);

        $category = $cs->getByIdAndPlace($service->sepl_case_id, $idPlace);
        if(empty($category)){
            throw new ServiceException(['idService' => "Serviço não encontrado"], 400);
        }

        $data->emse_ep_id = $employeePlace->ep_id;

        if(!empty($this->repository->getByEmployeePlaceAndIdService($employeePlace->ep_id, $service->sepl_id))){
            throw new ServiceException(['idService' => "Serviço já cadastrado"], 400);
        }

        $data->ep_id = $employeePlace->ep_id;

        $employeeService = $this->repository->store($data);

        return $this->repository->getById($employeeService->emse_id, EmployeeServiceModel::$WITH_CATEGORY_AND_SERVICE);
    }
}