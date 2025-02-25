<?php

use App\Http\Controllers\AuthEmployeeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\CategoryServiceController;
use App\Http\Controllers\ServicePlaceController;
use Illuminate\Support\Facades\Route;

Route::get('/ping', fn() => ['pong' => true]);
Route::get('/401', [AuthEmployeeController::class, 'unauthorized'])->name('login');

// Authentication Routes
Route::prefix('auth/employee')->group(function () {
    Route::post('/login', [AuthEmployeeController::class, 'login']);
    Route::post('/logout', [AuthEmployeeController::class, 'logout']);
    Route::post('/refresh', [AuthEmployeeController::class, 'refresh']);
    Route::post('/', [AuthEmployeeController::class, 'create']);
});

// Employee Routes
Route::prefix('employee')->group(function () {
    Route::get('/', [EmployeeController::class, 'read']);
    Route::post('/', [EmployeeController::class, 'create']);
    Route::get('/{id}', [EmployeeController::class, 'one']);
    Route::middleware('auth:employee')->group(function () {
        Route::put('/', [EmployeeController::class, 'update']);
    });
});

// Admin Employee Routes
Route::prefix('admin/employees')->middleware('auth:employee')->group(function () {
    Route::get('/', [EmployeeController::class, 'list']);
    Route::delete('/{id}', [EmployeeController::class, 'delete']);
    Route::get('/types', [EmployeeController::class, 'types']);
});

// Place Routes
Route::prefix('place')->group(function () {
    Route::get('/categories', [PlaceController::class, 'categories']);
    Route::get('/services/categories', [CategoryServiceController::class, 'list']);
    Route::get('/services/{id}', [ServicePlaceController::class, 'byCategory']);
    Route::get('/service/{id}', [ServicePlaceController::class, 'one']);
});

// Admin Place Routes
Route::prefix('admin/place')->middleware('auth:employee')->group(function () {
    Route::post('/', [PlaceController::class, 'create']);
    Route::post('/service/category', [CategoryServiceController::class, 'create']);
    Route::post('/service', [ServicePlaceController::class, 'create']);
    Route::put('/service', [ServicePlaceController::class, 'update']);
    Route::put('/service/category', [CategoryServiceController::class, 'update']);
    Route::delete('/service/{id}', [ServicePlaceController::class, 'delete']);
    Route::delete('/service/category/{id}', [CategoryServiceController::class, 'delete']);
});

// Dashboard Routes
Route::prefix('dashboard')->middleware('auth:employee')->group(function () {
    Route::post('/init', [DashboardController::class, 'init']);
});
