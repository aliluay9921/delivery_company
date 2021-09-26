<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Traits\Pagination;
use App\Traits\SendResponse;
use Illuminate\Http\Request;
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
        $drivers = Driver::select('id', 'name', 'phone_number', 'phone_number2', 'address', 'number_car', 'type_vehicle');
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
            'name'     => 'required',
            'phone_number'  => 'required|unique:drivers,phone_number|min:11|max:11',
            'phone_number2'      => 'unique:drivers,phone_number|min:11|max:11',
            'address'       => 'required',
        ], [
            'name.required' => 'يجب ادخال أسم العميل  ',
            'phone_number.required' => 'يرجى ادخال رقم هاتف للموضف ',
            'phone_number.unique' => 'رقم الهاتف مستخدم سابقاً',
            'phone_number.min' => 'يرجى ادخال رقم هاتف صالح ',
            'phone_number.max' => 'يرجى ادخال رقم هاتف صالح',
            'phone_number2.unique' => 'رقم الهاتف مستخدم سابقاً',
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
        if (array_key_exists('type_vehicle', $request)) {
            $data['type_vehicle'] = $request['type_vehicle'];
        }
        $driver = Driver::create($data);
        return $this->send_response(200, 'تم اضافة مندوب جديد', [], $driver);
    }
    public function editDriver(Request $request)
    {
        $request = $request->json()->all();
        $driver = Driver::find($request['driver_id']);
        $validator = Validator::make($request, [
            'driver_id' => 'required|exists:drivers,id',
            'name'     => 'required',
            'phone_number'  => 'required|min:11|max:11|unique:drivers,phone_number,' . $driver->id,
            'phone_number2'      => 'min:11|max:11|unique:drivers,phone_number,' . $driver->id,
            'address'       => 'required',
        ], [
            'name.required' => 'يجب ادخال أسم العميل  ',
            'phone_number.required' => 'يرجى ادخال رقم هاتف للموضف ',
            'phone_number.unique' => 'رقم الهاتف مستخدم سابقاً',
            'phone_number.min' => 'يرجى ادخال رقم هاتف صالح ',
            'phone_number.max' => 'يرجى ادخال رقم هاتف صالح',
            'phone_number2.unique' => 'رقم الهاتف مستخدم سابقاً',
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
}
