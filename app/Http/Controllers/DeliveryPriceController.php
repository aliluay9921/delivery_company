<?php

namespace App\Http\Controllers;

use App\Models\DeliveryPrice;
use App\Traits\Pagination;
use App\Traits\SendResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DeliveryPriceController extends Controller
{
    use SendResponse, Pagination;
    public function getDeliveryPrice()
    {
        $get = DeliveryPrice::all();
        return $this->send_response(200, 'تم جلب الاسعار بنجاح', [], $get);
    }
    public function addDeliveryPrice(Request $request)
    {
        $request = $request->json()->all();
        $validator = Validator::make($request, [
            'location' => 'required',
            'company_cost' => 'required',
            'driver_cost' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->send_response(401, 'خطأ بالمدخلات', $validator->errors(), []);
        }
        $add = DeliveryPrice::create($request);
        return $this->send_response(200, 'تم اضافة سعر جديد', [], $add);
    }

    public function deleteDeliveryPrice(Request $request)
    {
        $request = $request->json()->all();
        $validator = Validator::make($request, [
            'delivery_price_id'       => 'required|exists:delivery_prices,id',
        ], [
            'delivery_price_id.required'       => 'يجب ادخال السعر المراد التعديل على معلوماته',
            'delivery_price_id.exists'         => 'السعر الذي قمت بأدخالة غير متوفر',
        ]);
        if ($validator->fails()) {
            return $this->send_response(401, 'خطأ بالمدخلات', $validator->errors(), []);
        }
        DeliveryPrice::find($request['delivery_price_id'])->delete();
        return $this->send_response(200, 'تم حذف المستخدم', [], []);
    }
}