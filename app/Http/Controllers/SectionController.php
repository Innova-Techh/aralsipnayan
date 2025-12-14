<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Assessment;
use App\Models\AssessmentAssignment;
class SectionController extends Controller
{
    /**
     * Show student sections with assigned assessments
     */
    public function index(): View
    {
        $user = Auth::guard('student')->user();
        
        // Get student's section from student_profile
        $studentProfile = DB::table('student_profile')->where('user_id', $user->id)->first();
        $studentSection = $studentProfile ? $studentProfile->section : null;
    
        $assignments = AssessmentAssignment::where(function($query) use ($user, $studentSection) {
            $query->where('student_id', $user->id)
                  ->orWhere(function($q) use ($studentSection, $user) {
                      $q->whereNull('student_id')
                        ->where(function($subQ) use ($studentSection) {
                            $subQ->where('section', $studentSection)
                                 ->orWhere('section', 'Section ' . $studentSection);
                        })
                        ->whereNotExists(function($exists) use ($user) {
                            $exists->select(DB::raw(1))
                                   ->from('teacher_assessment_assignments as aa2')
                                   ->whereColumn('aa2.assessment_id', 'teacher_assessment_assignments.assessment_id')
                                   ->where('aa2.student_id', $user->id);
                        });
                  });
        })
        ->with(['assessment' => function($query) {
            $query->where('status', 'Active');
        }])
        ->whereHas('assessment', function($query) {
            $query->where('status', 'Active');
        })
        ->selectRaw('MIN(id) as id, assessment_id, MAX(student_id) as student_id, MAX(section) as section, MAX(status) as status')
        ->groupBy('assessment_id')
        ->get();
    
        // Check status from quiz results
        $completedAssessmentIds = \App\Models\QuizResult::where('student_id', $user->id)
                                    ->pluck('assessment_id')
                                    ->toArray();

        // Convert to sections format for the view
        $sections = $assignments->map(function($assignment) use ($completedAssessmentIds) {
            if (!$assignment->assessment) return null;
            
            $assessment = $assignment->assessment;
            
            // Check if user has already completed this assessment
            $isCompleted = in_array($assessment->id, $completedAssessmentIds);
            
            $colors = [
                'Number & Algebra' => 'from-blue-400 to-purple-600',
                'Measurement & Geometry' => 'from-green-400 to-teal-600', 
                'Data & Probability' => 'from-purple-400 to-indigo-600'
            ];
            
            return [
                'id' => $assessment->id,
                'title' => $assessment->title,
                'description' => $assessment->description ?: 'Complete this assessment to earn XP',
                'xp_reward' => 150,
                'time_limit' => $assessment->time_limit . ' mins',
                'difficulty' => $assessment->difficulty,
                'color' => $colors[$assessment->category] ?? 'from-gray-400 to-gray-600',
                'is_live_quiz' => $assessment->is_live_quiz,
                'assignment_id' => $assignment->id,
                'status' => $isCompleted ? 'Completed' : $assignment->status // Override status if completed in results
            ];
        })->filter()->values()->toArray();

        // Fetch completed assessments for the student
        $completedAssignments = AssessmentAssignment::where(function($query) use ($user, $studentSection) {
            $query->where('student_id', $user->id)
                  ->orWhere(function($q) use ($studentSection, $user) {
                      $q->whereNull('student_id')
                        ->where(function($subQ) use ($studentSection) {
                            $subQ->where('section', $studentSection)
                                 ->orWhere('section', 'Section ' . $studentSection);
                        })
                        ->whereNotExists(function($exists) use ($user) {
                            $exists->select(DB::raw(1))
                                   ->from('teacher_assessment_assignments as aa2')
                                   ->whereColumn('aa2.assessment_id', 'teacher_assessment_assignments.assessment_id')
                                   ->where('aa2.student_id', $user->id);
                        });
                  });
        })
        ->with(['assessment'])
        ->whereHas('assessment', function($query) {
            $query->where('status', 'Completed');
        })
        ->selectRaw('MIN(id) as id, assessment_id, MAX(student_id) as student_id, MAX(section) as section, MAX(status) as status')
        ->groupBy('assessment_id')
        ->get();

        // Convert completed assessments to sections format
        $completedSections = $completedAssignments->map(function($assignment) use ($completedAssessmentIds) {
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
                'description' => $assessment->description ?: 'Review or retake this assessment',
                'xp_reward' => 150,
                'time_limit' => $assessment->time_limit . ' mins',
                'difficulty' => $assessment->difficulty,
                'color' => $colors[$assessment->category] ?? 'from-gray-400 to-gray-600',
                'is_live_quiz' => $assessment->is_live_quiz,
                'assignment_id' => $assignment->id,
                'status' => 'Completed'
            ];
        })->filter()->values()->toArray();
    
        // Fetch announcements for the student
        // Matches assignments for the section or specifically for the student
        $announcements = \App\Models\Announcement::whereHas('assignments', function($query) use ($studentSection) {
            $query->where('section', $studentSection)
                  ->orWhere('section', 'Section ' . $studentSection);
        })
        ->with('creator.teacherProfile')
        ->orderBy('created_at', 'desc')
        ->get();
    
        return view('student.sections', compact('sections', 'completedSections', 'announcements'));
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
