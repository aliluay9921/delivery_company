<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\User;
use App\Traits\Pagination;
use App\Traits\SendResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    use SendResponse, Pagination;
    public function getUsers()
    {
        if (isset($_GET['user_id'])) {
            $user = User::with('permissions')->find($_GET['user_id']);
            return $this->send_response(200, 'تم جلب المنشور بنجاح', [], $user);
        }
        $users = User::with('permissions');
        if (!isset($_GET['skip']))
            $_GET['skip'] = 0;
        if (!isset($_GET['limit']))
            $_GET['limit'] = 10;
        $res = $this->paging($users,  $_GET['skip'],  $_GET['limit']);
        return $this->send_response(200, 'تم جلب المستخدمين بنجاح', [], $res["model"], null, $res["count"]);
    }
    public function getPermissions()
    {
        $permissions = Permission::all();
        return $this->send_response(200, 'تم جلب الصلاحيات بنجاح', [], $permissions);
    }

    public function addUser(Request $request)
    {
        $request = $request->json()->all();
        $validator = Validator::make($request, [
            'full_name'     => 'required',
            'phone_number'  => 'required|unique:users,phone_number|min:11|max:11',
            'password'      => 'required|min:6',
            'address'       => 'required',
            'salary'        => 'required',
            'permission_id' => 'required'
        ], [
            'full_name.required' => 'يجب ادخال الاسم الكامل للموضف الجديد ',
            'phone_number.required' => 'يرجى ادخال رقم هاتف للموضف ',
            'phone_number.unique' => 'رقم الهاتف مستخدم سابقاً',
            'phone_number.min' => 'يرجى ادخال رقم هاتف صالح ',
            'phone_number.max' => 'يرجى ادخال رقم هاتف صالح',
            'password.required' => 'يرجى ادخال كلمة مرور خاصة بالموضف',
            'password.min' => 'يجب ان تكون كلمة المرور على الاقل 6',
            'address.required' => 'عنوان الموضف مطلوب',
            'salary.required' => 'يرجى ادخال راتب الموضف ',
            'permission_id.required' => 'يرجى ادخال الوضيفة التي يعمل بها هذا الموضف'
        ]);
        if ($validator->fails()) {
            return $this->send_response(401, 'خطأ بالمدخلات', $validator->errors(), []);
        }

        $user = User::Create([
            'full_name' => $request['full_name'],
            'phone_number' => $request['phone_number'],
            'password' => bcrypt($request['password']),
            'address' => $request['address'],
            'salary' => $request['salary'],
        ]);
        $user->permissions()->sync($request['permission_id']);
        return $this->send_response(200, 'تم اضافة موضف جديد', [], $user);
    }

    public function editUser(Request $request)
    {
        $request = $request->json()->all();
        $user = User::find($request['user_id']);

        $validator = Validator::make($request, [
            'user_id'       => 'required|exists:users,id',
            'full_name'     => 'required',
            'phone_number'  => 'required|min:11|max:11|unique:users,phone_number,' . $user->id,
            'password'      => 'required|min:6',
            'address'       => 'required',
            'salary'        => 'required',
            'permission_id' => 'required'
        ], [
            'user_id.required'       => 'يجب ادخال المتسخدم المراد التعديل على معلوماته',
            'user_id.exists'         => 'المستخدم الذي قمت بأدخالة غير متوفر',
            'full_name.required'     => 'يجب ادخال الاسم الكامل للموضف الجديد ',
            'phone_number.required'  => 'يرجى ادخال رقم هاتف للموضف ',
            'phone_number.unique'    => 'رقم الهاتف مستخدم سابقاً',
            'phone_number.min'       => 'يرجى ادخال رقم هاتف صالح ',
            'phone_number.max'       => 'يرجى ادخال رقم هاتف صالح',
            'password.required'      => 'يرجى ادخال كلمة مرور خاصة بالموضف',
            'password.min'           => 'يجب ان تكون كلمة المرور على الاقل 6',
            'address.required'       => 'عنوان الموضف مطلوب',
            'salary.required'        => 'يرجى ادخال راتب الموضف ',
            'permission_id.required' => 'يرجى ادخال الوضيفة التي يعمل بها هذا الموضف'
        ]);
        if ($validator->fails()) {
            return $this->send_response(401, 'خطأ بالمدخلات', $validator->errors(), []);
        }
        $user->update([
            'full_name' => $request['full_name'],
            'phone_number' => $request['phone_number'],
            'password' => bcrypt($request['password']),
            'address' => $request['address'],
            'salary' => $request['salary'],
        ]);
        $user->permissions()->sync($request['permission_id']);
        return $this->send_response(200, 'تم التعديل على معلومات الموضف', [], $user);
    }

    public function deleteUser(Request $request)
    {
        $request = $request->json()->all();
        $validator = Validator::make($request, [
            'user_id'       => 'required|exists:users,id',
        ], [
            'user_id.required'       => 'يجب ادخال المتسخدم المراد التعديل على معلوماته',
            'user_id.exists'         => 'المستخدم الذي قمت بأدخالة غير متوفر',
        ]);
        if ($validator->fails()) {
            return $this->send_response(401, 'خطأ بالمدخلات', $validator->errors(), []);
        }
        User::find($request['user_id'])->delete();
        return $this->send_response(200, 'تم حذف المستخدم', [], []);
    }
}