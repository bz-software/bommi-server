<?php
namespace App\Repositories;

use App\Exceptions\RepositoryException;
use App\Models\Appointment as Model;
use App\Models\EmployeePlace;
use Carbon\Carbon;

class AppointmentRepository
{
    public function __construct(private Model $model, private EmployeePlace $epModel) {}


    public function employeeAvaliable($start, $end, $idEmployeePlace){
        $employee = $this->epModel::where('ep_id', $idEmployeePlace)
        ->whereDoesntHave('appointments', function ($query) use ($start, $end) {
            $query->where('apmt_date', '>=', $start)
                  ->where('apmt_date', '<', $end);
        })
        ->first();

        if(!empty($employee)){
            return true;
        }

        return false;
    }
}

