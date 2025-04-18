<?php
namespace App\Repositories;

use App\DTOs\AppointmentServiceDTO;
use App\Exceptions\RepositoryException;
use App\Models\AppointmentService as Model;

class AppointmentServiceRepository
{
    public function __construct(private Model $model) {}

    public function store(AppointmentServiceDTO $dto)
    {
        $data = $dto->toArray();
        
        $model = new $this->model();
        $model->fill($data);

        if(!$model->save()){
            throw new RepositoryException("Falha interna ao salvar itens do agendamento", 500);
        }
        
        return $this->model->find($model['apse_id']);
    }
}

