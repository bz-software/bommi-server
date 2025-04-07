<?php
namespace App\Repositories;

use App\DTOs\CategoryServiceDTO;
use App\Models\CategoryPlace as Model;
use App\Exceptions\RepositoryException;

class CategoryPlaceRepository
{
    public function __construct(private Model $model) {}

    public function list(){
        return $this->model::all();
    }
}

