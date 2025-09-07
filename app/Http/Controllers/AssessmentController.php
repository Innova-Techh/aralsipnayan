<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssessmentController extends Controller
{
    /**
     * Display the assessments page
     */
    public function index()
    {
        $userId = Auth::id();
        
        return view('student.assessments');
    }

    /**
     * Display assessment list for a specific category
     */
    public function showCategory($category)
    {
        $categoryData = [
            'Number_Algebra' => [
                'id' => 'number_algebra',
                'title' => 'Numbers and Algebra',
                'description' => 'Master number theory, operations, and algebraic reasoning',
                'icon' => '🔢',
                'color' => 'from-blue-500 to-blue-600'
            ],
            'Measurement_Geometry' => [
                'id' => 'measurement_geometry',
                'title' => 'Measurement and Geometry', 
                'description' => 'Test your knowledge of shapes, angles, and spatial relationships',
                'icon' => '📐',
                'color' => 'from-green-500 to-green-600'
            ],
            'Data_Probability' => [
                'id' => 'data_probability',
                'title' => 'Data and Probability',
                'description' => 'Explore data tables, graphs, and probability concepts',
                'icon' => '📊',
                'color' => 'from-purple-500 to-purple-600'
            ]
        ];

        if (!array_key_exists($category, $categoryData)) {
            abort(404);
        }

        // You can add logic here to fetch actual assessments from database
        // $assessments = Assessment::where('category', $category)->get();

        return view('student.assessment-list', [
            'category' => $category,
            'data' => $categoryData[$category]
        ]);
    }

    
}