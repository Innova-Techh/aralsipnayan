<?php
// app/Http/Controllers/CoursesController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\ViewModels\LessonViewModel;

class CoursesController extends Controller
{
    public function index()
    {
        // Sample lesson
        $lessons = collect([
            [
                'id' => 1,
                'title' => 'Introduction to Algebra',
                'description' => 'Learn the basics of algebraic expressions and equations',
                'category' => 'algebra',
                'duration' => 45,
                'questions_count' => 10,
                'difficulty_level' => 'beginner',
                'order' => 1,
                'is_active' => true,
                'progressStatus' => 'available',
                'progressPercentage' => 0,
            ],
            [
                'id' => 2,
                'title' => 'Linear Equations',
                'description' => 'Solve linear equations with one variable',
                'category' => 'algebra',
                'duration' => 60,
                'questions_count' => 15,
                'difficulty_level' => 'intermediate',
                'order' => 2,
                'is_active' => true,
                'progressStatus' => 'in-progress',
                'progressPercentage' => 50,
            ],
            [
                'id' => 3,
                'title' => 'Quadratic Equations',
                'description' => 'Master quadratic equations and their solutions',
                'category' => 'algebra',
                'duration' => 75,
                'questions_count' => 20,
                'difficulty_level' => 'advanced',
                'order' => 3,
                'is_active' => true,
                'progressStatus' => 'completed',
                'progressPercentage' => 100,
            ],
            [
                'id' => 4,
                'title' => 'Basic Geometry',
                'description' => 'Introduction to geometric shapes and properties',
                'category' => 'geometry',
                'duration' => 50,
                'questions_count' => 12,
                'difficulty_level' => 'beginner',
                'order' => 4,
                'is_active' => true,
                'progressStatus' => 'available',
                'progressPercentage' => 0,
            ],
            [
                'id' => 5,
                'title' => 'Area and Perimeter',
                'description' => 'Calculate area and perimeter of various shapes',
                'category' => 'geometry',
                'duration' => 55,
                'questions_count' => 18,
                'difficulty_level' => 'intermediate',
                'order' => 5,
                'is_active' => true,
                'progressStatus' => 'available',
                'progressPercentage' => 0,
            ],
            [
                'id' => 6,
                'title' => 'Trigonometry Basics',
                'description' => 'Learn about sine, cosine, and tangent functions',
                'category' => 'trigonometry',
                'duration' => 65,
                'questions_count' => 16,
                'difficulty_level' => 'intermediate',
                'order' => 6,
                'is_active' => true,
                'progressStatus' => 'available',
                'progressPercentage' => 0,
            ],
            [
                'id' => 7,
                'title' => 'Statistics Fundamentals',
                'description' => 'Introduction to data analysis and statistics',
                'category' => 'statistics',
                'duration' => 70,
                'questions_count' => 14,
                'difficulty_level' => 'beginner',
                'order' => 7,
                'is_active' => true,
                'progressStatus' => 'available',
                'progressPercentage' => 0,
            ],
            [
                'id' => 8,
                'title' => 'Calculus Introduction',
                'description' => 'Basic concepts of limits and derivatives',
                'category' => 'calculus',
                'duration' => 80,
                'questions_count' => 22,
                'difficulty_level' => 'advanced',
                'order' => 8,
                'is_active' => true,
                'progressStatus' => 'available',
                'progressPercentage' => 0,
            ]
        ])->map(function($lesson) {
            return (object) $lesson;
        });
        
        return view('user.user_courses', compact('lessons'));
    }
    
    public function show($id)
    {
        // Sample lesson
        $lesson = (object) [
            'id' => $id,
            'title' => 'Sample Lesson',
            'description' => 'This is a sample lesson for frontend display',
            'category' => 'algebra',
            'duration' => 45,
            'questions_count' => 10,
            'video_url' => 'https://example.com/video.mp4',
            'difficulty_level' => 'beginner',
            'order' => 1,
            'is_active' => true
        ];
        
        return view('user.lesson_detail', compact('lesson'));
    }
}