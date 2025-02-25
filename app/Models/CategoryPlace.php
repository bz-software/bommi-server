<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryPlace extends Model {

    protected $primaryKey = "capl_id";
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'categories_places';
}
