<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\StudentProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Show the profile edit page
     */
    public function edit(): View
    {
        $user = Auth::guard('student')->user();
        $profile = $user->studentProfile;

        // Pass student data for gender-based avatar filtering
        $student = (object) [
            'id' => $user->id,
            'gender' => $profile->gender ?? null,
            'firstname' => $profile->firstname ?? 'Student',
            'lastname' => $profile->lastname ?? 'User',
            'avatar_url' => $profile->avatar_url ?? null
        ];

        // Set avatar URL for the main display
        $userAvatarUrl = $profile && $profile->avatar_url
            ? asset($profile->avatar_url)
            : asset('images/profile/avatar5.png');

        return view('student.profile.edit', compact('profile', 'student', 'userAvatarUrl'));
    }
    
    /**
     * Update the user's avatar
     */
    public function updateAvatar(Request $request): JsonResponse
    {
        $request->validate([
            'avatar' => 'required|string|in:avatar1.png,avatar2.png,avatar3.png,avatar4.png,avatar5.png,avatar6.png'
        ]);
        
        $user = Auth::guard('student')->user();
        $profile = $user->studentProfile;
        
        if (!$profile) {
            // Create profile if it doesn't exist
            $profile = StudentProfile::create([
                'user_id' => $user->id,
                'firstname' => 'Student',
                'lastname' => 'User',
                'avatar_url' => 'images/profile/' . $request->avatar,
            ]);
        } else {
            // Update existing profile
            $profile->update([
                'avatar_url' => 'images/profile/' . $request->avatar
            ]);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Avatar updated successfully!',
            'avatar_url' => asset('images/profile/' . $request->avatar)
        ]);
    }
    
    /**
     * Get the current user's avatar URL
     */
    public function getCurrentAvatar(): JsonResponse
    {
        $user = Auth::guard('student')->user();
        $profile = $user->studentProfile;
        
        $avatarUrl = $profile && $profile->avatar_url 
            ? asset($profile->avatar_url)
            : asset('images/profile/avatar5.png'); // Default avatar
            
        return response()->json([
            'avatar_url' => $avatarUrl
        ]);
    }

    /**
     * Update the user's password
     */
    public function updatePassword(Request $request)
    {
        // Validate the request
        $request->validate([
            'old_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'confirmed', Password::min(8)],
        ], [
            'old_password.required' => 'Please enter your current password.',
            'new_password.required' => 'Please enter a new password.',
            'new_password.confirmed' => 'The new password confirmation does not match.',
            'new_password.min' => 'The new password must be at least 8 characters.',
        ]);

        $user = Auth::guard('student')->user();

        // Check if the old password is correct
        if (!Hash::check($request->old_password, $user->password)) {
            return back()->withErrors([
                'old_password' => 'The current password is incorrect.'
            ])->withInput();
        }

        // Check if new password is different from old password
        if (Hash::check($request->new_password, $user->password)) {
            return back()->withErrors([
                'new_password' => 'The new password must be different from your current password.'
            ])->withInput();
        }

        // Update the password
        $user->password = Hash::make($request->new_password);
        $user->save();

        // Return with success message
        return back()->with('success', 'Password changed successfully!');
    }

    /**
     * Logout the student
     */
    public function logout(Request $request)
    {
        Auth::guard('student')->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('student.login')->with('success', 'You have been logged out successfully.');
    }
}