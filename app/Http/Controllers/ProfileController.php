<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $user = Auth::user();
        $profile = $user->studentProfile;
        
        return view('student.profile.edit', compact('profile'));
    }
    
    /**
     * Update the user's avatar
     */
    public function updateAvatar(Request $request): JsonResponse
    {
        $request->validate([
            'avatar' => 'required|string|in:avatar1.png,avatar2.png,avatar3.png,avatar4.png,avatar5.png,avatar6.png'
        ]);
        
        $user = Auth::user();
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
        $user = Auth::user();
        $profile = $user->studentProfile;
        
        $avatarUrl = $profile && $profile->avatar_url 
            ? asset($profile->avatar_url)
            : asset('images/profile/avatar5.png'); // Default avatar
            
        return response()->json([
            'avatar_url' => $avatarUrl
        ]);
    }
}
