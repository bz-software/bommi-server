<?php
namespace App\Repositories;

use App\DTOs\CreateEmployeeAuthDTO;
use App\DTOs\CreateEmployeeDTO;
use App\Enums\EmployeeType;
use App\Models\Employee as EmployeeModel;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class EmployeeRepository
{
    public function __construct(private EmployeeModel $model) {}

    public function storeAuth(CreateEmployeeAuthDTO $dto)
    {
        $data = $dto->toArray();

        $employee = new $this->model($data);
        $employee->emp_birthdate = Carbon::parse($dto->emp_birthdate)->translatedFormat('Y-m-d');
        $employee->emp_password = Hash::make($dto->emp_password);
        $employee->emp_type = EmployeeType::Owner->value;

        if(!$employee->save()){
            abort(500);
        }
        
        return $this->model->find($employee->emp_id);
    }

    public function store(CreateEmployeeDTO $dto){
        $model = new $this->model;
        $model->fill($dto->toArray());

        $model->emp_birthdate = Carbon::parse($model->emp_birthdate)->translatedFormat('Y-m-d');
        $model->emp_password = Hash::make($model->emp_password);
        $model->emp_type = $dto->emp_type;
        
        if(!$model->save()){
            abort(500);
        }
        
        return $this->model->find($model->emp_id);
    }

    public function update(CreateEmployeeDTO $dto)
    {   
        $model = $this->model->find($dto->emp_id);
        $model->emp_first_name = $dto->emp_first_name;
        $model->emp_last_name = $dto->emp_last_name;
        $model->emp_cpf = $dto->emp_cpf;
        $model->emp_birthdate = Carbon::parse($dto->emp_birthdate)->translatedFormat('Y-m-d');

        $nameFile = $this->storeAvatar($dto->avatar, $dto->emp_id);
        $model->emp_avatar = !empty($nameFile) ? $nameFile : $model->emp_avatar;

        if(!$model->save()){
            abort(500);
        }

        return $model;
    }

    public function storeAvatar(?UploadedFile $avatar, $idEmployee){
        if(empty($avatar)){
            return null;
        }

        $avatarName = $idEmployee . '_' . rand(0, 9999) . '.' . $avatar->getClientOriginalExtension();
        $path = $avatar->storeAs('public/employee_avatar', $avatarName);

        if(Storage::exists($path)){
            return $avatarName;
        }

        abort(500);
    }

    public function getByEmail($email){
        return $this->model::where('emp_email', $email)->where('emp_deleted', false)->first();
    }

    public function getById($id){
        return $this->model::select('*')
            ->where('emp_deleted', '=', false)
            ->where('emp_id', '=', $id)
            ->first();
    }

    public function getByCpf($cpf, $idExcept = null){
        return $this->model::select('*')
            ->where('emp_cpf', '=', $cpf)
            ->where('emp_deleted', '=', false)
            ->when($idExcept !== null, function ($query) use ($idExcept) {
                $query->where('emp_id', '!=', $idExcept);
            })
        ->first();
    }
}

