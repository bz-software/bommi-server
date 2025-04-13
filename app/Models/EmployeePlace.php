<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\EmployeePlace
 * 
 * @property int $ep_id
 * @property int $ep_place_id
 * @property int $ep_employee_id
 * @property int $ep_deleted
 * @property string $ep_deleted_at
 * @property int $ep_employee_type
 * @property string $ep_clock_in
 * @property string $ep_clock_out
 */
class EmployeePlace extends Model {
    use SoftDeletes;

    protected $primaryKey = "ep_id";

    const CREATED_AT = NULL;
    const UPDATED_AT = NULL;
    const DELETED_AT = 'ep_deleted_at';
    
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

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'ep_employee_id', 'emp_id');
    }
}
