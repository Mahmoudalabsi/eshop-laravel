<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AuthenticatedSessionController extends Controller
{
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            // Block inactive users
            if (method_exists($user, 'isActive') && !$user->isActive()) {
                Auth::logout();
                return back()->withErrors(['email' => 'حسابك غير مفعّل. يرجى التواصل مع الإدارة.']);
            }

            $request->session()->regenerate();
            Session::put('user', [
                'id'            => $user->id,
                'name'          => $user->name,
                'email'         => $user->email,
                'role'          => $user->role,
                'profile_image' => $user->profile_image ?? null,
            ]);

            // Redirect admin to admin dashboard if they came from there
            if ($user->role === 'admin') {
                return redirect()->route('home')->with('success', 'مرحباً بك مدير النظام');
            }

            return redirect()->intended(route('home'))->with('success', 'تم تسجيل الدخول بنجاح');
        }

        return back()->withErrors([
            'email' => 'بيانات الدخول غير صحيحة',
        ])->onlyInput('email');
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        Session::forget(['api_token', 'user']);

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'تم تسجيل الخروج بنجاح');
    }
}
