<?php
namespace App\Repositories;

use App\DTOs\CreatePlaceAddressDTO;
use App\DTOs\PlaceDTO;
use App\Enums\EmployeeType;
use App\Models\Place as Model;
use App\Exceptions\RepositoryException;
use App\Models\EmployeePlace;
use App\Models\PlaceAddress;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PlaceRepository
{
    public function __construct(private Model $model, private PlaceAddress $paModel ) {}

    public function create(PlaceDTO $place, CreatePlaceAddressDTO $address){
        $place->pla_slug_name = Str::slug($place->pla_name);

        try {
            DB::beginTransaction();
                $placeSave = new $this->model($place->toArray());
                $placeSave->pla_employee_id = auth('employee')->user()->emp_id;

                if($placeSave->save()){
                    $placeAddressSave = new $this->paModel($address->toArray());
                    $placeAddressSave->plad_place_id = $placeSave->pla_id;
                    $placeAddressSave->save();

                    /** @var EmployeePlaceRepository $epRepository */
                    $epRepository = app(EmployeePlaceRepository::class);
                    $epRepository->store(auth('employee')->user()->emp_id, $placeSave->pla_id, EmployeeType::Owner->value);

                    /** @var EmployeeRepository $eRepository */
                    $eRepository = app(EmployeeRepository::class);
                    $eRepository->setPlaceLogged($placeSave->pla_id);
                }else{
                    throw new RepositoryException("Erro interno", 500);
                }

            DB::commit();

            return $placeSave;
        } catch (\PDOException $e) {
            DB::rollBack();
            throw new RepositoryException("Falha ao cadastrar estabelecimento: ".$e->getMessage(), 500);
        }
    }
}

