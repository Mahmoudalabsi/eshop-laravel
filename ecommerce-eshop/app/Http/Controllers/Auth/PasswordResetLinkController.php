<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\ApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class PasswordResetLinkController extends Controller
{
    protected $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Call the dashboard API to send password reset link
        $response = $this->api->post('/forgot-password', [
            'email' => $request->email,
        ]);

        if ($response->get('status') === 'success') {
            return back()->with('status', 'تم إرسال رابط إعادة تعيين كلمة المرور إلى بريدك الإلكتروني');
        }

        return back()->withErrors([
            'email' => $response->get('message', 'لم نتمكن من إيجاد حساب بهذا البريد الإلكتروني'),
        ]);
    }
}
