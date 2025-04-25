<?php

namespace App\Http\Controllers;

use App\DTOs\AppointmentDTO;
use App\DTOs\SearchFiltersCalendarAppointmentDTO;
use App\Http\Requests\AvailabilityRequest;
use App\Http\Requests\SearchFiltersCalendarAppointmentRequest;
use App\Http\Resources\Appointment\AppointmentWithFullServiceResource;
use App\Services\AppointmentService as Service;

class AppointmentController extends Controller
{
    public function __construct(private Service $service){}

    public function availability(AvailabilityRequest $request){
        return $this->service->getAvailability(AppointmentDTO::fromRequest($request));
    }

    public function book(AvailabilityRequest $request){
        return $this->service->book(AppointmentDTO::fromRequest($request));
    }

    public function getByFilters(SearchFiltersCalendarAppointmentRequest $request){
        return AppointmentWithFullServiceResource::collection($this->service->getByFilters(SearchFiltersCalendarAppointmentDTO::fromRequest($request)));
    }

    public function finish($id){
        return $this->service->finish($id);
    }
}
