<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\ApiService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    protected $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Call the dashboard API to register
        $response = $this->api->post('/register', [
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'password_confirmation' => $request->password_confirmation,
            'device_name' => 'web_shop',
        ]);

        if ($response->get('status') === 'success') {
            $userData = $response->get('data');
            $apiUser = $userData['user'];
            
            // Sync with local User model to support local Auth features
            $user = User::updateOrCreate(
                ['email' => $apiUser['email']],
                [
                    'name' => $apiUser['name'],
                    'password' => Hash::make($request->password),
                    'role' => $apiUser['role'] ?? 'user',
                ]
            );

            // Login locally
            Auth::login($user);
            
            // Store token and user data in session
            Session::put('api_token', $userData['access_token']);
            Session::put('user', $apiUser); 
            
            return redirect()->route('home')->with('success', 'تم إنشاء الحساب بنجاح');
        }

        return back()->withErrors([
            'email' => $response->get('message', 'حدث خطأ أثناء إنشاء الحساب'),
        ]);
    }
}
