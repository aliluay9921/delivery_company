<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\Income;
use App\Traits\Pagination;
use App\Traits\SendResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class IncomesController extends Controller
{
    use SendResponse, Pagination;

    public function getIntcomes()
    {
        $incomes = Income::with('customer');
        if (isset($_GET['query'])) {
            $columns = Schema::getColumnListing('outcomes');
            foreach ($columns as $column) {
                $incomes->orWhere($column, 'LIKE', '%' . $_GET['query'] . '%');
            }
        }
        if (isset($_GET)) {
            foreach ($_GET as $key => $value) {
                if ($key == 'skip' || $key == 'limit' || $key == 'query') {
                    continue;
                } else {
                    $sort = $value == 'true' ? 'desc' : 'asc';
                    $incomes->orderBy($key,  $sort);
                }
            }
        }
        if (!isset($_GET['skip']))
            $_GET['skip'] = 0;
        if (!isset($_GET['limit']))
            $_GET['limit'] = 10;
        $res = $this->paging($incomes,  $_GET['skip'],  $_GET['limit']);
        return $this->send_response(200, 'تم جلب الاضافات بنجاح', [], $res["model"], null, $res["count"]);
    }


    public function addIntcome(Request $request)
    {
        $request = $request->json()->all();
        $validator = Validator::make($request, [
            'type' => 'required',
            'name' => 'required',
            'value' => 'required',
            'customer_id' => 'nullable|exists:customers,id',
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
        $log = [];
        $data = [
            'type' => $request['type'],
            'name' => $request['name'],
            'value' => $request['value'],
        ];
        $log = [
            'value' => $request['value'],
            'log_type' =>  $request['name'],
            'user_id'   => auth()->user()->id,
        ];

        if (array_key_exists('customer_id', $request)) {
            $data['customer_id'] = $request['customer_id'];
            $log['target_id'] = $request['customer_id'];
            $log['type'] = 2;
        } else {
            $log['type'] = 3;
        }
        $income = Income::create($data);
        Log::create($log);
        return $this->send_response(200, 'تم اضافة قيمة الى القاصة ', [], Income::with('customer')->find($income->id));
    }
}