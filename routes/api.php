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
use App\Models\Customer;
use App\Models\Driver;
use App\Models\GoodReceived;
use App\Models\GoodsDriver;
use App\Models\Income;
use App\Models\Outcome;
use App\Models\User;
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
    Route::get('get_users', [UserController::class, 'getUsers'])->middleware(['can:view,' . User::class]);
    Route::get('get_customers', [CustomersController::class, 'getCustomers'])->middleware(['can:view,' . Customer::class]);
    Route::get('customers_account', [CustomersController::class, 'customersAccount']);
    Route::get('drivers_account', [DriverController::class, 'driversAccount']);
    Route::get('get_drivers', [DriverController::class, 'getDrivers'])->middleware(['can:view,' . Driver::class]);
    Route::get('get_delivery_price', [DeliveryPriceController::class, 'getDeliveryPrice']);
    Route::get('get_goods_in_store', [GoodReceviedController::class, 'getGoodsInStore'])->middleware(['can:view,' . GoodReceived::class]);
    Route::get('get_outcomes', [OutcomesController::class, 'getOutcomes'])->middleware(['can:view,' . Outcome::class]);
    Route::get('get_incomes', [IncomesController::class, 'getIntcomes'])->middleware(['can:view,' . Income::class]);
    Route::get('company_balance', [UserController::class, 'companyBalance'])->middleware(['can:companyBalance,' . User::class]);

    Route::get('statistics', [UserController::class, 'statistics']);
    Route::get('get_logs', [UserController::class, 'getLogs']);
    Route::get('get_checks', [GoodDriverController::class, 'getChecks']);


    Route::post('add_delivery_price', [DeliveryPriceController::class, 'addDeliveryPrice']);
    Route::post('add_check', [GoodDriverController::class, 'addCheck'])->middleware(['can:create,' . GoodsDriver::class]);
    Route::post('add_outcome', [OutcomesController::class, 'addOutcome'])->middleware(['can:create,' . Outcome::class]);
    Route::post('add_income', [IncomesController::class, 'addIntcome'])->middleware(['can:create,' . Income::class]);
    Route::post('add_goods_to_store', [GoodReceviedController::class, 'addGoodsToStore'])->middleware(['can:create,' . GoodReceived::class]);
    Route::post('add_customers', [CustomersController::class, 'addCustomers'])->middleware(['can:create,' . Customer::class]);
    Route::post('add_driver', [DriverController::class, 'addDriver'])->middleware(['can:create,' . Driver::class]);
    Route::post('add_user', [UserController::class, 'addUser'])->middleware(['can:create,' . User::class]);



    Route::put('goods_archive', [GoodReceviedController::class, 'goodsArchive'])->middleware(['can:archive,' . GoodReceived::class]);
    // Route::put('edit_goods_in_store', [GoodReceviedController::class, 'editGoodsInStore']);
    Route::put('change_status_goods', [GoodReceviedController::class, 'changeGoodsStatus'])->middleware(['can:changeGoodsStatus,' . User::class]);
    Route::put('toggle_active_delivery_price', [DeliveryPriceController::class, 'toggleActiveDeliveryPrice']);

    Route::put('toggle_active_user', [UserController::class, 'toggleActiveUser'])->middleware(['can:toggleActiveUser,' . User::class]);
    Route::put('toggle_active_customer', [CustomersController::class, 'toggleActiveCustomer'])->middleware(['can:toggleActiveCustomer,' . Customer::class]);
    Route::put('toggle_active_driver', [DriverController::class, 'toggleActiveDriver'])->middleware(['can:toggleActiveDriver,' . Driver::class]);

    Route::put('edit_user', [UserController::class, 'editUser'])->middleware(['can:update,' . User::class]);
    Route::put('edit_driver', [DriverController::class, 'editDriver'])->middleware(['can:update,' . Driver::class]);
    Route::put('edit_customer', [CustomersController::class, 'editCustomer'])->middleware(['can:update,' . Customer::class]);



    Route::middleware(['admin'])->group(function () {
        Route::get('get_permissions', [UserController::class, 'getPermissions']);



        Route::delete('delete_user', [UserController::class, 'deleteUser']);
        Route::delete('delete_delivery_price', [DeliveryPriceController::class, 'deleteDeliveryPrice']);
        Route::delete('delete_driver', [DriverController::class, 'deleteDriver']);
        Route::delete('delete_customer', [CustomersController::class, 'deleteCustomer']);
    });
});