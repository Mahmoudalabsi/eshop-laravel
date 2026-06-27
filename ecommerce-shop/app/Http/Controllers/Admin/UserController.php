<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules; // هذا هو السطر الأهم

class UserController extends Controller
{
    // 1. تفتح صفحة Blade الفارغة
    public function index()
    {
        return view('eshop.dashboard.users.index');
    }

    public function updateRole($id)
    {
        $user = User::findOrFail($id);

        // حماية: لا تسمح للمدير بتغيير رتبة نفسه
        if (auth()->id() == $user->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'لا يمكنك تغيير رتبة حسابك الحالي!'
            ], 403);
        }

        // تبديل الرتبة
        $user->role = ($user->role == 'admin') ? 'user' : 'admin';
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'تم تحديث الرتبة بنجاح'
        ]);
    }


    public function getUsersJson()
    {
        try {
            $users = User::all();
            return response()->json([
                'status' => 'success',
                'data' => $users
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        // يفضل دائماً التحقق من البيانات قبل الإدخال
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|in:admin,customer',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => 1,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'تم إضافة المستخدم بنجاح',
            'data' => $user
        ]);
    }
    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return response()->json(['status' => 'success']);
    }
    public function updateStatus(Request $request, $id)
    {
        try {
            // 1. العثور على المستخدم
            $user = User::findOrFail($id);

            // 2. حماية: التأكد من أن التعديل يتم فقط للأدمن
            if ($user->role !== 'admin') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'هذه الخاصية متاحة فقط لحسابات الإدارة.'
                ], 403);
            }

            // 3. حماية: منع تعطيل الحساب الأساسي (ID = 1)
            if ($user->id == 1) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'لا يمكن تعطيل حساب المدير العام للنظام.'
                ], 403);
            }

            // 4. تحديث الحالة
            $user->status = $request->status; // سيستلم 1 أو 0 من الجافا سكريبت
            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'تم تحديث حالة الحساب بنجاح'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ أثناء التحديث'
            ], 500);
        }
    }

}
