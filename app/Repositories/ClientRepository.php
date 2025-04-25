<?php
namespace App\Repositories;

use App\DTOs\AppointmentDTO;
use App\Exceptions\RepositoryException;
use App\Models\Client;

class ClientRepository
{
    public function __construct(private Client $model) {}

    public function storeFromAppointment(AppointmentDTO $dto)
    {
        $model = new $this->model();
        $model->cli_name = ucwords($dto->cli_name);
        $model->cli_phone_number = $dto->cli_phone_number;

        if(!$model->save()){
            throw new RepositoryException("Falha interna ao salvar cliente", 500);
        }
        
        return $this->model->find($model['cli_id']);
    }

    public function getByPhoneNumber($phoneNumber){
        return $this->model::where('cli_phone_number', $phoneNumber)->first();
    }
}

