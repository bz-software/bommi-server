<?php
 
namespace App\Enums;
use App\Helpers\EnumToArray as HelpersEnumToArray;
 
enum AppointmentStatus: int {
    use HelpersEnumToArray;
    
    case Pending = 0;
    case Confirmed = 1;
    case Completed = 2;
    case Canceled = 3;
}