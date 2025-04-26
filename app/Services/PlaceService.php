<?php
namespace App\Services;

use App\DTOs\CreatePlaceAddressDTO;
use App\DTOs\PlaceDTO;
use App\Exceptions\ServiceException;
use App\Models\CategoryPlace;
use App\Repositories\CategoryPlaceRepository;
use App\Repositories\PlaceRepository;
use App\Models\Place as Model;
use App\Services\AppointmentService as AppoService;
use App\Services\AuthEmployeeService;

class PlaceService {
    public function __construct(
        private PlaceRepository $repository, 
        private CategoryPlaceRepository $cpRepository,
        private AppoService $appoService,
        private AuthEmployeeService $authEmployeeService
    ) {}

    public function create(PlaceDTO $place, CreatePlaceAddressDTO $address){
        $place = $this->repository->create($place, $address);

        /**
         * O EmployeePlace é criado apenas após a criação do estabelecimento.
         * Por padrão, a estrutura de dados do usuário autenticado é: Employee => EmployeePlace.
         * Isso permite identificar o tipo de usuário.
         * 
         * Portanto, após a criação, o método `setUser` é chamado para criar essa estrutura para o usuário logado.
         */
        $this->authEmployeeService->setUser(auth('employee')->user()->emp_id, $place->pla_id);

        return auth('employee')->user();
    }

    public function getCategories(){
        return $this->cpRepository->list();
    }

    public function existBySlug($slug){
        if(!empty($this->repository->getBySlug($slug))){
            return response()->noContent(200);
        }else{
            return response()->noContent(404);
        }
    }

    public function bySlug($slug){
        $model = $this->repository->getBySlug($slug, Model::$WITH_ALL);
        if(!empty($model)){
            $model['avaliableDates'] = $this->appoService->getAvaliableDates();
        }

        return $model;
    }
}

