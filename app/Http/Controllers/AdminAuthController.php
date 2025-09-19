<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    /**
     * Show the admin/teacher login form
     */
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    /**
     * Handle admin/teacher login
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
            'captcha' => 'required|captcha',
        ], [
            'captcha.captcha' => 'Invalid Captcha',
        ]);

        // Allow login with either username or email
        $loginField = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        
        if (Auth::guard('admin')->attempt([$loginField => $request->username, 'password' => $request->password])) {
            $request->session()->regenerate();

            // Only allow Admin and Teacher roles
            if (in_array(Auth::guard('admin')->user()->role, ['Admin', 'Teacher'])) {
                switch (Auth::guard('admin')->user()->role) {
                    case 'Admin':
                        return redirect()->route('admin.dashboard');
                    case 'Teacher':
                        return redirect()->route('teacher.dashboard');
                }
            } else {
                Auth::guard('admin')->logout();
                return back()->withErrors([
                    'username' => 'Access denied. Admin/Teacher accounts only.',
                ])->withInput($request->except('password', 'captcha'));
            }
        }

        return back()->withErrors([
            'username' => 'Invalid credentials.',
        ])->withInput($request->except('password', 'captcha'));
    }

    /**
     * Handle admin/teacher logout
     */
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
