<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Place extends Model {
    const CREATED_AT = 'pla_created_at';
    const UPDATED_AT = 'pla_changed_at';

    protected $primaryKey = "pla_id";

    const REQUIRED_MSG = 'Campo obrigatório';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "pla_name",
        "pla_phone_number",
        "pla_category_id",
        "pla_cnpj",
        "pla_has_cnpj",
        "pla_avatar_url"
    ];

    public static function rules():array {
        return [
            'pla_name' => 'required|max:200',
            'pla_phone_number' => 'required|max:11|phonenumber',
            'pla_category_id' => 'required|exists:categories_places,capl_id',
            'pla_cnpj' => 'cnpj',
            'pla_has_cnpj' => 'required|boolean',
        ];
    }

    public static function messages():array {
        return [
            'pla_name' => [
                'required' => self::REQUIRED_MSG,
                'max' => 'O nome deve conter no máximo 200 caracteres'
            ],

            'pla_phone_number' => [
                'max' => 'Telefone incorreto',
                'required' => self::REQUIRED_MSG
            ],

            'pla_category_id' => [
                'required' => self::REQUIRED_MSG,
                'exists' => "Categoria inválida"
            ],

            'pla_has_cnpj' => [
                'required' => self::REQUIRED_MSG
            ],
        ];
    }

    /**
     * Retorna o estabelecimento do funcionario logado
     * @return null|array[]|array
     */
    public static function getByIdEmployee($id){
        $employee =  Employee::where('emp_id', $id)->first();

        if(empty($employee['emp_place_id_logged'])){
            // Buscando todos os estabelecimentos que o funcionario pode acessar
            $places = Place::select('*')
                ->join('employees_place', 'employees_place.ep_place_id', '=', 'places.pla_id')
            ->where('employees_place.ep_employee_id', $id)
            ->get();

            $total = intval($places->count());
            
            if($total > 1){
                // se for maior que um, retorna todos
                return $places->load('address');
            }else if($total == 0){
                return null;
            }else{
                // se for um, seta ele como o estabelecimento em uso e retorna o mesmo
                $place = $places->first();
                Employee::setPlaceLogged($place->pla_id);

                return $place->load('address');
            }
        }else{
            // caso o funcionario tenha um estabelecimento em uso, primeiro verifica se ele ainda tem acesso e depois o retorna
            $place = Place::select('*')
                ->join('employees_place', 'employees_place.ep_place_id', '=', 'places.pla_id')
            ->where('employees_place.ep_employee_id', $id)
            ->where('employees_place.ep_place_id', $employee['emp_place_id_logged'])
            ->first();

            return $place->load('address');
        }
    }

    public function address(): HasOne
    {
        return $this->hasOne(PlaceAddress::class, 'plad_place_id');
    }
}
