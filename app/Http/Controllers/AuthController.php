<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            $user = Auth::user();

            // Handle different user roles
            switch ($user->role) {
                case 'Admin':
                    return redirect()->route('admin.dashboard');
                case 'Teacher':
                    return redirect()->route('teacher.dashboard');
                case 'Student':
                    return $this->handleStudentLogin($user);
                default:
                    return redirect('/');
            }
        }

        return back()->withErrors([
            'username' => 'Invalid credentials.',
        ]);
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

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}