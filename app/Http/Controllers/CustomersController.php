<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Traits\Pagination;
use App\Traits\SendResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomersController extends Controller
{
    use SendResponse, Pagination;

    public function getCustomers()
    {
        if (isset($_GET['customer_id'])) {
            $customer = Customer::find($_GET['customer_id']);
            return $this->send_response(200, 'تم جلب العميل بنجاح', [], $customer);
        }
        $customers = Customer::select('id', 'name', 'phone_number', 'phone_number2', 'address', 'code');
        if (!isset($_GET['skip']))
            $_GET['skip'] = 0;
        if (!isset($_GET['limit']))
            $_GET['limit'] = 10;
        $res = $this->paging($customers,  $_GET['skip'],  $_GET['limit']);
        return $this->send_response(200, 'تم جلب العملاء بنجاح', [], $res["model"], null, $res["count"]);
    }
    public function addCustomers(Request $request)
    {
        $request = $request->json()->all();
        $validator = Validator::make($request, [
            'name'     => 'required',
            'phone_number'  => 'required|unique:customers,phone_number|min:11|max:11',
            'phone_number2'      => 'unique:customers,phone_number|min:11|max:11',
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
        $code = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6);
        $customers = Customer::all();
        foreach ($customers as $customer) {
            if ($customer->code === $code) {
                $code = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6);
            }
        }

        $data = [
            'name' => $request['name'],
            'phone_number' => $request['phone_number'],
            'address' => $request['address'],
            'code'    => $code
        ];
        if (array_key_exists('phone_number2', $request)) {
            $data['phone_number2'] = $request['phone_number2'];
        }
        $new = Customer::Create($data);
        return $this->send_response(200, 'تم اضافة موضف جديد', [], $new);
    }
    public function deleteCustomer(Request $request)
    {
        $request = $request->json()->all();
        $validator = Validator::make($request, [
            'customer_id'       => 'required|exists:customers,id',
        ], [
            'customer_id.required'       => 'يجب ادخال العميل المراد التعديل على معلوماته',
            'customer_id.exists'         => 'العميل الذي قمت بأدخالة غير متوفر',
        ]);
        if ($validator->fails()) {
            return $this->send_response(401, 'خطأ بالمدخلات', $validator->errors(), []);
        }
        Customer::find($request['customer_id'])->delete();
        return $this->send_response(200, 'تم حذف العميل', [], []);
    }
}