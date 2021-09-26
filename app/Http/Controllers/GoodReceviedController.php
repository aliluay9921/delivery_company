<?php

namespace App\Http\Controllers;

use App\Models\GoodReceived;
use App\Traits\Pagination;
use App\Traits\SendResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GoodReceviedController extends Controller
{
    use SendResponse, Pagination;
    public function addGoodsToStore(Request $request)
    {
        $request = $request->json()->all();
        $validator = Validator::make($request, [
            'customer_id'       => 'required|exists:customers,id',
            'delivery_price_id'       => 'required|exists:delivery_prices,id',
            'type_deliver'          => 'required',
            'buyers_address'          => 'required',
            'buyers_phone1'            => 'required',
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
        //return $request;

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
            'order_status' => 0
        ];
        if (array_key_exists('phone_number2', $request)) {
            $data['phone_number2'] = $request['phone_number2'];
        }
        if (array_key_exists('note', $request)) {
            $data['note'] = $request['note'];
        }
        $new = GoodReceived::create($data);
        return $this->send_response(200, 'تم اضافة البضاعة جديدة', [], $new);
    }
}