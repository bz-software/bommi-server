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
use Carbon\Carbon;

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

    public function list(){
        cHelper::employeeNeedsSelectPlace();
        
        $idEmployee = auth('employee')->user()->emp_id;
        $idPlace = auth('employee')->user()->emp_place_id_logged;

        /** @var EmployeePlaceRepository $ep */
        $ep = app(EmployeePlaceRepository::class);

        $employeePlace = $ep->get($idEmployee, $idPlace);
        if(empty($employeePlace)){
            throw new ServiceException([], 400, "Funcionário não existe no estabelecimento informado.");
        }

        return $this->repository->getAll($employeePlace->ep_id, EmployeeServiceModel::$WITH_CATEGORY_AND_SERVICE);
    }

    public function destroy($id){
        cHelper::employeeNeedsSelectPlace();

        if(intval($id) <= 0){
            throw new ServiceException([], 400, "ID é obrigatório");
        }

        $idEmployee = auth('employee')->user()->emp_id;
        $idPlace = auth('employee')->user()->emp_place_id_logged;

        /** @var EmployeePlaceRepository $ep */
        $ep = app(EmployeePlaceRepository::class);

        $employeePlace = $ep->get($idEmployee, $idPlace);
        if(empty($employeePlace)){
            throw new ServiceException([], 400, "Funcionário não existe no estabelecimento informado.");
        }

        $data = $this->repository->getById($id);

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

        /**
         * Condição 1: Verifica se o funcionário que atende ao serviço é o mesmo do estabelecimento que o funcionário está logado
         * Condição 2: Verifica se o estabelecimento da categoria é o mesmo que o do funcionário logado
         */
        if($data->emse_ep_id == $employeePlace->ep_id && $category->case_place_id == $employeePlace->ep_place_id){
            $this->repository->destroy($id);

            return response()->noContent(200);
        }else{
            throw new ServiceException([], 400, "Falha ao excluir, antendimento não encontrado");
        }
    }
}