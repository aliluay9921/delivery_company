<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\DeliveryPriceController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\goodDriverController;
use App\Http\Controllers\GoodReceviedController;
use App\Http\Controllers\IncomesController;
use App\Http\Controllers\OutcomesController;
use App\Http\Controllers\UserController;
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
    Route::get('customers_account', [CustomersController::class, 'customersAccount']);
    Route::get('drivers_account', [DriverController::class, 'driversAccount']);
    Route::get('get_drivers', [DriverController::class, 'getDrivers']);
    Route::get('get_delivery_price', [DeliveryPriceController::class, 'getDeliveryPrice']);
    Route::get('get_goods_in_store', [GoodReceviedController::class, 'getGoodsInStore']);
    Route::get('get_outcomes', [OutcomesController::class, 'getOutcomes']);
    Route::get('get_incomes', [IncomesController::class, 'getIntcomes']);
    Route::get('company_balance', [UserController::class, 'companyBalance']);
    Route::get('statistics', [UserController::class, 'statistics']);
    Route::get('get_logs', [UserController::class, 'getLogs']);
    Route::get('get_checks', [GoodDriverController::class, 'getChecks']);



    Route::post('add_check', [GoodDriverController::class, 'addCheck']);
    Route::post('add_outcome', [OutcomesController::class, 'addOutcome']);
    Route::post('add_income', [IncomesController::class, 'addIntcome']);
    Route::post('add_goods_to_store', [GoodReceviedController::class, 'addGoodsToStore']);
    Route::post('add_customers', [CustomersController::class, 'addCustomers']);
    Route::post('add_driver', [DriverController::class, 'addDriver']);



    Route::put('goods_archive', [GoodReceviedController::class, 'goodsArchive']);
    Route::put('edit_goods_in_store', [GoodReceviedController::class, 'editGoodsInStore']);
    Route::put('change_status_goods', [GoodReceviedController::class, 'changeGoodsStatus']);
    Route::put('toggle_active_delivery_price', [DeliveryPriceController::class, 'toggleActiveDeliveryPrice']);
    Route::put('toggle_active_user', [UserController::class, 'toggleActiveUser']);
    Route::put('toggle_active_customer', [CustomersController::class, 'toggleActiveCustomer']);
    Route::put('toggle_active_driver', [DriverController::class, 'toggleActiveDriver']);
    Route::put('edit_user', [UserController::class, 'editUser']);
    Route::put('edit_driver', [DriverController::class, 'editDriver']);
    Route::put('edit_customer', [CustomersController::class, 'editCustomer']);


    Route::middleware(['admin'])->group(function () {
        Route::get('get_users', [UserController::class, 'getUsers']);
        Route::get('get_permissions', [UserController::class, 'getPermissions']);

        Route::post('add_delivery_price', [DeliveryPriceController::class, 'addDeliveryPrice']);
        Route::post('add_user', [UserController::class, 'addUser']);


        Route::delete('delete_user', [UserController::class, 'deleteUser']);
        Route::delete('delete_delivery_price', [DeliveryPriceController::class, 'deleteDeliveryPrice']);
        Route::delete('delete_driver', [DriverController::class, 'deleteDriver']);
        Route::delete('delete_customer', [CustomersController::class, 'deleteCustomer']);
    });
});