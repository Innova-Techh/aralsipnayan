<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\TeacherProfile;
use Cloudinary\Cloudinary;

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

            // Store old values for logging
            $oldValues = [
                'firstname' => $profile->firstname,
                'lastname' => $profile->lastname,
                'email' => $teacher->email,
                'school_name' => $profile->school_name,
            ];

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

            // Log notification
            $this->logNotification(
                $profile->id,
                'profile_update',
                'Updated profile information',
                json_encode([
                    'old' => $oldValues,
                    'new' => [
                        'firstname' => $request->firstname,
                        'lastname' => $request->lastname,
                        'email' => $request->email,
                        'school_name' => $request->school_name ?? $profile->school_name,
                    ]
                ]),
                $request
            );

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
            $profile = $teacher->teacherProfile;

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

            // Log notification
            $this->logNotification(
                $profile->id,
                'password_change',
                'Changed account password',
                json_encode([
                    'timestamp' => now()->toDateTimeString(),
                    'message' => 'Password was successfully changed'
                ]),
                $request
            );

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
                $oldPhotoUrl = $profile->profile_url;

                // Store new photo in Cloudinary
                $upload = $this->getCloudinaryClient()->uploadApi()->upload(
                    $request->file('photo')->getRealPath(),
                    [
                        'folder' => 'aralsipnayan/teacher-profiles',
                        'resource_type' => 'image',
                    ]
                );
                $path = $upload['secure_url'] ?? null;

                // Update profile
                $profile->update([
                    'profile_url' => $path
                ]);

                if ($oldPublicId = $this->extractCloudinaryPublicId($oldPhotoUrl)) {
                    try {
                        $this->getCloudinaryClient()->uploadApi()->destroy($oldPublicId, ['resource_type' => 'image']);
                    } catch (\Throwable $e) {
                        // Keep profile update successful even if old asset cleanup fails.
                    }
                }

                // Log notification
                $this->logNotification(
                    $profile->id,
                    'photo_update',
                    'Updated profile photo',
                    json_encode([
                        'old_photo' => $oldPhotoUrl,
                        'new_photo' => $path,
                        'file_size' => $request->file('photo')->getSize(),
                        'mime_type' => $request->file('photo')->getMimeType()
                    ]),
                    $request
                );

                return response()->json([
                    'success' => true,
                    'message' => 'Profile photo updated successfully!',
                    'photo_url' => $path
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

    /**
     * Get teacher notifications
     */
    public function getNotifications()
    {
        try {
            $teacher = Auth::guard('admin')->user();
            $profile = $teacher->teacherProfile;

            if (!$profile) {
                return response()->json([
                    'success' => false,
                    'message' => 'Teacher profile not found'
                ], 404);
            }

            // Get latest 10 notifications
            $notifications = DB::table('teacher_notifications')
                ->where('teacher_id', $profile->id)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function($notification) {
                    return [
                        'id' => $notification->id,
                        'type' => $notification->type,
                        'action' => $notification->action,
                        'details' => $notification->details,
                        'is_read' => $notification->is_read,
                        'created_at' => $notification->created_at,
                        'time_ago' => \Carbon\Carbon::parse($notification->created_at)->diffForHumans(),
                    ];
                });

            // Get unread count
            $unreadCount = DB::table('teacher_notifications')
                ->where('teacher_id', $profile->id)
                ->where('is_read', false)
                ->count();

            return response()->json([
                'success' => true,
                'notifications' => $notifications,
                'unread_count' => $unreadCount
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch notifications: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Request $request)
    {
        try {
            $teacher = Auth::guard('admin')->user();
            $profile = $teacher->teacherProfile;

            if (!$profile) {
                return response()->json([
                    'success' => false,
                    'message' => 'Teacher profile not found'
                ], 404);
            }

            $notificationId = $request->input('notification_id');

            // Update notification
            $updated = DB::table('teacher_notifications')
                ->where('id', $notificationId)
                ->where('teacher_id', $profile->id)
                ->update([
                    'is_read' => true,
                    'updated_at' => now()
                ]);

            if ($updated) {
                return response()->json([
                    'success' => true,
                    'message' => 'Notification marked as read'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Notification not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark notification as read: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        try {
            $teacher = Auth::guard('admin')->user();
            $profile = $teacher->teacherProfile;

            if (!$profile) {
                return response()->json([
                    'success' => false,
                    'message' => 'Teacher profile not found'
                ], 404);
            }

            DB::table('teacher_notifications')
                ->where('teacher_id', $profile->id)
                ->where('is_read', false)
                ->update([
                    'is_read' => true,
                    'updated_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => 'All notifications marked as read'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark notifications as read: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Log notification to teacher_notifications table
     */
    private function logNotification($teacherProfileId, $type, $action, $details, Request $request)
    {
        try {
            DB::table('teacher_notifications')->insert([
                'teacher_id' => $teacherProfileId,
                'type' => $type,
                'action' => $action,
                'details' => $details,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            // Log error but don't fail the main operation
            \Log::error('Failed to log teacher notification: ' . $e->getMessage());
        }
    }

    private function getCloudinaryClient(): Cloudinary
    {
        $cloudinaryUrl = config('services.cloudinary.url')
            ?: env('CLOUDINARY_URL')
            ?: getenv('CLOUDINARY_URL');

        if (!$cloudinaryUrl) {
            throw new \RuntimeException('Cloudinary is not configured. Set CLOUDINARY_URL in environment variables.');
        }

        return new Cloudinary($cloudinaryUrl);
    }

    private function extractCloudinaryPublicId(?string $url): ?string
    {
        if (!$url || !preg_match('#https?://res\.cloudinary\.com/#', $url)) {
            return null;
        }

        $path = parse_url($url, PHP_URL_PATH);
        if (!$path) {
            return null;
        }

        $segments = explode('/', ltrim($path, '/'));
        $uploadIndex = array_search('upload', $segments, true);
        if ($uploadIndex === false) {
            return null;
        }

        $publicSegments = array_slice($segments, $uploadIndex + 1);
        if (!empty($publicSegments) && preg_match('/^v\d+$/', $publicSegments[0])) {
            array_shift($publicSegments);
        }

        if (empty($publicSegments)) {
            return null;
        }

        $last = array_pop($publicSegments);
        $lastWithoutExt = preg_replace('/\.[^.]+$/', '', $last);
        $publicSegments[] = $lastWithoutExt;

        return implode('/', $publicSegments);
    }
}
