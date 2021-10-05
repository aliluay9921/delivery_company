<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Log;
use App\Models\User;
use App\Models\Driver;
use App\Models\Outcome;
use App\Models\Customer;
use App\Models\Permission;
use App\Traits\Pagination;
use App\Models\GoodsDriver;
use App\Models\GoodReceived;
use App\Models\Income;
use App\Traits\SendResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    use SendResponse, Pagination;
    public function getUsers()
    {
        if (isset($_GET['user_id'])) {
            $user = User::with('permissions')->find($_GET['user_id']);
            return $this->send_response(200, 'تم جلب المنشور بنجاح', [], $user);
        }
        $users = User::with('permissions');
        if (isset($_GET['query'])) {
            $columns = Schema::getColumnListing('users');
            foreach ($columns as $column) {
                $users->orWhere($column, 'LIKE', '%' . $_GET['query'] . '%');
            }
        }
        if (isset($_GET)) {
            foreach ($_GET as $key => $value) {
                if ($key == 'skip' || $key == 'limit' || $key == 'query') {
                    continue;
                } else {
                    $sort = $value == 'true' ? 'desc' : 'asc';
                    $users->orderBy($key,  $sort);
                }
            }
        }
        // return $users;
        if (!isset($_GET['skip']))
            $_GET['skip'] = 0;
        if (!isset($_GET['limit']))
            $_GET['limit'] = 10;
        $res = $this->paging($users,  $_GET['skip'],  $_GET['limit']);
        return $this->send_response(200, 'تم جلب المستخدمين بنجاح', [], $res["model"], null, $res["count"]);
    }
    public function getPermissions()
    {
        $permissions = Permission::all();
        return $this->send_response(200, 'تم جلب الصلاحيات بنجاح', [], $permissions);
    }

    public function addUser(Request $request)
    {
        $request = $request->json()->all();
        $validator = Validator::make($request, [
            'full_name'     => 'required',
            'phone_number'  => 'required|unique:users,phone_number|min:11|max:11',
            'password'      => 'required|min:6',
            'address'       => 'required',
            'salary'        => 'required',
            'permission_id' => 'required|exists:permissions,id'
        ], [
            'full_name.required' => 'يجب ادخال الاسم الكامل للموضف الجديد ',
            'phone_number.required' => 'يرجى ادخال رقم هاتف للموضف ',
            'phone_number.unique' => 'رقم الهاتف مستخدم سابقاً',
            'phone_number.min' => 'يرجى ادخال رقم هاتف صالح ',
            'phone_number.max' => 'يرجى ادخال رقم هاتف صالح',
            'password.required' => 'يرجى ادخال كلمة مرور خاصة بالموضف',
            'password.min' => 'يجب ان تكون كلمة المرور على الاقل 6',
            'address.required' => 'عنوان الموضف مطلوب',
            'salary.required' => 'يرجى ادخال راتب الموضف ',
            'permission_id.required' => 'يرجى ادخال الوضيفة التي يعمل بها هذا الموضف',
            'permission_id.exists' => 'يرجى ادخال معلومات صلاحية صحيحه',
        ]);
        if ($validator->fails()) {
            return $this->send_response(401, 'خطأ بالمدخلات', $validator->errors(), []);
        }

        $user = User::Create([
            'full_name' => $request['full_name'],
            'phone_number' => $request['phone_number'],
            'password' => bcrypt($request['password']),
            'address' => $request['address'],
            'salary' => $request['salary'],
        ]);
        $user->permissions()->sync($request['permission_id']);
        return $this->send_response(200, 'تم اضافة موضف جديد', [], User::with('permissions')->find($user->id));
    }

    public function editUser(Request $request)
    {
        $request = $request->json()->all();
        $user = User::find($request['user_id']);

        $validator = Validator::make($request, [
            'user_id'       => 'required|exists:users,id',
            'full_name'     => 'required',
            'phone_number'  => 'required|min:11|max:11|unique:users,phone_number,' . $user->id,
            'password'      => 'min:6',
            'address'       => 'required',
            'salary'        => 'required',
            'permission_id' => 'required'
        ], [
            'user_id.required'       => 'يجب ادخال المتسخدم المراد التعديل على معلوماته',
            'user_id.exists'         => 'المستخدم الذي قمت بأدخالة غير متوفر',
            'full_name.required'     => 'يجب ادخال الاسم الكامل للموضف الجديد ',
            'phone_number.required'  => 'يرجى ادخال رقم هاتف للموضف ',
            'phone_number.unique'    => 'رقم الهاتف مستخدم سابقاً',
            'phone_number.min'       => 'يرجى ادخال رقم هاتف صالح ',
            'phone_number.max'       => 'يرجى ادخال رقم هاتف صالح',
            'password.min'           => 'يجب ان تكون كلمة المرور على الاقل 6',
            'address.required'       => 'عنوان الموضف مطلوب',
            'salary.required'        => 'يرجى ادخال راتب الموضف ',
            'permission_id.required' => 'يرجى ادخال الوضيفة التي يعمل بها هذا الموضف'
        ]);
        if ($validator->fails()) {
            return $this->send_response(401, 'خطأ بالمدخلات', $validator->errors(), []);
        }
        $data = [];
        $data = [
            'full_name' => $request['full_name'],
            'phone_number' => $request['phone_number'],
            'address' => $request['address'],
            'salary' => $request['salary'],
        ];
        if (array_key_exists('passwprd', $request)) {
            $data['password'] = bcrypt($request['password']);
        }
        $user->update($data);
        $user->permissions()->sync($request['permission_id']);
        return $this->send_response(200, 'تم التعديل على معلومات الموضف', [], User::with('permissions')->find($request['user_id']));
    }

    public function deleteUser(Request $request)
    {
        $request = $request->json()->all();
        $validator = Validator::make($request, [
            'user_id'       => 'required|exists:users,id',
        ], [
            'user_id.required'       => 'يجب ادخال المتسخدم المراد التعديل على معلوماته',
            'user_id.exists'         => 'المستخدم الذي قمت بأدخالة غير متوفر',
        ]);
        if ($validator->fails()) {
            return $this->send_response(401, 'خطأ بالمدخلات', $validator->errors(), []);
        }
        User::find($request['user_id'])->delete();
        return $this->send_response(200, 'تم حذف المستخدم', [], []);
    }
    public function toggleActiveUser(Request $request)
    {
        $request = $request->json()->all();
        $validator = Validator::make($request, [
            'user_id' => 'required|exists:users,id',
        ], [
            'user_id.required' => 'يجب ادخال موضف',
            'user_id.exists' => 'الموضف الذي قمت بأدخالة غير صحيح'
        ]);
        if ($validator->fails()) {
            return $this->send_response(401, 'خطأ بالمدخلات', $validator->errors(), []);
        }
        $user = User::find($request['user_id']);
        $user->update([
            'active' => !$user->active
        ]);
        return $this->send_response(200, 'تم تغيرر حالة الموضف', [], $user);
    }

    public function getLogs()
    {
        $logs = Log::with('customer', 'driver', 'user');
        if (isset($_GET['query'])) {
            $columns = Schema::getColumnListing('logs');
            foreach ($columns as $column) {
                $logs->orWhere($column, 'LIKE', '%' . $_GET['query'] . '%');
            }
        }
        if (isset($_GET)) {
            foreach ($_GET as $key => $value) {
                if ($key == 'skip' || $key == 'limit' || $key == 'query') {
                    continue;
                } else {
                    $sort = $value == 'true' ? 'desc' : 'asc';
                    $logs->orderBy($key,  $sort);
                }
            }
        }
        if (!isset($_GET['skip']))
            $_GET['skip'] = 0;
        if (!isset($_GET['limit']))
            $_GET['limit'] = 10;
        $res = $this->paging($logs,  $_GET['skip'],  $_GET['limit']);
        return $this->send_response(200, 'تم جلب البضائع بنجاح', [], $res["model"], null, $res["count"]);
    }

    public function companyBalance()
    {
        $data = [];
        $data['صافي ربح الشركة'] = 0;
        $data['ارباح اليوم'] = 0;

        $employees = User::all()->sum('salary');
        if (isset($_GET['from']) && isset($_GET['to'])) {
            $customers = Customer::whereBetween('created_at', [$_GET['from'], $_GET['to']])->get()->sum('balance');
            $drivers = Driver::whereBetween('created_at', [$_GET['from'], $_GET['to']])->get()->sum('balance');
            $company_balance = GoodReceived::where('paid_company', false)->where('order_status', 2)->whereBetween('created_at', [$_GET['from'], $_GET['to']])->get();
            foreach ($company_balance as $balance) {
                $data['صافي ربح الشركة'] += $balance->delevery_price->company_cost;
                if ($balance->created_at->toDateString() === Carbon::today()->toDateString()) {
                    $data['ارباح اليوم'] += $balance->delevery_price->company_cost;
                }
            }
        } else {
            $customers = Customer::all()->sum('balance');
            $drivers = Driver::all()->sum('balance');
            $outcom = Outcome::first();
            $company_balance = $outcom->CompanyBalance;
            $data['صافي ربح الشركة'] = $company_balance;
            $company_balance_daily = GoodReceived::where('paid_company', false)->where('order_status', 2)->get();
            foreach ($company_balance_daily as $balance) {
                if ($balance->created_at->toDateString() === Carbon::today()->toDateString()) {
                    $data['ارباح اليوم'] += $balance->delevery_price->company_cost;
                }
            }
        }
        $data['صافي رصيد العملاء'] = $customers;
        $data['صافي رصيد المندوبين'] = $drivers;
        $data['رواتب الموضفين'] = $employees;
        return $this->send_response(200, 'احصائيات الشركة', [], [$data]);
    }

    public function statistics()
    {

        $statistics = [];
        if (isset($_GET['good_receiveds'])) {
            $statistics = [
                'order_status_zero' => GoodReceived::where('order_status', 0)->count(),
                'order_status_first' => GoodReceived::where('order_status', 1)->count(),
                'order_status_second' => GoodReceived::where('order_status', 2)->count(),
                'order_status_third' => GoodReceived::where('order_status', 3)->count(),
                'order_status_fourth' => GoodReceived::where('order_status', 4)->count(),
                'order_archive' => GoodReceived::where('archive', 1)->count(),
                'order_not_archive' => GoodReceived::where('archive', 0)->count(),
                'gift' =>  GoodReceived::where('type_deliver', 0)->count(),
                'money' => GoodReceived::where('type_deliver', 1)->count(),
                'goods' => GoodReceived::count(),
            ];
        }
        if (isset($_GET['drivers'])) {
            $statistics = [
                'drivers' => Driver::count(),
                'drivers_zero' => Driver::where('type_vehicle', 0)->count(),
                'drivers_one' => Driver::where('type_vehicle', 1)->count(),
                'drivers_two' => Driver::where('type_vehicle', 2)->count(),
                'drivers_active' => Driver::where('active', 1)->count(),
                'drivers_disActive' => Driver::where('active', 0)->count(),
            ];
        }
        if (isset($_GET['customers'])) {
            $statistics = [
                'customers' => Customer::count(),
                'customers_active' => Customer::where('active', 1)->count(),
                'customers_disActive' => Customer::where('active', 0)->count(),
            ];
        }
        if (isset($_GET['goods_drivers'])) {
            $statistics = [
                'goods_drivers' => GoodsDriver::count(),
            ];
        }
        if (isset($_GET['incomes'])) {
            $statistics = [
                'incomes' => Income::count(),
                'incomes_owner' => Income::where('type', 0)->count(),
                'incomes_customer' => Income::where('type', 1)->count(),
            ];
        }
        if (isset($_GET['outcomes'])) {
            $statistics = [
                'outcomes' => Income::count(),
                'outcomes_owner' => Income::whereIn('type', [2, 3])->count(),
                'outcomes_customer' => Income::where('type', 1)->count(),
                'outcomes_driver' => Income::where('type', 0)->count(),
            ];
        }
        return $this->send_response(200, 'احصائيات', [], [$statistics]);
    }
}