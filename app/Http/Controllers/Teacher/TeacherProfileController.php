<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\TeacherProfile;

class TeacherProfileController extends Controller
{
    /**
     * Display the teacher profile page
     */
    public function index()
    {
        $teacher = Auth::guard('admin')->user();
        $profile = $teacher->teacherProfile;
        
        if (!$profile) {
            abort(404, 'Teacher profile not found');
        }

        // Get teaching statistics
        $stats = $this->getTeachingStatistics($profile->id);

        return view('admin.teacher.profile.index', compact('profile', 'stats'));
    }

    /**
     * Update teacher profile information
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'firstname' => 'required|string|max:100',
            'lastname' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . Auth::guard('admin')->id(),
            'school_name' => 'nullable|string|max:150',
        ]);

        try {
            DB::beginTransaction();

            $teacher = Auth::guard('admin')->user();
            $profile = $teacher->teacherProfile;

            // Update user email
            $teacher->update([
                'email' => $request->email,
            ]);

            // Update teacher profile
            $profile->update([
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'school_name' => $request->school_name ?? $profile->school_name,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update teacher password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|different:current_password',
            'confirm_password' => 'required|same:new_password',
        ]);

        try {
            $teacher = Auth::guard('admin')->user();

            // Verify current password
            if (!Hash::check($request->current_password, $teacher->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect'
                ], 422);
            }

            // Update password
            $teacher->update([
                'password' => Hash::make($request->new_password)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update password: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update teacher profile photo
     */
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:1024', // 1MB max
        ]);

        try {
            $teacher = Auth::guard('admin')->user();
            $profile = $teacher->teacherProfile;

            if ($request->hasFile('photo')) {
                // Delete old photo if exists
                if ($profile->profile_url && Storage::disk('public')->exists($profile->profile_url)) {
                    Storage::disk('public')->delete($profile->profile_url);
                }

                // Store new photo
                $path = $request->file('photo')->store('profiles/teachers', 'public');

                // Update profile
                $profile->update([
                    'profile_url' => $path
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Profile photo updated successfully!',
                    'photo_url' => asset('storage/' . $path)
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No photo uploaded'
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update photo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get teaching statistics for the teacher
     */
    private function getTeachingStatistics($teacherProfileId)
    {
        // Get the user_id from teacher_profile
        $userId = DB::table('teacher_profile')
            ->where('id', $teacherProfileId)
            ->value('user_id');

        // Get total students
        $totalStudents = DB::table('teacher_sections')
            ->join('student_profile', 'teacher_sections.section', '=', 'student_profile.section')
            ->join('users', 'student_profile.user_id', '=', 'users.id')
            ->where('teacher_sections.teacher_id', $teacherProfileId)
            ->where('users.status', 'active')
            ->distinct('student_profile.user_id')
            ->count('student_profile.user_id');

        // Get total assessments created (using created_by which references users.id)
        $totalAssessments = DB::table('teacher_assessments')
            ->where('created_by', $userId)
            ->count();

        // Get active sections
        $activeSections = DB::table('teacher_sections')
            ->where('teacher_id', $teacherProfileId)
            ->distinct('section')
            ->count('section');

        return [
            'total_students' => $totalStudents,
            'total_assessments' => $totalAssessments,
            'active_sections' => $activeSections,
        ];
    }
}
