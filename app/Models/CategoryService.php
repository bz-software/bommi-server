<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryService extends Model {
    const CREATED_AT = null;
    const UPDATED_AT = null;

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
}
