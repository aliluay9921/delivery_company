<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\Pagination;
use App\Traits\SendResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    use SendResponse, Pagination;
    public function login(Request $request)
    {
        $request = $request->json()->all();
        $validator = Validator::make($request, [
            'password' => 'required',
            "phone_number" => 'required|exists:users,phone_number',
        ], [
            'phone_number.required' => ' يرجى ادخال رقم الهاتف ',
            'phone_number.exists' => 'رقم الهاتف الذي قمت بأدخالة غير صحيح  ',
            'password.required' => 'يرجى ادخال كلمة المرور ',
        ]);
        if ($validator->fails()) {
            return $this->send_response(401, 'خطأ بالمدخلات', $validator->errors(), []);
        }
        if (Auth::attempt(['phone_number' => $request['phone_number'], 'password' => $request['password']])) {
            $user = Auth::user();
            $token = $user->createToken($user->full_name)->accessToken;
            return $this->send_response(200, 'تم تسجيل الدخول بنجاح', [], User::With('permissions')->find(Auth::id()), $token);
        } else {
            return $this->send_response(401, 'هناك مشكلة تحقق من تطابق المدخلات', null, null, null);
        }
    }
}