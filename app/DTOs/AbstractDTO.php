<?php

namespace App\DTOs;

use Illuminate\Http\Request;

class AbstractDTO
{
    public function toArray(){
        return (array) $this;
    }
}
