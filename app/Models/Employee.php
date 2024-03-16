<?php

namespace App\Models;

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
            'emp_cpf' => 'required|cpf|size:11',
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
                'size' => 'CPF Incompleto'
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
