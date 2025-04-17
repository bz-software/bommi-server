<?php
namespace App\Services;

use App\DTOs\AppointmentDTO;
use App\Exceptions\ServiceException;
use App\Models\EmployeePlace;
use App\Repositories\ServicePlaceRepository;
use Carbon\Carbon;
use App\Models\Appointment;
use App\Repositories\AppointmentRepository;
use App\Repositories\EmployeePlaceRepository;
use Illuminate\Support\Facades\Log;


class AppointmentService {
    public function __construct(
        private ServicePlaceRepository $spRepository,
        private EmployeePlaceRepository $epRepository,
        private AppointmentRepository $apRepository
    ) {}

    public function getAvaliableDates()
    {
        $dates = collect();

        $startDate = Carbon::today();

        for ($i = 0; $i <= Appointment::DAYS_SCHEDULE_OPEN; $i++) {
            $dates->push($startDate->copy()->addDays($i)->format('Y-m-d'));
        }

        return $dates;
    }

    public function getAvailability(AppointmentDTO $dto){
        $this->verifyServicesExist($dto->apmt_pla_id, $dto->appointmentServices);

        $idsServices = array_column($dto->appointmentServices, 'id');

        $total = $this->spRepository->totalByIds($idsServices);
        $totalDuration = $total->duration;
        $totalValue = $total->value;

        $currentDate = Carbon::now();

        $employees = $this->epRepository->getByServicesPlace($idsServices);
        
        // Check available dates (assuming appointments can be scheduled within the next 7 days)
        $avaliableDates = [];
        for ($i = 0; $i < Appointment::DAYS_SCHEDULE_OPEN; $i++) {
            foreach($employees as $employee){
                $checkedDate = $currentDate->copy()->addDays($i);
                
                $availableTimes = $this->getAvailableTimes($checkedDate, $totalDuration, $employee);

                if(!empty($availableTimes)){
                    $avaliableDates[] = [
                        'idEmployee' => $employee['ep_id'],
                        'date' => $checkedDate->format('Y-m-d'),
                        'times' => $availableTimes
                    ];
                }
            }
        }

        return response()->json($avaliableDates);

    }

    /**
     * Returns the available time slots for a given date,
     * considering the total duration of the services and existing appointments.
     *
     * @param Carbon $date The date to check availability for.
     * @param int $totalDuration The total service duration in minutes.
     * @param EmployeePlace $employee
     * @return array List of available time slots for the given date.
     */
    public function getAvailableTimes(Carbon $date, int $totalDuration, $employee): array
    {
        
        $startTime = Carbon::createFromTime(Appointment::PLACE_CLOCK_IN_HOUR, 0);
        $endTime = Carbon::createFromTime(Appointment::PLACE_CLOCK_OUT_HOUR, 0);

        // If the employee has a custom clock-in time, and it's more restrictive, use it
        if (!empty($employee->emp_clock_in_hour)) {
            $employeeClockIn = Carbon::createFromFormat('H:i', $employee->ep_clock_in);

            if ($employeeClockIn->greaterThan($startTime)) {
                $startTime = $employeeClockIn;
            }
        }

        // If the employee has a custom clock-out time, and it's more restrictive, use it
        if (!empty($employee->emp_clock_out_hour)) {
            $employeeClockOut = Carbon::createFromFormat('H:i', $employee->ep_clock_out);

            if ($employeeClockOut->lessThan($endTime)) {
                $endTime = $employeeClockOut;
            }
        }

        // Some code that might throw an exception
        $possibleTimes = [];
        while ($startTime->lte($endTime)) {
            // array_push($possibleTimes, $startTime->copy());
            array_push($possibleTimes, $startTime->copy()->format('H:i'));
            $startTime->addMinutes(Appointment::SLOT_SCHEDULE_INTERVAL_MINUTES); // Increment slot by the interval
        }

        return $possibleTimes;
    }

    private function verifyServicesExist($idPlace, $services){
        foreach($services as $service){
            if(empty($this->spRepository->getByIdAndPlace($service['id'], $idPlace))){
                throw new ServiceException([], 400, "Serviço não encontrado");
            }
        }
    }

    /**
     * Converts a time string in "HH:MM" format to total minutes as an integer.
     *
     * Example: "01:30" → 90
     *
     * @param string $time The time string in "HH:MM" format.
     * @return int The total number of minutes.
     */
    public function convertDurationStringToMinutes($tempo)
    {
        [$horas, $minutos] = explode(':', $tempo);
        return ((int) $horas * 60) + (int) $minutos;
    }

    private function getServicesByIds(array $ids){
        $services = array();
        foreach($ids as $id){
            array_push($services, $this->spRepository->getById($id));
        }

        return $services;
    }
}

