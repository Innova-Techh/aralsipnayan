<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class SectionController extends Controller
{
    /**
     * Sample Data not being used.
     */
    public function index(): View
    {
        $user = Auth::user();
        $sections = [
            [
                'id' => 1,
                'title' => 'Geometry Fundamentals',
                'description' => 'Understand shapes, angles, geometric properties',
                'xp_reward' => 150,
                'time_limit' => '25 mins',
                'difficulty' => 'Medium',
                'color' => 'from-blue-400 to-purple-600'
            ],
            [
                'id' => 2,
                'title' => 'Fractions and Decimals',
                'description' => 'Practice division and converting fractions to decimals',
                'xp_reward' => 150,
                'time_limit' => '26 mins',
                'difficulty' => 'Easy',
                'color' => 'from-green-400 to-teal-600'
            ],
            [
                'id' => 3,
                'title' => 'Number Operations Quiz',
                'description' => 'Quick review and test basic arithmetic operations',
                'xp_reward' => 150,
                'time_limit' => '25 mins',
                'difficulty' => 'Hard',
                'color' => 'from-purple-400 to-indigo-600'
            ]
        ];

        return view('student.sections', compact('sections'));
    }


    /**
     * Get sections data for API calls
     */
    public function getSectionsData()
    {
        $user = Auth::user();
        
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
