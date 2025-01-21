<?php

use App\Http\Controllers\AuthEmployeeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\CategoryServiceController;
use App\Http\Controllers\ServicePlaceController;
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

/**
 * [EMPLOYEES]
 */
// AUTH
Route::post('/auth/employee/login', [AuthEmployeeController::class, 'login']);
Route::post('/auth/employee/logout', [AuthEmployeeController::class, 'logout']);
Route::post('/auth/employee/refresh', [AuthEmployeeController::class, 'refresh']);
Route::post('/auth/employee', [AuthEmployeeController::class, 'create']);
//PUBLICS
Route::get('/employee', [EmployeeController::class, 'read']);
Route::post('/employee', [EmployeeController::class, 'create']);
Route::put('/employee', [EmployeeController::class, 'update'])->middleware('auth:employee');
Route::get('/employee', [EmployeeController::class, 'one']);
Route::get('/employees/types', [EmployeeController::class, 'types']);
//ADMIN
Route::get('/admin/employees', [EmployeeController::class, 'list'])->middleware('auth:employee');
Route::delete('/admin/employee/{id}', [EmployeeController::class, 'delete']);


/**
 * [PLACES]
 */
//PUBLICS
Route::get('/place/categories', [PlaceController::class, 'categories']);
Route::get('/place/services/categories', [CategoryServiceController::class, 'list']);
Route::get('/place/services/{id}', [ServicePlaceController::class, 'byCategory']);
Route::get('/place/service/{id}', [ServicePlaceController::class, 'one']);
//ADMIN
Route::post('/admin/place', [PlaceController::class, 'create'])->middleware('auth:employee');
Route::post('/admin/place/service/category', [CategoryServiceController::class, 'create'])->middleware('auth:employee');
Route::post('/admin/place/service', [ServicePlaceController::class, 'create'])->middleware('auth:employee');
Route::delete('/admin/place/service/{id}', [ServicePlaceController::class, 'delete'])->middleware('auth:employee');
Route::put('/admin/place/service', [ServicePlaceController::class, 'update'])->middleware('auth:employee');

Route::post('/dashboard/init', [DashboardController::class, 'init'])->middleware('auth:employee');