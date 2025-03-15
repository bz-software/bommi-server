<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Symfony\Component\VarDumper\VarDumper;

class ServicePlace extends Model {
    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $primaryKey = "sepl_id";

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'services_places';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "sepl_name",
        "sepl_description",
        "sepl_case_id",
        "sepl_price",
        "sepl_duration"
    ];

    public static function getAllByCategory($idCategory){
        $data = ServicePlace::select("*")
            ->where('sepl_case_id', $idCategory)
            ->where('sepl_deleted', false)
        ->get();

        return $data;
    }

    public static function getByNameAndCategory($idCategory, $name){
        $data = ServicePlace::select("*")
            ->whereRaw('LOWER(sepl_name) = ?', [$name])
            ->where('sepl_case_id', $idCategory)
            ->where('sepl_deleted', false)
        ->first();

        return $data;
    }

    public static function getById($id){
        $data = ServicePlace::select("*")
            ->where('sepl_id', $id)
            ->where('sepl_deleted', false)
        ->first();

        return $data;
    }

    public function categoryService(): BelongsTo
    {
        return $this->belongsTo(CategoryService::class, 'sepl_case_id', 'case_id');
    }
}
