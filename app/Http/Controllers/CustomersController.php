<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\GoodReceived;
use App\Models\GoodsDriver;
use App\Traits\Pagination;
use App\Traits\SendResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\Validator;

class CustomersController extends Controller
{
    use SendResponse, Pagination;

    public function getCustomers()
    {
        if (isset($_GET['customer_id'])) {
            $customer = GoodReceived::where($_GET['customer_id']);
            return $this->send_response(200, 'تم جلب العميل بنجاح', [], $customer);
        }
        $customers = Customer::withCount('goods_recevied');
        if (isset($_GET['query'])) {
            $columns = Schema::getColumnListing('customers');
            foreach ($columns as $column) {
                $customers->orWhere($column, 'LIKE', '%' . $_GET['query'] . '%');
            }
        }
        if (isset($_GET)) {
            foreach ($_GET as $key => $value) {
                if ($key == 'skip' || $key == 'limit' || $key == 'query') {
                    continue;
                } else {
                    $sort = $value == 'true' ? 'desc' : 'asc';
                    $customers->orderBy($key,  $sort);
                }
            }
        }
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
            'phone_number'  => 'required|min:11|max:11',
            'phone_number2'      => 'nullable|min:11|max:11',
            'address'       => 'required',
        ], [
            'name.required' => 'يجب ادخال أسم العميل  ',
            'phone_number.required' => 'يرجى ادخال رقم هاتف للموضف ',
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
        return $this->send_response(200, 'تم اضافة موضف جديد', [], Customer::find($new->id));
    }
    public function editCustomer(Request $request)
    {
        $request = $request->json()->all();
        $customer = Customer::find($request['customer_id']);
        $validator = Validator::make($request, [
            'customer_id' => 'required|exists:customers,id',
            'name'     => 'required',
            'phone_number'  => 'required|min:11|max:11',
            'phone_number2'      => 'nullable|min:11|max:11',
            'address'       => 'required',
        ], [
            'name.required' => 'يجب ادخال أسم العميل  ',
            'phone_number.required' => 'يرجى ادخال رقم هاتف للعميل ',
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
        if (array_key_exists('phone_number2', $request)) {
            $data['phone_number2'] = $request['phone_number2'];
        }
        $customer->update($data);
        return $this->send_response(200, 'تم التعديل على معلومات العميل', [], $customer);
    }
    public function deleteCustomer(Request $request)
    {
        $request = $request->json()->all();
        $validator = Validator::make($request, [
            'customer_id'                => 'required|exists:customers,id',
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
    public function toggleActiveCustomer(Request $request)
    {
        $request = $request->json()->all();
        $validator = Validator::make($request, [
            'customer_id' => 'required|exists:customers,id',
        ], [
            'customer_id.required' => 'يجب ادخال العميل',
            'customer_id.exists' => 'العميل الذي قمت بأدخالة غير صحيح'
        ]);
        if ($validator->fails()) {
            return $this->send_response(401, 'خطأ بالمدخلات', $validator->errors(), []);
        }
        $customer = Customer::find($request['customer_id']);
        $customer->update([
            'active' => !$customer->active
        ]);
        return $this->send_response(200, 'تم تغيرر حالة العميل', [], $customer);
    }
    public function customersAccount()
    {

        $customer = GoodReceived::with('delevery_price')->where('customer_id', $_GET['customer_id']);

        if (isset($_GET['query'])) {
            $columns = Schema::getColumnListing('good_receiveds');
            foreach ($columns as $column) {
                $customer->Where($column, 'LIKE', '%' . $_GET['query'] . '%');
            }
        }
        if (isset($_GET)) {
            foreach ($_GET as $key => $value) {
                if ($key == 'skip' || $key == 'limit' || $key == 'query') {
                    continue;
                } else {
                    $sort = $value == 'true' ? 'desc' : 'asc';
                    $customer->orderBy($key,  $sort);
                }
            }
        }
        if (!isset($_GET['skip']))
            $_GET['skip'] = 0;
        if (!isset($_GET['limit']))
            $_GET['limit'] = 10;
        $res = $this->paging($customer,  $_GET['skip'],  $_GET['limit']);
        return $this->send_response(200, 'تم جلب العملاء بنجاح', [], $res["model"], null, $res["count"]);
    }
}