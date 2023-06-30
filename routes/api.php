<?php

use App\Http\Controllers\TestController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PlaceController;
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
// Places
Route::resource('place', PlaceController::class);
Route::post('/place/register', [PlaceController::class, 'register']);

Route::resource('test', TestController::class);
Route::resource('user', UserController::class);