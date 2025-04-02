<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\EmployeePlace
 * 
 * @property int $ep_id
 * @property int $ep_place_id
 * @property int $ep_employee_id
 * @property int $ep_deleted
 * @property string $ep_deleted_at
 * @property int $ep_employee_type
 */
class EmployeePlace extends Model {

    protected $primaryKey = "ep_id";

    const CREATED_AT = NULL;
    const UPDATED_AT = NULL;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'employees_place';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "ep_place_id",
        "ep_employee_id",
        "ep_deleted"
    ];

    public static function rules(){
        return [
            'ep_clock_in' => [
                'nullable',
                'regex:/^(?:[01]\d|2[0-3]):[0-5]\d$/',
                function ($attribute, $value, $fail) {
                    if (request('ep_clock_out') && $value > request('ep_clock_out')) {
                        $fail('O horário de entrada não pode ser maior que o de saída.');
                    }
                },
            ],
            'ep_clock_out' => [
                'nullable',
                'regex:/^(?:[01]\d|2[0-3]):[0-5]\d$/',
                function ($attribute, $value, $fail) {
                    if (request('ep_clock_in') && $value < request('ep_clock_in')) {
                        $fail('O horário de saída não pode ser menor que o de entrada.');
                    }
                },
            ],
        ];
    }

    public static function messages() {
        return [
            'ep_clock_in.required' => 'O horário de entrada é obrigatório.',
            'ep_clock_in.regex' => 'O horário de entrada deve estar no formato HH:MM.',

            'ep_clock_out.required' => 'O horário de saída é obrigatório.',
            'ep_clock_out.regex' => 'O horário de saída deve estar no formato HH:MM.',
        ];
    }

    public static function unset($idEmployee){
        $now = Carbon::now();
        $idOwner = auth('employee')->user()->emp_id;
        $idPlace = auth('employee')->user()->emp_place_id_logged;

        $employeePlace = EmployeePlace::select('employees_place.*')
            ->join('places as p', 'p.pla_id', '=', 'employees_place.ep_place_id')
            ->join('employees as owner', 'owner.emp_id', '=', 'p.pla_employee_id')
            ->where('p.pla_id', '=', $idPlace)
            ->where('owner.emp_id', '=', $idOwner)
            ->where('employees_place.ep_employee_id', '=', $idEmployee)
            ->first();

        if(!empty($employeePlace)){
            $employeePlace->ep_deleted = true;
            $employeePlace->ep_deleted_at = $now->toDateTimeString();
            
            return $employeePlace->save();
        }

        return false;
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'ep_employee_id', 'emp_id');
    }
}
