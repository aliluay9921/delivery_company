<?php

namespace App\Http\Controllers;

use App\Traits\Pagination;
use App\Models\GoodReceived;
use App\Traits\SendResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class GoodReceviedController extends Controller
{
    use SendResponse, Pagination;

    public function random_code()
    {
        $code = substr(str_shuffle("0123456789ABCD"), 0, 6);
        $get = GoodReceived::where('code', $code)->first();
        if ($get) {
            return $this->random_code();
        } else {
            return $code;
        }
    }


    public function getGoodsInStore()
    {
        $good_receiveds = GoodReceived::with('customer', 'delevery_price');
        if (isset($_GET['query'])) {
            $columns = Schema::getColumnListing('good_receiveds');
            foreach ($columns as $column) {
                $good_receiveds->orWhere($column, 'LIKE', '%' . $_GET['query'] . '%');
            }
        }
        if (isset($_GET)) {
            foreach ($_GET as $key => $value) {
                if ($key == 'skip' || $key == 'limit' || $key == 'query') {
                    continue;
                } else {
                    $sort = $value == 'true' ? 'desc' : 'asc';
                    $good_receiveds->orderBy($key,  $sort);
                }
            }
        }
        if (!isset($_GET['skip']))
            $_GET['skip'] = 0;
        if (!isset($_GET['limit']))
            $_GET['limit'] = 10;
        $res = $this->paging($good_receiveds,  $_GET['skip'],  $_GET['limit']);
        return $this->send_response(200, 'تم جلب البضائع بنجاح', [], $res["model"], null, $res["count"]);
    }
    public function addGoodsToStore(Request $request)
    {
        $request = $request->json()->all();
        $validator = Validator::make($request, [
            'customer_id'       => 'required|exists:customers,id',
            'delivery_price_id'       => 'required|exists:delivery_prices,id',
            'type_deliver'          => 'required',
            'buyers_address'          => 'required',
            'buyers_phone1'            => 'required|min:11|max:11',
            'buyers_name'          => 'required',
            'content'            => 'required',
            'quantity'            => 'required',
            'price'            => 'required',
        ], [
            'customer_id.required'       => 'يجب اختيار العميل',
            'customer_id.exists'         => 'العميل الذي قمت بأدخالة غير صحيح',
            'delivery_price_id.required'       => 'يجب اختيار نوع التوصيل',
            'delivery_price_id.exists'         => 'نوع التوصيل الذي اخترته غير صحيح',
            'type_deliver.required'       => 'يجب ادخال عملية التوصيل ',
            'buyers_address.required'       => 'يجب ادخال عنوان الزبون',
            'buyers_name.required'       => 'يجب ادخال اسم الزبون',
            'buyers_phone1.required'       => 'يجب ادخال رقم هاتف الزبون',
            'content.required'       => 'يجب ادخال نوع المادة ',
            'quantity.required'       => 'يجب الكمية  ',

        ]);
        if ($validator->fails()) {
            return $this->send_response(401, 'خطأ بالمدخلات', $validator->errors(), []);
        }

        $data = [];
        $data = [
            'customer_id' => $request['customer_id'],
            'delivery_price_id' => $request['delivery_price_id'],
            'type_deliver' => $request['type_deliver'],
            'buyers_address' => $request['buyers_address'],
            'buyers_phone1' => $request['buyers_phone1'],
            'buyers_name' => $request['buyers_name'],
            'content' => $request['content'],
            'quantity' => $request['quantity'],
            'price' => $request['price'],
            'order_status' => 0,
            'code' => $this->random_code(),
        ];
        if (array_key_exists('buyers_phone2', $request)) {
            $data['buyers_phone2'] = $request['buyers_phone2'];
        }
        if (array_key_exists('note', $request)) {
            $data['note'] = $request['note'];
        }
        $new = GoodReceived::create($data);
        return $this->send_response(200, 'تم اضافة البضاعة جديدة', [], GoodReceived::with('customer', 'delevery_price')->find($new->id));
    }
    public function editGoodsInStore(Request $request)
    {
        $request = $request->json()->all();
        $validator = Validator::make($request, [
            'goods_id' => 'required|exists:good_receiveds,id',
            'customer_id' => 'required|exists:customers,id',
            'delivery_price_id' => 'required|exists:delivery_prices,id',
            'type_deliver'          => 'required',
            'buyers_address'          => 'required',
            'buyers_phone1'            => 'required|min:11|max:11',
            'buyers_name'          => 'required',
            'content'            => 'required',
            'quantity'            => 'required',
            'price'            => 'required',
        ], [
            'goods_id.required' => 'يجب ادخال بضاعة',
            'goods_id.exists' => 'يجب ادخال بضاعة متوفرة',
            'customer_id.required'       => 'يجب اختيار العميل',
            'customer_id.exists'         => 'العميل الذي قمت بأدخالة غير صحيح',
            'delivery_price_id.required'       => 'يجب اختيار نوع التوصيل',
            'delivery_price_id.exists'         => 'نوع التوصيل الذي اخترته غير صحيح',
            'type_deliver.required'       => 'يجب ادخال عملية التوصيل ',
            'buyers_address.required'       => 'يجب ادخال عنوان الزبون',
            'buyers_name.required'       => 'يجب ادخال اسم الزبون',
            'buyers_phone1.required'       => 'يجب ادخال رقم هاتف الزبون',
            'content.required'       => 'يجب ادخال نوع المادة ',
            'quantity.required'       => 'يجب الكمية  ',

        ]);
        if ($validator->fails()) {
            return $this->send_response(401, 'خطأ بالمدخلات', $validator->errors(), []);
        }
        $data = [];
        $data = [
            'customer_id' => $request['customer_id'],
            'delivery_price_id' => $request['delivery_price_id'],
            'type_deliver' => $request['type_deliver'],
            'buyers_address' => $request['buyers_address'],
            'buyers_phone1' => $request['buyers_phone1'],
            'buyers_name' => $request['buyers_name'],
            'content' => $request['content'],
            'quantity' => $request['quantity'],
            'price' => $request['price'],
        ];
        if (array_key_exists('buyers_phone2', $request)) {
            $data['buyers_phone2'] = $request['buyers_phone2'];
        }
        if (array_key_exists('note', $request)) {
            $data['note'] = $request['note'];
        }
        $goods = GoodReceived::find($request['goods_id']);
        $goods->update($data);
        return $this->send_response(200, 'تم تعديل معلومات البضاعة', [], GoodReceived::with('customer', 'delevery_price')->find($request['goods_id']));
    }
    public function changeGoodsStatus(Request $request)
    {
        $request = $request->json()->all();
        $validator = Validator::make($request, [
            'goods_id' => 'required|exists:good_receiveds,id',
            'order_status' => 'required'
        ], [
            'goods_id.required' => 'يجب ادخال بضاعة',
            'goods_id.exists' => 'يجب ادخال بضاعة متوفرة',
            'order_status' => 'حاله البضاعة مطلوبة'

        ]);
        if ($validator->fails()) {
            return $this->send_response(401, 'خطأ بالمدخلات', $validator->errors(), []);
        }
        foreach ($request['goods_id'] as $good) {
            $product = GoodReceived::find($good);
            $product->update([
                'order_status' => $request['order_status']
            ]);
        }
        return $this->send_response(200, 'تم تعديل معلومات البضاعة', [], GoodReceived::with('customer', 'delevery_price')->find($request['goods_id']));
    }
}