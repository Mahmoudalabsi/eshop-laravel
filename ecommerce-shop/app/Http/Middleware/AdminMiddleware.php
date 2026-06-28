<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // 1. التأكد أن المستخدم مسجل دخول وأن رتبته admin
        if (auth()->check() && auth()->user()->role === 'admin') {

            // 2. فحص حالة الحساب: إذا كان معطلاً (status == 0)
            if (auth()->user()->status == 0) {
                auth()->logout(); // تسجيل خروج المستخدم فوراً

                // إبطال مفعول الجلسة لزيادة الأمان
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect('/login')->with('error', 'عذراً، تم إيقاف حسابك من قبل الإدارة.');
            }

            return $next($request);
        }

        // إذا لم يكن مسجلاً أو لم يكن مديراً
        return redirect('/')->with('error', 'عذراً، لا تملك صلاحيات دخول لهذه الصفحة.');
    }
}