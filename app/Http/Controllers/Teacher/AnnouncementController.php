<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

class AnnouncementController extends Controller
{
    public function index()
    {
        $teacher = auth()->guard('admin')->user();
        
        // Fetch announcements with assignments
        $announcements = \App\Models\Announcement::where('teacher_id', $teacher->id)
            ->with('assignments')
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Get sections for the dropdown
        $teacherProfile = DB::table('teacher_profile')->where('user_id', $teacher->id)->first();
        
        $sections = [];
        if ($teacherProfile) {
            $sections = DB::table('teacher_sections')
                ->where('teacher_id', $teacherProfile->id)
                ->pluck('section');
        }
            
        return view('admin.teacher.announcements.index', compact('announcements', 'sections'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'priority' => 'required|in:high,medium,low',
            'assign_to' => 'required|string', // 'all', 'section', 'student'
            'section' => 'required_if:assign_to,section',
        ]);

        $teacher = auth()->guard('admin')->user();
        $teacherProfile = DB::table('teacher_profile')->where('user_id', $teacher->id)->first();

        $announcement = \App\Models\Announcement::create([
            'teacher_id' => $teacher->id,
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'priority' => $request->input('priority'),
        ]);

        $assignmentDetails = [];
        if ($request->input('assign_to') === 'section') {
            \App\Models\AnnouncementAssignment::create([
                'announcement_id' => $announcement->id,
                'section' => $request->input('section'),
            ]);
            $assignmentDetails['assigned_to'] = 'section';
            $assignmentDetails['section'] = $request->input('section');
        } else {
            $assignmentDetails['assigned_to'] = 'all';
        }
        
        // Log notification
        if ($teacherProfile) {
            $this->logNotification(
                $teacherProfile->id,
                'announcement_created',
                'Created new announcement: ' . $request->input('title'),
                json_encode([
                    'announcement_id' => $announcement->id,
                    'title' => $request->input('title'),
                    'priority' => $request->input('priority'),
                    'assignment' => $assignmentDetails
                ]),
                $request
            );
        }

        return redirect()->route('teacher.announcements.index')
            ->with('success', 'Announcement created successfully!');
    }

    public function destroy($id, Request $request)
    {
        $announcement = \App\Models\Announcement::findOrFail($id);
        
        // Ensure teacher owns this announcement
        if ($announcement->teacher_id !== auth()->guard('admin')->user()->id) {
            abort(403);
        }

        $teacher = auth()->guard('admin')->user();
        $teacherProfile = DB::table('teacher_profile')->where('user_id', $teacher->id)->first();
        
        // Store announcement details before deletion
        $announcementTitle = $announcement->title;
        $announcementId = $announcement->id;
        
        $announcement->delete();
        
        // Log notification
        if ($teacherProfile) {
            $this->logNotification(
                $teacherProfile->id,
                'announcement_deleted',
                'Deleted announcement: ' . $announcementTitle,
                json_encode([
                    'announcement_id' => $announcementId,
                    'title' => $announcementTitle,
                    'deleted_at' => now()->toDateTimeString()
                ]),
                $request
            );
        }

        return redirect()->route('teacher.announcements.index')
            ->with('success', 'Announcement deleted successfully!');
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
}
