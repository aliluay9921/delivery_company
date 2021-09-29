<?php

namespace App\Http\Controllers;

use App\Models\GoodReceived;
use App\Traits\Pagination;
use App\Traits\SendResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GoodDriverController extends Controller
{
    use SendResponse, Pagination;
    public function addGoodToDriver(Request $request)
    {
        $request = $request->json()->all();
        $validator = Validator::make($request, [
            'driver_id' => 'required|exists:drivers,id',
            'goods_received_id' => 'required|exists:good_receiveds,id',
        ], [
            'driver_id.required' => 'يجب ادخال منودب',
            'driver_id.exists' => 'يجب ادخال مندوب متوفر',
            'goods_received_id.required' => 'يجب ادخال بضاعة متوفرة',
            'goods_received_id.exists' => 'يجب ادخال بضاعة متوفرة',

        ]);
        if ($validator->fails()) {
            return $this->send_response(401, 'خطأ بالمدخلات', $validator->errors(), []);
        }
        $price = 0;
        $goods = GoodReceived::find($request['goods_received_id']);
        return $goods;
    }
}