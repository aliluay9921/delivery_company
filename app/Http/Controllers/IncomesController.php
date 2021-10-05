<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Traits\Pagination;
use App\Traits\SendResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class IncomesController extends Controller
{
    use SendResponse, Pagination;
    public function addIntcome(Request $request)
    {
        $request = $request->json()->all();
        $validator = Validator::make($request, [
            'type' => 'required',
            'name' => 'required',
            'value' => 'required',
            'customer_id' => 'exists:customers,id',
        ], [
            'name.required' => 'هذا العنصر مطلوب ',
            'value.required' => 'هذا العنصر مطلوب',
            'type.required' => 'هذا العنصر مطلوب',
            'customer_id.exists' => 'يجب ادخال عنصر صحيح',
        ]);
        if ($validator->fails()) {
            return $this->send_response(401, 'خطأ بالمدخلات', $validator->errors(), []);
        }
        $data = [];
        $data = [
            'type' => $request['type'],
            'name' => $request['name'],
            'value' => $request['value'],
        ];
        if (array_key_exists('customer_id', $request)) {
            $data['customer_id'] = $request['customer_id'];
        }
        $income = Income::create($data);
        return $this->send_response(200, 'تم اضافة قيمة الى القاصة ', [], Income::find($income->id));
    }
}