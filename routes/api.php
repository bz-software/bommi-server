<?php

use App\Http\Controllers\AuthEmployeeController;
use App\Http\Controllers\EmployeeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/ping', function(){
    return ['pong' => true];
});

Route::get('/401', [AuthEmployeeController::class, 'unauthorized'])->name('login');

Route::post('/auth/employee/login', [AuthEmployeeController::class, 'login']);
Route::post('/auth/employee/logout', [AuthEmployeeController::class, 'logout']);
Route::post('/auth/employee/refresh', [AuthEmployeeController::class, 'refresh']);
Route::post('/auth/employee', [AuthEmployeeController::class, 'create']);

Route::get('/employee', [EmployeeController::class, 'read']);
Route::get('/employees', [EmployeeController::class, 'list']);
Route::get('/employee/{id}', [EmployeeController::class, 'one']);
Route::put('/employee', [EmployeeController::class, 'update']);