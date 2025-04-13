<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoryService extends Model {
    use SoftDeletes;

    const CREATED_AT = null;
    const UPDATED_AT = null;
    const DELETED_AT = 'case_deleted_at';

    protected $primaryKey = "case_id";

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'categories_services';

    protected $hidden = [
        'case_deleted',
        'case_place_id'
    ]; 

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "case_name",
        "case_place_id"
    ];

    public function services(){
        return $this->hasMany(ServicePlace::class, 'sepl_case_id');
    }
}
