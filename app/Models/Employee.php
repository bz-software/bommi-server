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

    protected $appends = ['emp_avatar_url'];

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
        "emp_cpf",
        "emp_avatar",
    ];

    protected $hidden = [
        'emp_password',
        'emp_remember_token'
    ]; 


    public static function rules($insert = true, $idExcept = null):array {
        $rules = [
            'emp_first_name' => 'required|max:100',
            'emp_last_name' => 'max:100',
            'emp_cpf' => 'required|cpf|size:11|unique:employees,emp_cpf,NULL,emp_id,emp_deleted,0',
            'emp_birthdate' => 'required|date|before_or_equal:'.now()->subYears(18)->format('Y-m-d'),
            'emp_email' => 'required|email|unique:employees,emp_email,NULL,emp_id,emp_deleted,0',
            'emp_password' => 'required|min:6'            
        ];

        if(!$insert){
            $rules['emp_cpf'] = 'required|cpf|size:11|unique:employees,emp_cpf,' . $idExcept . ',emp_id,emp_deleted,0';
            unset($rules['emp_password']);
            unset($rules['emp_email']);
        }

        return $rules;
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
            ],

            'avatar' => [
                'image' => 'O arquivo deve ser uma imagem.',
                'mimes' => 'A imagem deve ser nos formatos JPEG, PNG ou JPG.',
                'dimensions' => 'A imagem deve ter pelo menos 100px de largura e 100px de altura.',
                'max' => 'A imagem não pode exceder 2MB.',
            ]
        ];
    }

    public function getEmpAvatarUrlAttribute()
    {
        return asset("storage/employee_avatar/{$this->emp_avatar}");
    }

    public function setCpfAttribute($value) {
        $this->attributes['emp_cpf'] = preg_replace('/\D/', '', $value);
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

    public static function getAllActiveByPlace($idPlace, $employeeExcept = null){
        // SELECT * FROM employees e
        // INNER JOIN employees_place ep ON ep.ep_employee_id = e.emp_id
        // WHERE ep.ep_place_id = 7
        // AND e.emp_deleted = false

        if(intval($employeeExcept) > 0){
            $employees = Employee::select('*')
                ->join('employees_place', 'employees_place.ep_employee_id', '=', 'employees.emp_id')
                ->where('employees_place.ep_place_id', '=', $idPlace)
                ->where('employees.emp_deleted', '=', 0)
                ->where('employees.emp_id', '!=', $employeeExcept)
                ->where('employees_place.ep_deleted', '=', false)
                ->get();
        }else{
            $employees = Employee::select('*')
                ->join('employees_place', 'employees_place.ep_employee_id', '=', 'employees.emp_id')
                ->where('employees_place.ep_place_id', '=', $idPlace)
                ->where('employees.emp_deleted', '=', 0)
                ->where('employees_place.ep_deleted', '=', false)
                ->get();
        }

        foreach($employees as $i => $employee){
            $employees[$i] = self::setType($employee, $idPlace);
        }

        return $employees;
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
