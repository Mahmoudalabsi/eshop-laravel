<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required|string', // لتحديد الجهاز المسجل منه
        ]);

        $user = User::where('email', $request->email)->first();

        // 1. التحقق من المستخدم والحالة (Status)
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'بيانات الدخول غير صحيحة'], 401);
        }

        if ($user->status == 0) {
            return response()->json(['message' => 'هذا الحساب معطل حالياً، يرجى التواصل مع الإدارة'], 403);
        }

        $user->tokens()->where('name', $request->device_name)->delete();

        $tokenResult = $user->createToken($request->device_name, ['server:update']);
        $token = $tokenResult->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'تم تسجيل الدخول بنجاح',
            'data' => [
                'access_token' => $token,
                'token_type' => 'Bearer',
                'expires_at' => null,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'profile_image' => $user->profile_image ? url($user->profile_image) : null,
                    'created_at' => $user->created_at,
                ]
            ]
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'تم تسجيل الخروج']);
    }
}
