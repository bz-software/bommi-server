<?php

use App\Http\Controllers\AuthEmployeeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PlaceController;
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
Route::post('/employee', [EmployeeController::class, 'create']);
Route::get('/employees', [EmployeeController::class, 'list']);
Route::get('/employees/init', [EmployeeController::class, 'init']);
Route::put('/employee', [EmployeeController::class, 'update'])->middleware('auth:employee');
Route::get('/employee', [EmployeeController::class, 'one']);

Route::post('/place', [PlaceController::class, 'create'])->middleware('auth:employee');
Route::get('/place/categories', [PlaceController::class, 'categories'])->middleware('auth:employee');

Route::post('/dashboard/init', [DashboardController::class, 'init'])->middleware('auth:employee');