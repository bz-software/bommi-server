<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $table = 'clients';
    protected $primaryKey = 'cli_id';
    public $timestamps = true; 

    const CREATED_AT = 'cli_created_at';

    protected $fillable = [
        'cli_name',
        'cli_phone_number',
    ];
}