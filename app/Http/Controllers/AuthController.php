<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    /**
     * Show the student login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle student login
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

        // delay to test the loader (remove in production)
        sleep(1);

        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            $user = Auth::user();

            // Only handle Student role - redirect others to appropriate login
            if ($user->role === 'Student') {
                return $this->handleStudentLogin($user);
            } else {
                // Non-student users should use admin login
                Auth::logout();
                return back()->withErrors([
                    'username' => 'Please use the admin login for teacher/admin accounts.',
                ])->withInput($request->only('username'));
            }
        }

        return back()->withErrors([
            'username' => 'Invalid credentials.',
        ])->withInput($request->only('username'));
    }

    /**
     * Handle student login with onboarding flow
     */
    private function handleStudentLogin($user)
    {
        $profile = $user->studentProfile;
        
        if (!$profile) {
            // This shouldn't happen with proper teacher setup, but just in case
            return redirect()->route('login')->withErrors(['error' => 'Student profile not found.']);
        }

        // Update last login and first login tracking
        $updates = ['last_login_date' => now()];
        
        if ($profile->is_first_login) {
            $updates['is_first_login'] = false;
        }
        
        $user->update($updates);

        // Check onboarding status
        if (!$profile->has_completed_onboarding) {
            // First time login - go to welcome page
            return redirect()->route('student.onboarding.welcome')
                ->with('success', "Welcome to AralSipnayan, {$profile->firstname}!");
        }

        // Regular login - go to dashboard
        return redirect()->route('student.dashboard')
        ->with('success', "Welcome back, {$profile->firstname}!");
    }


    /**
     * Handle student logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}