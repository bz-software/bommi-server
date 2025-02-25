<?php
 
namespace App\Enums;
use App\Helpers\EnumToArray as HelpersEnumToArray;
 
enum EmployeeType: int {
    use HelpersEnumToArray;
    
    case Owner = 1;
    case Admin = 2;
    case Secretary = 3;
    case Default = 4;
}