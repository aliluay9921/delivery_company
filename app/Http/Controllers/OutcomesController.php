<?php

namespace App\Http\Controllers;

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
        ]);
    }
}