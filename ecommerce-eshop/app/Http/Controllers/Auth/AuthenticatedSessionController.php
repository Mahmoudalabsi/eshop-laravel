<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\ApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AuthenticatedSessionController extends Controller
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
            'password' => ['required'],
        ]);

        // Call the dashboard API to authenticate
        $response = $this->api->post('/login', [
            'email' => $request->email,
            'password' => $request->password,
            'device_name' => 'web_shop',
        ]);

        if ($response->get('status') === 'success') {
            $userData = $response->get('data');
            $apiUser = $userData['user'];
            
            // Sync with local User model to support local Auth features
            $user = \App\Models\User::updateOrCreate(
                ['email' => $apiUser['email']],
                [
                    'name' => $apiUser['name'],
                    'password' => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(24)), // Dummy password
                    'role' => $apiUser['role'] ?? 'user',
                ]
            );

            // Login locally
            Auth::login($user, $request->has('remember'));
            
            // Store token and user data in session
            Session::put('api_token', $userData['access_token']);
            Session::put('user', $apiUser); 
            
            return redirect()->intended(route('home'))->with('success', 'تم تسجيل الدخول بنجاح');
        }

        return back()->withErrors([
            'email' => $response->get('message', 'بيانات الدخول غير صحيحة'),
        ]);
    }

    public function destroy(Request $request)
    {
        // Call logout API if user has token
        if (Session::has('api_token')) {
            $this->api->post('/logout');
        }

        Auth::logout();
        Session::forget(['api_token', 'user']);
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('home')->with('success', 'تم تسجيل الخروج بنجاح');
    }
}
