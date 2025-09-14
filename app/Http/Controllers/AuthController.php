<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // delay to test the loader (remove in production)
        sleep(1);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Redirect based on role
            switch (Auth::user()->role) {
                case 'Admin':
                    return redirect()->route('admin.dashboard');
                case 'Teacher':
                    return redirect()->route('teacher.dashboard');
                case 'Student':
                    return redirect()->route('student.dashboard');
                default:
                    return redirect('/');
            }
        }

        return back()->withErrors([
            'username' => 'Invalid credentials.',
        ])->withInput($request->only('username'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}