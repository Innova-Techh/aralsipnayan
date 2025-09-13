@extends('layouts.user_layout')

@section('title', 'Quiz - AralSipnayan')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4">
    <div class="bg-white rounded-3xl shadow-xl p-8 max-w-md w-full text-center">
        <!-- Trophy Image -->
        <div class="mb-4">
            <img src="{{ asset('images/assessments/trophy.png') }}" alt="Trophy" class="mx-auto w-24 h-24">
        </div>

        <!-- Header -->
        <h1 class="text-2xl font-bold mb-2">Assessment Complete!</h1>
        <p class="text-gray-600 mb-6">Great job on completing the assessment!</p>

        <!-- Points and Score -->
        <div class="flex justify-around mb-6">
            <div class="bg-green-100 text-green-800 px-6 py-4 rounded-xl font-semibold">
                {{ $pointsEarned ?? 0 }}<br>
                <span class="text-sm font-normal">Points Earned</span>
            </div>
            <div class="bg-gray-100 text-blue-800 px-6 py-4 rounded-xl font-semibold">
                {{ $correctAnswers ?? 0 }}/{{ $totalQuestions ?? 15 }}<br>
                <span class="text-sm font-normal">Score</span>
            </div>
        </div>

        <!-- Buttons -->
        <div class="space-y-3">
            <!-- Review Assessment -->
            <a href="{{ route('student.assessments.review', $category) }}"
               class="w-full inline-block bg-gradient-to-b from-[#F6510C] to-[#F5D70B] text-white text-lg font-semibold py-3 rounded-2xl border-b-4 border-[#922f26] shadow-lg hover:scale-[1.03] transition-all duration-300">
                Review Assessment
            </a>

            <!-- Back to Assessments -->
            <a href="{{ route('student.assessments.category', $category) }}"
               class="block w-full text-white py-3 px-4 rounded-xl font-medium transition-all duration-300 relative z-20 bg-cover bg-center bg-no-repeat hover:brightness-110 hover:bg-[rgba(139,86,204,0.3)] text-center"
               style="background-image: url('{{ asset('images/assessments/btnbg.png') }}');">
                Back to Assessments
            </a>

            <!-- Go to Dashboard -->
            <a href="{{ route('student.dashboard') }}"
               class="block w-full py-3 px-4 rounded-xl border border-gray-300 text-gray-700 font-medium hover:bg-gray-100 transition-all duration-300 text-center">
                Go to Dashboard
            </a>
        </div>
    </div>
</div>
@endsection
