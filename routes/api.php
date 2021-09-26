<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('login', [AuthController::class, 'login']);
Route::middleware(['auth:api'])->group(function () {
    Route::get('get_customers', [CustomersController::class, 'getCustomers']);
    Route::get('get_drivers', [DriverController::class, 'getDrivers']);

    Route::middleware(['admin'])->group(function () {
        Route::get('get_users', [UserController::class, 'getUsers']);

        Route::post('add_user', [UserController::class, 'addUser']);
        Route::post('add_customers', [CustomersController::class, 'addCustomers']);
        Route::post('add_driver', [DriverController::class, 'addDriver']);

        Route::put('edit_user', [UserController::class, 'editUser']);
        Route::put('edit_driver', [DriverController::class, 'editDriver']);
    });
});