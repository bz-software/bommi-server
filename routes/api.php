<?php

use App\Http\Controllers\AuthEmployeeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\CategoryServiceController;
use App\Http\Controllers\EmployeeServiceController;
use App\Http\Controllers\ServicePlaceController;
use App\Models\EmployeePlace;
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
//ADMIN
Route::get('/admin/employee', [EmployeeController::class, 'one'])->middleware('auth:employee');
Route::put('/admin/employee', [EmployeeController::class, 'update'])->middleware('auth:employee');
Route::put('/admin/employee/attendance', [EmployeeController::class, 'updateAttendance'])->middleware('auth:employee');
Route::get('/admin/employee/attendance', [EmployeeController::class, 'listAttendance'])->middleware('auth:employee');
Route::post('/admin/employee', [EmployeeController::class, 'create'])->middleware('auth:employee');
Route::get('/admin/employees', [EmployeeController::class, 'list'])->middleware('auth:employee');
Route::delete('/admin/employee/{id}', [EmployeeController::class, 'delete'])->middleware('auth:employee');
Route::get('/admin/employees/types', [EmployeeController::class, 'types'])->middleware('auth:employee');
// EMPLOYEE SERVICES
Route::post('/admin/employee/service', [EmployeeServiceController::class, 'create'])->middleware('auth:employee');
Route::get('/admin/employee/services', [EmployeeServiceController::class, 'list'])->middleware('auth:employee');
Route::delete('/admin/employee/service/{id}', [EmployeeServiceController::class, 'delete'])->middleware('auth:employee');


// ============================================================================================================


/**
 * [PLACES]
 */
// ==== ADMIN ====
// CategoryService
Route::get('/admin/place/services/categories', [CategoryServiceController::class, 'list'])->middleware('auth:employee');
Route::post('/admin/place/service/category', [CategoryServiceController::class, 'create'])->middleware('auth:employee');
Route::put('/admin/place/service/category', [CategoryServiceController::class, 'update'])->middleware('auth:employee');
Route::delete('/admin/place/service/category/{id}', [CategoryServiceController::class, 'delete'])->middleware('auth:employee');
//ServicePlace
Route::get('/admin/place/service/{id}', [ServicePlaceController::class, 'one'])->middleware('auth:employee');
Route::get('/admin/place/services/{id}', [ServicePlaceController::class, 'byCategory'])->middleware('auth:employee');
Route::post('/admin/place/service', [ServicePlaceController::class, 'create'])->middleware('auth:employee');
Route::delete('/admin/place/service/{id}', [ServicePlaceController::class, 'delete'])->middleware('auth:employee');
Route::put('/admin/place/service', [ServicePlaceController::class, 'update'])->middleware('auth:employee');
//Place
Route::get('/admin/place/categories', [PlaceController::class, 'categories'])->middleware('auth:employee');
Route::post('/admin/place', [PlaceController::class, 'create'])->middleware('auth:employee');


// ==== PUBLICS ====
Route::get('/place/exist-by-slug/{slug}', [PlaceController::class, 'existBySlug']);
Route::get('/place/slug/{slug}', [PlaceController::class, 'getAllBySlug']);
// ============================================================================================================


/**
 * [DASHBOARD]
 */
Route::post('/dashboard/init', [DashboardController::class, 'init'])->middleware('auth:employee');