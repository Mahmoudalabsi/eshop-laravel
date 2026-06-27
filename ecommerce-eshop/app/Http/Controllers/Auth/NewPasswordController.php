<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\ApiService;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

class NewPasswordController extends Controller
{
    protected $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    public function store(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Call the dashboard API to reset password
        $response = $this->api->post('/reset-password', [
            'token' => $request->token,
            'email' => $request->email,
            'password' => $request->password,
            'password_confirmation' => $request->password_confirmation,
        ]);

        if ($response->get('status') === 'success') {
            return redirect()->route('login')->with('status', 'تم إعادة تعيين كلمة المرور بنجاح. يمكنك الآن تسجيل الدخول');
        }

        return back()->withErrors([
            'email' => $response->get('message', 'حدث خطأ أثناء إعادة تعيين كلمة المرور'),
        ]);
    }
}
