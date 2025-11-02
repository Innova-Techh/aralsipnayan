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
           // 'captcha' => 'required|captcha',
        ], [
            //'captcha.captcha' => 'Invalid Captcha',
        ]);

        // delay to test the loader (remove in production)
        sleep(1);

        // Try authenticating against admin/teacher first (supports username or email)
        $loginField = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        if (Auth::guard('admin')->attempt([$loginField => $request->username, 'password' => $request->password])) {
            $request->session()->regenerate();

            $adminUser = Auth::guard('admin')->user();
            // Block inactive admin/teacher accounts
            if (isset($adminUser->status) && strtolower($adminUser->status) !== 'active') {
                Auth::guard('admin')->logout();
                return back()->withErrors([
                    'username' => 'This account is inactive. Please contact support.',
                ])->withInput($request->only('username'));
            }
            if (in_array($adminUser->role, ['Admin', 'Teacher'])) {
                if ($adminUser->role === 'Admin') {
                    return redirect()->route('admin.dashboard');
                }
                if ($adminUser->role === 'Teacher') {
                    return redirect()->route('teacher.dashboard');
                }
            }

            // Unexpected role in admin guard, logout and continue
            Auth::guard('admin')->logout();
        }

        // Fallback: try authenticating as student (supports username or email)
        $studentLoginField = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $studentCredentials = [$studentLoginField => $request->username, 'password' => $request->password];
        if (Auth::guard('student')->attempt($studentCredentials)) {
            $request->session()->regenerate();

            $user = Auth::guard('student')->user();
            // Block inactive or archived student accounts
            if (isset($user->status) && strtolower($user->status) !== 'active') {

                // Determine message based on status
                $status = strtolower($user->status);

                if ($status === 'archive') {
                    $message = 'This account has been archived. Please contact the administrator.';
                } elseif ($status === 'inactive') {
                    $message = 'This account is inactive. Please contact admin or your teacher.';
                } else {
                    // Catch-all for other non-active statuses
                    $message = 'This account is not active. Please contact the administrator.';
                }

                Auth::guard('student')->logout();

                return back()->withErrors([
                    'username' => $message,
                ])->withInput($request->only('username'));
            }
            if ($user->role === 'Student') {
                return $this->handleStudentLogin($user);
            }

            // Not a student role on student guard; logout and error
            Auth::guard('student')->logout();
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
        // Log out of both guards to ensure full sign-out from unified login
        if (Auth::guard('student')->check()) {
            Auth::guard('student')->logout();
        }
        if (Auth::guard('admin')->check()) {
            Auth::guard('admin')->logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}