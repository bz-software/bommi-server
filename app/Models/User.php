<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Sofa\Eloquence\Mappable; // extension trait
use App\Http\Requests\UserRequest;
class User extends Model
{
    use HasApiTokens, HasFactory, Notifiable, Mappable;

    const CREATED_AT = 'dataCriacao';
    const UPDATED_AT = 'dataAltera';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nome',
        'email',
        'senha',
        'dataNascimento',
        'cpf',
        'cargo',
        'email',
        'idPermissoes',
        'avatarUrl',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public static function rules():array {
        return [
            'nome' => 'required|max:255|full_name',
            'cpf' => 'required|cpf|size:11',
            'dataNascimento' => 'required|date|before_or_equal:'.now()->subYears(18)->format('Y-m-d'),
            'email' => 'required|email|unique:users,email,NULL,id,excluido,0',
            'senha' => 'required|min:6',
        ];
    }

    public static function messages():array {
        return [
            'nome' => [
                'required' => 'Campo obrigatório',
                'max' => 'O nome deve conter no máximo 255 caracteres'
            ],

            'cpf' => [
                'size' => 'CPF Incompleto'
            ],

            'dataNascimento' => [
                'required' => 'Campo obrigatório',
                'date' => 'Data inválida',
                'before_or_equal' => 'Você precisa ser maior de 18 anos'
            ],

            'email' => [
                'required' => 'Campo obrigatório',
                'unique' => 'O e-mail fornecido já está em uso'
            ] 
        ];
    }

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    // protected $casts = [
    //     'email_verified_at' => 'datetime',
    //     'password' => 'hashed',
    // ];
}
