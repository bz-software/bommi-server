<?php

namespace App\Http\Controllers;

use App\DTOs\AppointmentDTO;
use App\Http\Requests\AvailabilityRequest;
use App\Services\AppointmentService as Service;

class AppointmentController extends Controller
{
    public function __construct(private Service $service){}

    public function availability(AvailabilityRequest $request){
        return $this->service->getAvailability(AppointmentDTO::fromRequest($request));
    }
}
