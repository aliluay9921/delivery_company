<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\DeliveryPriceController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\GoodReceviedController;
use App\Http\Controllers\UserController;
use App\Models\Customer;
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
    Route::get('get_delivery_price', [DeliveryPriceController::class, 'getDeliveryPrice']);



    Route::post('add_goods_to_store', [GoodReceviedController::class, 'addGoodsToStore']);
    Route::put('edit_goods_in_store', [GoodReceviedController::class, 'editGoodsInStore']);


    Route::middleware(['admin'])->group(function () {
        Route::get('get_users', [UserController::class, 'getUsers']);
        Route::get('get_permissions', [UserController::class, 'getPermissions']);

        Route::post('add_delivery_price', [DeliveryPriceController::class, 'addDeliveryPrice']);
        Route::post('add_user', [UserController::class, 'addUser']);
        Route::post('add_customers', [CustomersController::class, 'addCustomers']);
        Route::post('add_driver', [DriverController::class, 'addDriver']);

        Route::put('toggle_active_user', [UserController::class, 'toggleActiveUser']);
        Route::put('toggle_active_customer', [CustomersController::class, 'toggleActiveCustomer']);
        Route::put('toggle_active_driver', [DriverController::class, 'toggleActiveDriver']);
        Route::put('edit_user', [UserController::class, 'editUser']);
        Route::put('edit_driver', [DriverController::class, 'editDriver']);
        Route::put('edit_customer', [CustomersController::class, 'editCustomer']);


        Route::delete('delete_user', [UserController::class, 'deleteUser']);
        Route::delete('delete_delivery_price', [DeliveryPriceController::class, 'deleteDeliveryPrice']);
        Route::delete('delete_driver', [DriverController::class, 'deleteDriver']);
        Route::delete('delete_customer', [CustomersController::class, 'deleteCustomer']);
    });
});