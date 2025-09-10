@extends('layouts.user_layout')

@section('title', 'Welcome')

@section('content')
<div class="min-h-screen bg-white-900 flex items-center justify-center px-6 py-12">
    <div class="max-w-xl w-full bg-white rounded-2xl shadow-2xl p-8 relative overflow-hidden">

        <!-- Background Decorations -->
        <div class="absolute -top-12 -left-12 w-40 h-40 bg-indigo-400 rounded-full opacity-20 blur-2xl"></div>
        <div class="absolute -bottom-12 -right-12 w-40 h-40 bg-pink-400 rounded-full opacity-20 blur-2xl"></div>

        <!-- Welcome Header -->
        <div class="text-center">
            <h1 class="text-4xl font-extrabold text-gray-800">Welcome, {{ $student->firstname }}! 🎉</h1>
            <p class="mt-3 text-gray-600 text-lg">We're excited to have you at <span class="font-semibold text-indigo-600">AralSipnayan</span>!  
                Let's begin your journey toward mastering mathematics.</p>
        </div>

        <!-- Hero Image -->
        <div class="flex justify-center my-6">
            <img src="{{ asset('images/onboarding/welcome.svg') }}" alt="Welcome" class="w-72 drop-shadow-lg">
        </div>

        <!-- Feature Highlights -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-center my-4">
            <div class="p-4 bg-indigo-50 rounded-xl shadow-md">
                <span class="text-indigo-600 text-3xl">📘</span>
                <p class="mt-2 text-sm font-medium text-gray-700">Learn through fun challenges</p>
            </div>
            <div class="p-4 bg-purple-50 rounded-xl shadow-md">
                <span class="text-purple-600 text-3xl">🏆</span>
                <p class="mt-2 text-sm font-medium text-gray-700">Earn points & badges</p>
            </div>
            <div class="p-4 bg-pink-50 rounded-xl shadow-md">
                <span class="text-pink-600 text-3xl">📈</span>
                <p class="mt-2 text-sm font-medium text-gray-700">Track your progress</p>
            </div>
        </div>

        <!-- Start Button -->
        <div class="mt-6 flex justify-center">
            <a href="{{ route('student.onboarding.avatar') }}" 
               class="px-6 py-3 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 text-white font-bold rounded-xl shadow-lg hover:scale-105 hover:shadow-xl transform transition-all duration-300 ease-in-out inline-block">
                🚀 Choose Your Avatar
            </a>
        </div>

        <!-- Skip Option -->
        <div class="mt-4 text-center">
            <a href="{{ route('student.dashboard') }}" class="text-gray-500 hover:text-gray-700 text-sm underline">
                Skip for now → Go to Dashboard
            </a>
        </div>
    </div>
</div>
@endsection