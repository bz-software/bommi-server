<?php

namespace App\DTOs;

use Illuminate\Http\Request;

class AbstractDTO
{
    public function toArray(): array
    {
        return (array) $this;
    }
}
