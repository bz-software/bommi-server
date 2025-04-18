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
        // Ensure all selected services exist for the given place
        $this->verifyServicesExist($dto->apmt_pla_id, $dto->appointmentServices);
    
        // Extract service IDs from the DTO
        $idsServices = array_column($dto->appointmentServices, 'id');
    
        // Get total duration and value of the selected services
        $total = $this->spRepository->totalByIds($idsServices);
        $totalDuration = $total->duration;
        // $totalValue = $total->value;
    
        // Convert the input date to a Carbon instance
        $date = Carbon::createFromFormat('Y-m-d', $dto->date);
    
        // Define the maximum allowed date for scheduling
        $maxDate = Carbon::now()->copy()->addDays(Appointment::DAYS_SCHEDULE_OPEN)->format('Y-m-d');
    
        // Throw an exception if the requested date is too far in the future
        if($date->gt($maxDate)){
            throw new ServiceException(["date" => "Data indisponível no momento"]);
        }
    
        // Get all employees that offer the selected services at the given place
        $employees = $this->epRepository->getByServicesPlace($idsServices);
        
        $allAvailableTimes = []; // Stores all available time slots from all employees
    
        // Loop through each employee to check their availability
        foreach ($employees as $employee) {
            // Get available times for the employee on the requested date
            $availableTimes = $this->getAvailableTimes($date, $totalDuration, $employee);
    
            // Merge available times into the main array (duplicates allowed for now)
            $allAvailableTimes = array_merge($allAvailableTimes, $availableTimes);
        }
    
        // Remove duplicate times and sort the list
        $uniqueTimes = array_unique($allAvailableTimes);
        sort($uniqueTimes);
    
        // If there are available time slots, return them
        if (!empty($uniqueTimes)) {
            $avaliableDates[] = [
                'date' => $date,
                'times' => $uniqueTimes
            ];
        }
    
        // Return the result as a JSON response
        return response()->json($avaliableDates);
    }
    
    /**
     * Returns the available time slots for a specific date and employee,
     * based on the total service duration, the employee's working hours,
     * and existing scheduled appointments.
     *
     * The method builds possible time slots within the employee's schedule,
     * filters out any that conflict with existing appointments, and returns
     * only the ones that are fully available.
     *
     * @param Carbon $date The date to check availability for.
     * @param int $totalDuration The total duration of all selected services, in minutes.
     * @param EmployeePlace $employee The employee to check availability for.
     * @return array A list of available time slots in 'H:i' format (e.g. '14:00', '14:30').
     */
    public function getAvailableTimes(Carbon $date, int $totalDuration, $employee): array
    {
        // Set default start and end time for the workday using the given date
        $startTime = $date->copy()->setTime(Appointment::PLACE_CLOCK_IN_HOUR, 0);
        $endTime = $date->copy()->setTime(Appointment::PLACE_CLOCK_OUT_HOUR, 0);

        // If the employee has a custom clock-in time, override the start time
        if (!empty($employee->emp_clock_in_hour)) {
            $employeeClockIn = Carbon::createFromFormat('H:i', $employee->ep_clock_in);
            $startTime->setTimeFrom($employeeClockIn);
        }

        // If the employee has a custom clock-out time, override the end time
        if (!empty($employee->emp_clock_out_hour)) {
            $employeeClockOut = Carbon::createFromFormat('H:i', $employee->ep_clock_out);
            $endTime->setTimeFrom($employeeClockOut);
        }

        $possibleTimes = []; // Will store all possible time slots within working hours
        $now = now(); // Current date and time
        $isToday = $date->isToday(); // Checks if the given date is today

        // Generate time slots within the employee's working hours
        while ($startTime->lte($endTime)) {
            // Only add future time slots if the date is today
            if (!$isToday || $startTime->gt($now)) {
                $possibleTimes[] = $startTime->copy()->format('H:i'); // Format: '14:00', '14:30', etc.
            }

            // Move to the next time slot based on defined interval
            $startTime->addMinutes(Appointment::SLOT_SCHEDULE_INTERVAL_MINUTES);
        }

        // Retrieve all existing appointments for the employee on that date
        $appointments = $this->apRepository->byDate($employee->ep_id, $date->format('Y-m-d'));

        $availableTimes = []; // Final array to hold non-conflicting time slots

        // Check each possible time slot against existing appointments
        foreach ($possibleTimes as $time) {
            // Build full datetime range for the current time slot
            $start = Carbon::parse($date->format('Y-m-d') . ' ' . $time);
            $end = $start->copy()->addMinutes($totalDuration);

            // Find if there's any appointment that overlaps with this time slot
            $conflict = $appointments->first(function ($appt) use ($start, $end) {
                $apptStart = Carbon::parse($appt->apmt_date); // Appointment start time
                $apptEnd = $apptStart->copy()->addMinutes($appt->apmt_total_time); // Appointment end time

                // Returns true if the time slot overlaps with an existing appointment
                return $start->lt($apptEnd) && $end->gt($apptStart);
            });

            // If no conflict found, the time slot is available
            if (!$conflict) {
                $availableTimes[] = $time;
            }
        }

        // Return only the time slots that are fully available
        return $availableTimes;
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

