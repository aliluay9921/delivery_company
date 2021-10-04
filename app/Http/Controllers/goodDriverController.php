<?php

namespace App\Http\Controllers;

use App\Traits\Pagination;
use App\Models\GoodsDriver;
use App\Models\GoodReceived;
use App\Models\Log;
use App\Traits\SendResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class GoodDriverController extends Controller
{


    use SendResponse, Pagination;
    public function getChecks()
    {
        $checks = GoodsDriver::with('driver', 'good', 'good.customer', 'good.delevery_price');
        if (isset($_GET['query'])) {
            $columns = Schema::getColumnListing('goods_drivers');
            foreach ($columns as $column) {
                $checks->orWhere($column, 'LIKE', '%' . $_GET['query'] . '%');
            }
        }
        if (isset($_GET)) {
            foreach ($_GET as $key => $value) {
                if ($key == 'skip' || $key == 'limit' || $key == 'query') {
                    continue;
                } else {
                    $sort = $value == 'true' ? 'desc' : 'asc';
                    $checks->orderBy($key,  $sort);
                }
            }
        }
        if (!isset($_GET['skip']))
            $_GET['skip'] = 0;
        if (!isset($_GET['limit']))
            $_GET['limit'] = 10;
        $res = $this->paging($checks,  $_GET['skip'],  $_GET['limit']);
        return $this->send_response(200, 'تم جلب البضائع بنجاح', [], $res["model"], null, $res["count"]);
    }
    public function addCheck(Request $request)
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
        foreach ($request['goods_received_id'] as $good) {
            $goods = GoodReceived::find($good);
            $price = $goods->delevery_price->company_cost + $goods->delevery_price->driver_cost + $goods->price;
            $goods->update([
                'order_status' => 1
            ]);
            $check = GoodsDriver::create([
                'driver_id' => $request['driver_id'],
                'goods_received_id' => $good,
                'final_price'   => $price
            ]);
            Log::create([
                'target_id' => $request['driver_id'],
                'value' => $price,
                'log_type' => 'تم اصدار وصل جديد',
                'type' => 0,
                'user_id'   => auth()->user()->id,
                'note' => $goods->note,
            ]);
        }

        return $this->send_response(200, 'تم انشاء وصل', [], GoodsDriver::with('driver', 'good', 'good.customer', 'good.delevery_price')->whereIn('id', $request['goods_received_id'])->get());
    }
}