<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    public function showForgotForm()
    {
        return view('auth.forgot-pass');
    }

    public function sendResetLink(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        Password::broker('users')->sendResetLink([
            'email' => $validated['email'],
        ]);

        // Avoid leaking whether an email exists.
        return back()
            ->withInput($request->only('email'))
            ->with('status', 'If an account exists for that email, a password reset link has been sent.');
    }

    public function showResetForm(Request $request, string $token)
    {
        $email = (string) $request->query('email', '');

        if ($email === '') {
            return redirect()
                ->route('password.request')
                ->withErrors(['email' => 'The password reset link is missing the email address. Please request a new link.']);
        }

        return view('auth.reset-pass', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $status = Password::broker('users')->reset(
            [
                'email' => $validated['email'],
                'password' => $validated['password'],
                'password_confirmation' => $request->input('password_confirmation'),
                'token' => $validated['token'],
            ],
            function ($user, string $password) {
                $user->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => __($status)]);
        }

        return redirect()
            ->route('login')
            ->with('success', 'Your password has been reset. You can now log in.');
    }
}
