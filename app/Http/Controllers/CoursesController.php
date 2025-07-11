<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class CoursesController extends Controller
{
    /**
     * Display a listing of the courses.
     */
    public function index(): View
    {
        $courses = [
            [
                'id' => 1,
                'title' => 'Fractions & Decimals',
                'description' => 'Master fractions, decimals, and their operations with visual learning tools',
                'level' => 'Beginner',
                'duration' => '4 weeks',
                'lessons' => 24,
                'students' => 1250,
                'rating' => 4.8,
                'progress' => 65,
                'color' => 'from-blue-500 to-blue-600',
                'icon_path' => 'M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z',
                'main_icon_path' => 'M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z',
                'mascot' => 'owl',
                'topics' => ['Basic Fractions', 'Decimal Conversion', 'Operations', 'Word Problems'],
            ],
            [
                'id' => 2,
                'title' => 'Geometry & Shapes',
                'description' => 'Explore 2D and 3D shapes, angles, area, and perimeter calculations',
                'level' => 'Intermediate',
                'duration' => '5 weeks',
                'lessons' => 30,
                'students' => 980,
                'rating' => 4.9,
                'progress' => 40,
                'color' => 'from-green-500 to-green-600',
                'icon_path' => 'M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z',
                'main_icon_path' => 'M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z',
                'mascot' => 'robot',
                'topics' => ['2D Shapes', '3D Solids', 'Area & Perimeter', 'Angles'],
            ],
            [
                'id' => 3,
                'title' => 'Ratios & Proportions',
                'description' => 'Understand ratios, proportions, and their real-world applications',
                'level' => 'Intermediate',
                'duration' => '3 weeks',
                'lessons' => 18,
                'students' => 756,
                'rating' => 4.7,
                'progress' => 80,
                'color' => 'from-purple-500 to-purple-600',
                'icon_path' => 'M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z',
                'main_icon_path' => 'M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z',
                'mascot' => 'cat',
                'topics' => ['Basic Ratios', 'Proportions', 'Scale Factors', 'Applications'],
            ],
            [
                'id' => 4,
                'title' => 'Integers & Operations',
                'description' => 'Work with positive and negative numbers and their operations',
                'level' => 'Advanced',
                'duration' => '4 weeks',
                'lessons' => 22,
                'students' => 634,
                'rating' => 4.6,
                'progress' => 25,
                'color' => 'from-red-500 to-red-600',
                'icon_path' => 'M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z',
                'main_icon_path' => 'M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z',
                'mascot' => 'robot',
                'topics' => ['Number Line', 'Addition & Subtraction', 'Multiplication', 'Division'],
            ],
            [
                'id' => 5,
                'title' => 'Data & Statistics',
                'description' => 'Collect, organize, and interpret data using graphs and charts',
                'level' => 'Beginner',
                'duration' => '3 weeks',
                'lessons' => 16,
                'students' => 892,
                'rating' => 4.8,
                'progress' => 0,
                'color' => 'from-orange-500 to-orange-600',
                'icon_path' => 'M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z',
                'main_icon_path' => 'M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z',
                'mascot' => 'owl',
                'topics' => ['Data Collection', 'Bar Graphs', 'Line Graphs', 'Mean & Median'],
            ],
            [
                'id' => 6,
                'title' => 'Measurement & Units',
                'description' => 'Learn about length, weight, volume, and unit conversions',
                'level' => 'Beginner',
                'duration' => '3 weeks',
                'lessons' => 20,
                'students' => 567,
                'rating' => 4.5,
                'progress' => 0,
                'color' => 'from-teal-500 to-teal-600',
                'icon_path' => 'M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z',
                'main_icon_path' => 'M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z',
                'mascot' => 'cat',
                'topics' => ['Length', 'Weight', 'Volume', 'Conversions'],
            ],
        ];

        return view('user.user_courses', compact('courses'));
    }
    public function show(string $id): View
    {
        return view('user.course_detail', compact('id'));
    }
}