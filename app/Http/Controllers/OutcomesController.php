<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\Driver;
use App\Models\Outcome;
use App\Traits\Pagination;
use App\Traits\SendResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class OutcomesController extends Controller
{
    use SendResponse, Pagination;
    public function getOutcomes()
    {
        $outcomes = Outcome::with('driver', 'customer');
        if (isset($_GET['query'])) {
            $columns = Schema::getColumnListing('outcomes');
            foreach ($columns as $column) {
                $outcomes->orWhere($column, 'LIKE', '%' . $_GET['query'] . '%');
            }
        }
        if (isset($_GET)) {
            foreach ($_GET as $key => $value) {
                if ($key == 'skip' || $key == 'limit' || $key == 'query') {
                    continue;
                } else {
                    $sort = $value == 'true' ? 'desc' : 'asc';
                    $outcomes->orderBy($key,  $sort);
                }
            }
        }
        if (!isset($_GET['skip']))
            $_GET['skip'] = 0;
        if (!isset($_GET['limit']))
            $_GET['limit'] = 10;
        $res = $this->paging($outcomes,  $_GET['skip'],  $_GET['limit']);
        return $this->send_response(200, 'تم جلب المصاريف بنجاح', [], $res["model"], null, $res["count"]);
    }
    public function addOutcome(Request $request)
    {
        $request = $request->json()->all();
        $validator = Validator::make($request, [
            'name' => 'required',
            'value' => 'required',
            'type' => 'required'
        ], [
            'name.required' => 'هذا العنصر مطلوب ',
            'value.required' => 'هذا العنصر مطلوب',
            'type.required' => 'هذا العنصر مطلوب',
        ]);
        if ($validator->fails()) {
            return $this->send_response(401, 'خطأ بالمدخلات', $validator->errors(), []);
        }
        $outcome = Outcome::create($request);
        $log = [];
        $log = [
            'target_id' => $request['target_id'],
            'value' => $request['value'],
            'log_type' =>  $request['name'],
            'user_id'   => auth()->user()->id,
        ];
        if ($request['type'] == 0) {
            $log['type'] = 0;
        } elseif ($request['type'] == 1) {
            $log['type'] = 1;
        } elseif ($request['type'] == 2) {
            $log['type'] = 2;
        }
        Log::create($log);

        return $this->send_response(200, 'تم اضافة صرف', [], Outcome::find($outcome->id));
    }
}