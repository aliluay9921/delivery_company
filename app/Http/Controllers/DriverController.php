<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\GoodReceived;
use App\Traits\Pagination;
use App\Traits\SendResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class DriverController extends Controller
{
    use SendResponse, Pagination;
    public function getDrivers()
    {
        if (isset($_GET['driver_id'])) {
            $driver = Driver::find($_GET['driver_id']);
            return $this->send_response(200, 'تم جلب ألمندوب بنجاح', [], $driver);
        }
        $drivers = Driver::select('id', 'name', 'phone_number', 'phone_number2', 'address', 'number_car', 'type_vehicle', 'active', 'balance');
        if (isset($_GET['query'])) {
            $columns = Schema::getColumnListing('drivers');
            foreach ($columns as $column) {
                $drivers->orWhere($column, 'LIKE', '%' . $_GET['query'] . '%');
            }
        }
        if (isset($_GET)) {
            foreach ($_GET as $key => $value) {
                if ($key == 'skip' || $key == 'limit' || $key == 'query') {
                    continue;
                } else {
                    $sort = $value == 'true' ? 'desc' : 'asc';
                    $drivers->orderBy($key,  $sort);
                }
            }
        }
        if (!isset($_GET['skip']))
            $_GET['skip'] = 0;
        if (!isset($_GET['limit']))
            $_GET['limit'] = 10;
        $res = $this->paging($drivers,  $_GET['skip'],  $_GET['limit']);
        return $this->send_response(200, 'تم جلب المندوبين بنجاح', [], $res["model"], null, $res["count"]);
    }
    public function addDriver(Request $request)
    {
        $request = $request->json()->all();
        $validator = Validator::make($request, [
            'name'     => 'required|unique:drivers,name',
            'phone_number'  => 'required|min:11|max:11',
            'phone_number2'      => 'nullable|min:11|max:11',
            'address'       => 'required',
        ], [
            'name.required' => 'يجب ادخال أسم العميل  ',
            'phone_number.required' => 'يرجى ادخال رقم هاتف للموضف ',
            'name.unique' => 'الاسم مستخدم سابقاً',
            'phone_number.min' => 'يرجى ادخال رقم هاتف صالح ',
            'phone_number.max' => 'يرجى ادخال رقم هاتف صالح',
            'phone_number2.min' => 'يرجى ادخال رقم هاتف صالح ',
            'phone_number2.max' => 'يرجى ادخال رقم هاتف صالح',
            'address.required' => 'عنوان العميل مطلوب',
        ]);
        if ($validator->fails()) {
            return $this->send_response(401, 'خطأ بالمدخلات', $validator->errors(), []);
        }
        $data = [];
        $data = [
            'name' => $request['name'],
            'phone_number' => $request['phone_number'],
            'address' => $request['address'],
        ];
        if (array_key_exists('number_car', $request)) {
            $data['number_car'] = $request['number_car'];
        }
        if (array_key_exists('phone_number2', $request)) {
            $data['phone_number2'] = $request['phone_number2'];
        }
        if (array_key_exists('type_vehicle', $request)) {
            $data['type_vehicle'] = $request['type_vehicle'];
        }
        $driver = Driver::create($data);
        return $this->send_response(200, 'تم اضافة مندوب جديد', [], Driver::find($driver->id));
    }
    public function editDriver(Request $request)
    {
        $request = $request->json()->all();
        $driver = Driver::find($request['driver_id']);
        $validator = Validator::make($request, [
            'driver_id' => 'required|exists:drivers,id',
            'name'     => 'required|unique:drivers,name,' . $driver->id,
            'phone_number'  => 'required|min:11|max:11',
            'phone_number2'      => 'nullable|min:11|max:11',
            'address'       => 'required',
        ], [
            'driver_id.required' => 'يجب ادخال مندوب',
            'driver_id.exists' => 'يجب ادخال مندوب صحيح',
            'name.required' => 'يجب ادخال أسم المندوب  ',
            'name.unique' => 'هذا الاسم مستخدم سابقاً ',
            'phone_number.required' => 'يرجى ادخال رقم هاتف للمندوب ',
            'phone_number.min' => 'يرجى ادخال رقم هاتف صالح ',
            'phone_number.max' => 'يرجى ادخال رقم هاتف صالح',
            'phone_number2.min' => 'يرجى ادخال رقم هاتف صالح ',
            'phone_number2.max' => 'يرجى ادخال رقم هاتف صالح',
            'address.required' => 'عنوان المندوب مطلوب',
        ]);
        if ($validator->fails()) {
            return $this->send_response(401, 'خطأ بالمدخلات', $validator->errors(), []);
        }
        $data = [];
        $data = [
            'name' => $request['name'],
            'phone_number' => $request['phone_number'],
            'address' => $request['address'],
        ];
        if (array_key_exists('number_car', $request)) {
            $data['number_car'] = $request['number_car'];
        }
        if (array_key_exists('phone_number2', $request)) {
            $data['phone_number2'] = $request['phone_number2'];
        }
        if (array_key_exists('type_vehicle', $request)) {
            $data['type_vehicle'] = $request['type_vehicle'];
        }
        $driver->update($data);
        return $this->send_response(200, 'تم التعديل على معلومات المندوب', [], $driver);
    }
    public function deleteDriver(Request $request)
    {
        $request = $request->json()->all();
        $validator = Validator::make($request, [
            'driver_id'       => 'required|exists:drivers,id',
        ], [
            'driver_id.required'       => 'يجب ادخال المندوب المراد التعديل على معلوماته',
            'driver_id.exists'         => 'المندوب الذي قمت بأدخالة غير متوفر',
        ]);
        if ($validator->fails()) {
            return $this->send_response(401, 'خطأ بالمدخلات', $validator->errors(), []);
        }
        Driver::find($request['driver_id'])->delete();
        return $this->send_response(200, 'تم حذف المندوب', [], []);
    }
    public function toggleActiveDriver(Request $request)
    {
        $request = $request->json()->all();
        $validator = Validator::make($request, [
            'driver_id' => 'required|exists:drivers,id',
        ], [
            'driver_id.required' => 'يجب ادخال العميل',
            'driver_id.exists' => 'العميل الذي قمت بأدخالة غير صحيح'
        ]);
        if ($validator->fails()) {
            return $this->send_response(401, 'خطأ بالمدخلات', $validator->errors(), []);
        }
        $driver = Driver::find($request['driver_id']);
        $driver->update([
            'active' => !$driver->active
        ]);
        return $this->send_response(200, 'تم تغيرر حالة العميل', [], $driver);
    }
    public function driversAccount()
    {
        if (isset($_GET['driver_id'])) {
            $goods = GoodReceived::with(['goods_driver','customer', 'delevery_price'])->whereHas('goods_driver', function ($q) {
                $q->where('driver_id',  $_GET['driver_id']);
            });
            // return $goods;
            if (isset($_GET['query'])) {
                $columns = Schema::getColumnListing('good_receiveds');
                foreach ($columns as $column) {
                    $goods->orWhere($column, 'LIKE', '%' . $_GET['query'] . '%');
                }
            }
            if (isset($_GET)) {
                foreach ($_GET as $key => $value) {
                    if ($key == 'skip' || $key == 'limit' || $key == 'query' || $key == 'driver_id') {
                        continue;
                    } else {
                        $sort = $value == 'true' ? 'desc' : 'asc';
                        $goods->orderBy($key,  $sort);
                    }
                }
            }
            if (!isset($_GET['skip']))
                $_GET['skip'] = 0;
            if (!isset($_GET['limit']))
                $_GET['limit'] = 10;
            $res = $this->paging($goods,  $_GET['skip'],  $_GET['limit']);
            return $this->send_response(200, 'تم جلب العملاء بنجاح', [], $res["model"], null, $res["count"]);
        }
    }
}
