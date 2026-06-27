<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->role === 'admin') {
            return $next($request);
        }

        // إذا لم يكن مسؤولاً، وجهه للصفحة الرئيسية مع رسالة خطأ
        return redirect('/')->with('error', 'ليس لديك صلاحية الدخول لهذه الصفحة');
    }
}
