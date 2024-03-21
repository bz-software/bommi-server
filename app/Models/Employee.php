<?php

namespace App\Models;

use App\Enums\EmployeeType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Employee extends Model implements AuthenticatableContract, JWTSubject
{
    
    use HasApiTokens, HasFactory, Notifiable, Authenticatable; 

    const CREATED_AT = 'emp_created_at';
    const UPDATED_AT = 'emp_updated_at';

    protected $primaryKey = "emp_id";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "emp_first_name",
        "emp_last_name",
        "emp_email",
        "emp_password",
        "emp_phone_number",
        "emp_birthdate",
        "emp_cpf"
    ];

    protected $hidden = [
        'emp_password',
        'emp_remember_token'
    ]; 


    public static function rules():array {
        return [
            'emp_first_name' => 'required|max:100',
            'emp_last_name' => 'max:100',
            'emp_cpf' => 'required|cpf|size:11|unique:employees,emp_cpf,NULL,emp_id,emp_deleted,0',
            'emp_birthdate' => 'required|date|before_or_equal:'.now()->subYears(18)->format('Y-m-d'),
            'emp_email' => 'required|email|unique:employees,emp_email,NULL,emp_id,emp_deleted,0',
            'emp_password' => 'required|min:6',
        ];
    }

    public static function messages():array {
        return [
            'emp_first_name' => [
                'required' => 'Campo obrigatório',
                'max' => 'O nome deve conter no máximo 100 caracteres'
            ],
            'emp_last_name' => [
                'max' => 'O sobrenome deve conter no máximo 100 caracteres'
            ],

            'emp_cpf' => [
                'required' => "Campo obrigatório",
                'size' => 'CPF Incompleto',
                'unique' => 'O CPF fornecido já está em uso'
            ],

            'emp_birthdate' => [
                'required' => 'Campo obrigatório',
                'date' => 'Data inválida',
                'before_or_equal' => 'Você precisa ser maior de 18 anos'
            ],

            'emp_email' => [
                'required' => 'Campo obrigatório',
                'unique' => 'O e-mail fornecido já está em uso'
            ],

            'emp_password' => [
                'required' => "Campo obrigatório",
                'min' => "A senha deve conter no mínimo 6 caracteres"
            ]
        ];
    }

    public function getAuthPassword(){
        return $this->emp_password;
    }

    public function getRememberTokenName(){
        return $this->emp_remember_token;
    }

    public function setRememberToken($value){
        $this->emp_remember_token = $value;
    }

    public static function setPlaceLogged($idPlace){
        $employeeModel = Employee::find(auth('employee')->user()->emp_id);
        $employeeModel->emp_place_id_logged = $idPlace;
        return $employeeModel->save();
    }

    public static function getByCpf($cpf, $except = null){
        // TODO: verificar como adicionar condicionais na query
        if(!empty($except)){
            $employee = Employee::select('*')
                ->where('emp_cpf', '=', $cpf)
                ->where('emp_id', '!=', $except)
                ->where('emp_deleted', '=', false)->first();
        }else{
            $employee = Employee::select('*')
            ->where('emp_cpf', '=', $cpf)
            ->where('emp_deleted', '=', false)->first();
        }
        
        return $employee;
    }

    public static function getById($id, $idPlace = null){
        $employee = Employee::select('*')
            ->where('emp_deleted', '=', false)
            ->where('emp_id', '=', $id)
            ->first();

        return self::setType($employee, $idPlace);
    }

    public static function setType($employee, $idPlace = null){
        $employee['emp_type'] = null;
        $employee['emp_type_name'] = null;

        $idPlace = empty($idPlace) ? $employee['emp_place_id_logged'] : $idPlace;

        if(!empty($idPlace)){
            $pe = EmployeePlace::select('*')
                ->where('ep_employee_id', '=', $employee['emp_id'])
                ->where('ep_place_id', '=', $idPlace)
                ->where('ep_deleted', '=', false)
                ->first();
            
            if(!empty($pe)){
                $idType = $pe['ep_employee_type'];

                $employee['emp_type'] = $pe['ep_employee_type'];
                $employee['emp_type_name'] = EmployeeType::name($idType);
            }
        }
        
        return $employee;
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
