<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Outcome;
use App\Traits\Pagination;
use App\Traits\SendResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OutcomesController extends Controller
{
    use SendResponse, Pagination;
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
        return $this->send_response(200, 'تم اضافة صرف', [], Outcome::find($outcome->id));
    }
}