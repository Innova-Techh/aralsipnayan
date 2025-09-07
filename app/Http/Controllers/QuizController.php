<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function start($category)
{
    // Placeholder data
    $quizData = [
        'Number_Algebra' => [
            'title' => 'Numbers and Algebra Quiz',
            'totalQuestions' => 10,
            'timeLimit' => 30
        ],
        'Measurement_Geometry' => [
            'title' => 'Geometry Quiz', 
            'totalQuestions' => 8,
            'timeLimit' => 25
        ],
        'Data_Probability' => [
            'title' => 'Data and Probability Quiz',
            'totalQuestions' => 12,
            'timeLimit' => 35
        ]
    ];

    if (!array_key_exists($category, $quizData)) {
        abort(404);
    }

    return view('student.quiz', [
        'category' => $category,
        'quiz' => $quizData[$category],
        'currentQuestion' => 1,
        'totalQuestions' => $quizData[$category]['totalQuestions']
    ]);
}
}
