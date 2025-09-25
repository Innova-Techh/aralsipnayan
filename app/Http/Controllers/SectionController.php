<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class SectionController extends Controller
{
    /**
     * Show student sections with assigned assessments
     */
    public function index(): View
    {
        $user = Auth::guard('student')->user();
        
        // Get student's section from student_profile
        $studentProfile = \Illuminate\Support\Facades\DB::table('student_profile')->where('user_id', $user->id)->first();
        $studentSection = $studentProfile ? $studentProfile->section : null;
        
        // Get assessments assigned to this student
        $assignments = \App\Models\AssessmentAssignment::where(function($query) use ($user, $studentSection) {
            $query->where('student_id', $user->id)
                  ->orWhere(function($q) use ($studentSection) {
                      // Handle both "A" and "Section A" formats
                      $q->where('section', $studentSection)
                        ->orWhere('section', 'Section ' . $studentSection)
                        ->whereNull('student_id');
                  });
        })
        ->with(['assessment' => function($query) {
            $query->where('status', 'Active');
        }])
        ->whereHas('assessment', function($query) {
            $query->where('status', 'Active');
        })
        ->get();

        // Convert assignments to sections format for the view
        $sections = $assignments->map(function($assignment) {
            if (!$assignment->assessment) return null;
            
            $assessment = $assignment->assessment;
            $colors = [
                'Number & Algebra' => 'from-blue-400 to-purple-600',
                'Measurement & Geometry' => 'from-green-400 to-teal-600', 
                'Data & Probability' => 'from-purple-400 to-indigo-600'
            ];
            
            return [
                'id' => $assessment->id,
                'title' => $assessment->title,
                'description' => $assessment->description ?: 'Complete this assessment to earn XP',
                'xp_reward' => 150, // Default XP reward
                'time_limit' => $assessment->time_limit . ' mins',
                'difficulty' => $assessment->difficulty,
                'color' => $colors[$assessment->category] ?? 'from-gray-400 to-gray-600',
                'is_live_quiz' => $assessment->is_live_quiz,
                'assignment_id' => $assignment->id,
                'status' => $assignment->status
            ];
        })->filter()->values()->toArray();


        return view('student.sections', compact('sections'));
    }


    /**
     * Get sections data for API calls
     */
    public function getSectionsData()
    {
        $user = Auth::guard('student')->user();
        
        // Sample data - replace with actual database queries
        return response()->json([
            'total_sections' => 3,
            'completed_sections' => 1,
            'in_progress_sections' => 2,
            'total_lessons' => 37,
            'completed_lessons' => 16,
            'overall_progress' => 43
        ]);
    }
}
