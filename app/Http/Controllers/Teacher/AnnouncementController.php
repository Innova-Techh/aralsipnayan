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

        $announcement = \App\Models\Announcement::create([
            'teacher_id' => $teacher->id,
            'title' => $request->title,
            'content' => $request->content,
            'priority' => $request->priority,
        ]);

        if ($request->assign_to === 'section') {
            \App\Models\AnnouncementAssignment::create([
                'announcement_id' => $announcement->id,
                'section' => $request->section,
            ]);
        } 
        // Logic for individual students can be added here if needed, 
        // for now focusing on section-based as per requirements implied by "sections" usage

        return redirect()->route('teacher.announcements.index')
            ->with('success', 'Announcement created successfully!');
    }

    public function destroy($id)
    {
        $announcement = \App\Models\Announcement::findOrFail($id);
        
        // Ensure teacher owns this announcement
        if ($announcement->teacher_id !== auth()->guard('admin')->user()->id) {
            abort(403);
        }

        $announcement->delete();

        return redirect()->route('teacher.announcements.index')
            ->with('success', 'Announcement deleted successfully!');
    }
}
