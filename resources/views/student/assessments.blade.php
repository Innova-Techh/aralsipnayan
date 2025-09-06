@extends('layouts.user_layout')

@section('title', 'AralSipnayan')

@section('content')

<div class="space-y-8">
    <!-- My Assessments Header -->
    
<div class="relative overflow-hidden mt-4 lg:mt-8">
    <div class="mx-auto max-w-10xl text-white bg-center bg-no-repeat rounded-2xl flex items-center" 
         style="background-image: url('{{ asset('images/assessments/bg.png') }}'); 
                background-size: 95% clamp(120px, 10vw + 60px, 200px);
                min-height: clamp(120px, 10vw + 60px, 200px);
                padding-left: clamp(2rem, 8vw, 18rem);">
        <div class="relative z-10 pr-6">
            <h1 class="text-[22px] sm:text-base md:text-xl lg:text-3xl leading-tight font-baloo font-extrabold">My Assessments</h1>
            <p class="text-[10px] sm:text-sm md:text-base lg:text-lg text-blue-100 mt-2 sm:mt-3 md:mt-4">
                Test your mathematical knowledge across different competencies and track your learning progress with adaptive assessments
            </p>
        </div>
    </div>
</div>

<!-- Feature Cards Section -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6 mb-6 md:mb-8">
            <!-- Ready for a Challenge Card -->
        <div class="relative overflow-hidden rounded-xl md:rounded-2xl p-4 md:p-6 text-white min-h-[120px] md:min-h-[200px] border-b-4 border-[#2c014b] shadow-lg"
            style="background-image: url('{{ asset('images/assessments/card1.png') }}'); background-size: cover; background-position: center;">
            <div class="relative z-10 w-[70%] md:w-[60%]">
                <div class="flex items-center mb-2 md:mb-4">
                    <h3 class="text-base md:text-xl font-bold">🎯 Ready for a Challenge?</h3>
                </div>
                <p class="text-white mb-2 md:mb-4 text-sm md:text-base">
                    Test your math skills with fun assessments! Choose from geometry, numbers, or fractions and start your learning adventure!
                </p>
            </div>
        </div>


        <!-- Level Up Your Skills Card -->
        <div class="relative overflow-hidden rounded-xl md:rounded-2xl p-4 md:p-6 text-white min-h-[120px] md:min-h-[200px] border-b-4 border-[#922f26] shadow-lg" 
            style="background-image: url('{{ asset('images/assessments/card2.png') }}'); background-size: cover; background-position: center;">
            <div class="relative z-10 w-[70%] md:w-[60%]">
                <div class="flex items-center mb-2 md:mb-4">
                    <h3 class="text-base md:text-xl font-bold">🌟 Level Up Your Skills!</h3>
                </div>
                <p class="text-white mb-2 md:mb-4 text-sm md:text-base">Adaptive learning just for you. Our smart system adjusts questions to match your learning pace perfectly!</p>
            </div>
        </div>

        <!-- Learning is Fun Card -->
        <div class="relative overflow-hidden rounded-xl md:rounded-2xl p-4 md:p-6 text-white min-h-[120px] md:min-h-[200px] border-b-4 border-[#396697] shadow-lg" 
            style="background-image: url('{{ asset('images/assessments/card3.png') }}'); background-size: cover; background-position: center;">
            <div class="relative z-10 w-[70%] md:w-[60%]">
                <div class="flex items-center mb-2 md:mb-4">
                    <h3 class="text-base md:text-xl font-bold">🎮 Learning is Fun!</h3>
                </div>
                <p class="text-white mb-2 md:mb-4 text-sm md:text-base">Gamified math assessments. Earn points, unlock achievements, and compete with friends while learning!</p>
            </div>
        </div>
</div>

    <!-- Assessment Categories Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <!-- Number and Algebra Card -->
        <div class="rounded-xl shadow-sm p-6 hover:shadow-md transition-shadow relative overflow-hidden border-t-2 border-l-2 border-r-2 border-b-4 border-[#FFA500]"
        style="background-image: url('{{ asset('images/assessments/assess1.png') }}'); background-size: cover; background-position: center; background-repeat: no-repeat;">
            
            <!-- Background Vector (kept on top for visual enhancement) -->
            <div class="absolute top-0 right-0 w-32 h-32 opacity-80 z-0">
                <img src="{{ asset('images/assessments/vector1.png') }}" alt="" class="w-full h-full object-cover">
            </div>
            <div class="absolute bottom-0 left-0 w-32 h-32 opacity-80 z-0">
                <img src="{{ asset('images/assessments/vector2.png') }}" alt="" class="w-full h-full object-cover">
            </div>

            <div class="flex justify-between items-start mb-4 relative z-10">
                <div class="relative w-[100%]">
                    <h3 class="text-lg font-bold text-white mb-1">Number and Algebra</h3>
                    <p class="text-sm text-gray-100">Test your knowledge of numbers, operations, and algebraic concepts</p>
                </div>
                @php
                    $naMastery = $masteryData['Number_Algebra'] ?? null;
                    $naLevel = $naMastery ? $naMastery->current_difficulty_level : 'Beginner';
                    $naProgress = $naMastery ? round($naMastery->mastery_probability * 100) : 30;
                @endphp
                <span class="bg-{{ $naLevel === 'Beginner' ? 'green' : ($naLevel === 'Intermediate' ? 'orange' : 'red') }}-100 
                            text-{{ $naLevel === 'Beginner' ? 'green' : ($naLevel === 'Intermediate' ? 'orange' : 'red') }}-700 
                            text-xs font-medium px-2 py-1 rounded-full">
                    {{ $naLevel }}
                </span>
            </div>
            
            <!-- Progress Bar -->
            <div class="mb-4 relative z-10">
                <div class="flex justify-between text-sm text-gray-100 mb-1">
                    <span>Mastery Level</span>
                    <span>{{ $naProgress }}%</span>
                </div>
                <div class="w-full bg-gray-300 rounded-full h-2">
                    <div class="bg-{{ $naProgress >= 70 ? 'green' : ($naProgress >= 40 ? 'orange' : 'red') }}-400 
                                h-2 rounded-full" style="width: {{ $naProgress }}%">
                    </div>
                </div>
            </div>

            <!-- Stats -->
            <div class="flex justify-between mb-4 relative z-10">
                <div class="text-center">
                    <div class="flex items-center justify-center mb-1">
                        <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center mr-2">
                            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <span class="text-xl font-bold text-white">
                            {{ $naMastery ? $naMastery->correct_answers : 0 }}
                        </span>
                    </div>
                    <span class="text-xs text-gray-100">Correct</span>
                </div>
                <div class="text-center">
                    <div class="flex items-center justify-center mb-1">
                        <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mr-2">
                            <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <span class="text-xl font-bold text-white">
                            {{ $naMastery ? $naMastery->total_questions_answered : 0 }}
                        </span>
                    </div>
                    <span class="text-xs text-gray-100">Total</span>
                </div>
            </div>

            <!-- Button -->
            <button onclick="startAssessment('Measurement_Geometry')" 
                class="w-full text-white py-2 px-4 rounded-xl font-medium transition-all duration-300 relative z-20 bg-cover bg-center bg-no-repeat hover:brightness-110 hover:bg-[rgba(139,86,204,0.3)]"
                style="background-image: url('{{ asset('images/assessments/btnbg.png') }}');">
                View Assessment
            </button>
        </div>

        <!-- Measurement and Geometry Card -->
        <div class="rounded-xl shadow-sm p-6 hover:shadow-md transition-shadow relative overflow-hidden border-t-2 border-l-2 border-r-2 border-b-4 border-[#FFA500]"
        style="background-image: url('{{ asset('images/assessments/assess1.png') }}'); background-size: cover; background-position: center; background-repeat: no-repeat;">
            
            <!-- Background Vector (kept on top for visual enhancement) -->
            <div class="absolute top-0 right-0 w-32 h-32 opacity-80 z-0">
                <img src="{{ asset('images/assessments/vector1.png') }}" alt="" class="w-full h-full object-cover">
            </div>
            <div class="absolute bottom-0 left-0 w-32 h-32 opacity-80 z-0">
                <img src="{{ asset('images/assessments/vector2.png') }}" alt="" class="w-full h-full object-cover">
            </div>
            <div class="flex justify-between items-start mb-4 relative z-20">
                <div class=" relative w-[100%]">
                    <h3 class="text-lg font-bold text-white mb-1">Measurement and Geometry</h3>
                    <p class="text-sm text-gray-100">Test your knowledge of shapes, angles, and spatial relationships</p>
                </div>
                @php
                    $mgMastery = $masteryData['Measurement_Geometry'] ?? null;
                    $mgLevel = $mgMastery ? $mgMastery->current_difficulty_level : 'Beginner';
                    $mgProgress = $mgMastery ? round($mgMastery->mastery_probability * 100) : 30;
                @endphp
                <span class="bg-{{ $mgLevel === 'Beginner' ? 'green' : ($mgLevel === 'Intermediate' ? 'orange' : 'red') }}-100 text-{{ $mgLevel === 'Beginner' ? 'green' : ($mgLevel === 'Intermediate' ? 'orange' : 'red') }}-700 text-xs font-medium px-2 py-1 rounded-full relative z-20">{{ $mgLevel }}</span>
            </div>

            <!-- Progress Bar -->
            <div class="mb-4 relative z-10">
                <div class="flex justify-between text-sm text-gray-100 mb-1">
                    <span>Mastery Level</span>
                    <span>{{ $mgProgress }}%</span>
                </div>
            <div class="w-full bg-gray-300 rounded-full h-2">
                <div class="bg-{{ $naProgress >= 70 ? 'green' : ($naProgress >= 40 ? 'orange' : 'red') }}-400 
                            h-2 rounded-full" style="width: {{ $naProgress }}%">
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="flex justify-between mb-4 relative z-10">
            <div class="text-center">
                <div class="flex items-center justify-center mb-1">
                    <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center mr-2">
                        <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <span class="text-xl font-bold text-white">
                        {{ $naMastery ? $naMastery->correct_answers : 0 }}
                    </span>
                </div>
                <span class="text-xs text-gray-100">Correct</span>
            </div>
            <div class="text-center">
                <div class="flex items-center justify-center mb-1">
                    <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mr-2">
                        <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <span class="text-xl font-bold text-white">
                        {{ $naMastery ? $naMastery->total_questions_answered : 0 }}
                    </span>
                </div>
                <span class="text-xs text-gray-100">Total</span>
            </div>
        </div>

        <!-- Button -->
        <button onclick="startAssessment('Measurement_Geometry')" 
            class="w-full text-white py-2 px-4 rounded-xl font-medium transition-all duration-300 relative z-20 bg-cover bg-center bg-no-repeat hover:brightness-110 hover:bg-[rgba(139,86,204,0.3)]"
            style="background-image: url('{{ asset('images/assessments/btnbg.png') }}');">
            View Assessment
        </button>
    </div>

        <!-- Data and Probability Card -->
           <div class="rounded-xl shadow-sm p-6 hover:shadow-md transition-shadow relative overflow-hidden border-t-2 border-l-2 border-r-2 border-b-4 border-[#FFA500]"
        style="background-image: url('{{ asset('images/assessments/assess1.png') }}'); background-size: cover; background-position: center; background-repeat: no-repeat;">
            
            <!-- Background Vector (kept on top for visual enhancement) -->
            <div class="absolute top-0 right-0 w-32 h-32 opacity-80 z-0">
                <img src="{{ asset('images/assessments/vector1.png') }}" alt="" class="w-full h-full object-cover">
            </div>
            <div class="absolute bottom-0 left-0 w-32 h-32 opacity-80 z-0">
                <img src="{{ asset('images/assessments/vector2.png') }}" alt="" class="w-full h-full object-cover">
            </div>

            <div class="flex justify-between items-start mb-4 relative z-10">
                <div class=" relative w-[100%]">
                    <h3 class="text-lg font-bold text-white mb-1">Data and Probabilit</h3>
                    <p class="text-sm text-gray-100">Explore data tables, bar graphs, line plots, mean, and chance events</p>
                </div>
                @php
                    $dpMastery = $masteryData['Data_Probability'] ?? null;
                    $dpLevel = $dpMastery ? $dpMastery->current_difficulty_level : 'Beginner';
                    $dpProgress = $dpMastery ? round($dpMastery->mastery_probability * 100) : 30;
                @endphp
                <span class="bg-{{ $dpLevel === 'Beginner' ? 'green' : ($dpLevel === 'Intermediate' ? 'orange' : 'red') }}-100 text-{{ $dpLevel === 'Beginner' ? 'green' : ($dpLevel === 'Intermediate' ? 'orange' : 'red') }}-700 text-xs font-medium px-2 py-1 rounded-full">{{ $dpLevel }}</span>
            </div>
            
                <!-- Progress Bar -->
            <div class="mb-4 relative z-10">
                <div class="flex justify-between text-sm text-gray-100 mb-1">
                    <span>Mastery Level</span>
                    <span>{{ $mgProgress }}%</span>
                </div>
            <div class="w-full bg-gray-300 rounded-full h-2">
                <div class="bg-{{ $naProgress >= 70 ? 'green' : ($naProgress >= 40 ? 'orange' : 'red') }}-400 
                            h-2 rounded-full" style="width: {{ $naProgress }}%">
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="flex justify-between mb-4 relative z-10">
            <div class="text-center">
                <div class="flex items-center justify-center mb-1">
                    <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center mr-2">
                        <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <span class="text-xl font-bold text-white">
                        {{ $naMastery ? $naMastery->correct_answers : 0 }}
                    </span>
                </div>
                <span class="text-xs text-gray-100">Correct</span>
            </div>
            <div class="text-center">
                <div class="flex items-center justify-center mb-1">
                    <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mr-2">
                        <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <span class="text-xl font-bold text-white">
                        {{ $naMastery ? $naMastery->total_questions_answered : 0 }}
                    </span>
                </div>
                <span class="text-xs text-gray-100">Total</span>
            </div>
        </div>

        <!-- Button -->
        <button onclick="startAssessment('Measurement_Geometry')" 
            class="w-full text-white py-2 px-4 rounded-xl font-medium transition-all duration-300 relative z-20 bg-cover bg-center bg-no-repeat hover:brightness-110 hover:bg-[rgba(139,86,204,0.3)]"
            style="background-image: url('{{ asset('images/assessments/btnbg.png') }}');">
            View Assessment
        </button>
    </div>
    </div>
</div>

<!-- Assessment Modal -->
<div id="assessmentModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl shadow-lg max-w-4xl w-full mx-4 max-h-[90vh] overflow-hidden">
        <!-- Modal Header -->
        <div class="flex justify-between items-center p-6 border-b">
            <div>
                <h2 id="modalTitle" class="text-2xl font-bold text-gray-900">Assessment</h2>
                <p id="modalSubtitle" class="text-sm text-gray-600 mt-1"></p>
            </div>
            <button onclick="closeAssessmentModal()" class="text-gray-400 hover:text-gray-600 text-2xl">
                &times;
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6 overflow-y-auto max-h-[70vh]">
            <!-- Loading State -->
            <div id="loadingState" class="text-center py-8">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
                <p class="text-gray-600">Preparing your assessment...</p>
            </div>

            <!-- Question Display -->
            <div id="questionDisplay" class="hidden">
                <!-- Progress Bar -->
                <div class="mb-6">
                    <div class="flex justify-between text-sm text-gray-600 mb-2">
                        <span>Question <span id="currentQuestion">1</span> of <span id="totalQuestions">15</span></span>
                        <span>Mastery: <span id="currentMastery">30%</span></span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div id="progressBar" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 6.67%"></div>
                    </div>
                </div>

                <!-- Question Content -->
                <div id="questionContent" class="mb-6">
                    <h3 id="questionText" class="text-lg font-semibold text-gray-900 mb-4"></h3>
                    <div id="questionOptions" class="space-y-3"></div>
                </div>

                <!-- Question Controls -->
                <div class="flex justify-between items-center">
                    <button id="hintButton" onclick="showHint()" class="text-blue-600 hover:text-blue-800 text-sm">
                        💡 Show Hint
                    </button>
                    <div class="space-x-3">
                        <button id="submitAnswer" onclick="submitCurrentAnswer()" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                            Submit Answer
                        </button>
                    </div>
                </div>

                <!-- Hint Display -->
                <div id="hintDisplay" class="hidden mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <p class="text-yellow-800" id="hintText"></p>
                </div>

                <!-- Feedback Display -->
                <div id="feedbackDisplay" class="hidden mt-4 p-4 rounded-lg">
                    <p id="feedbackText" class="font-medium"></p>
                    <p id="explanationText" class="mt-2 text-sm"></p>
                    <button onclick="nextQuestion()" class="mt-3 bg-blue-600 text-white px-4 py-2 rounded font-medium hover:bg-blue-700">
                        Next Question
                    </button>
                </div>
            </div>

            <!-- Results Display -->
            <div id="resultsDisplay" class="hidden">
                <div class="text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Assessment Complete!</h3>
                    <div id="resultsContent"></div>
                    <button onclick="closeAssessmentModal()" class="mt-6 bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700">
                        Continue Learning
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentSession = null;
let currentQuestionIndex = 0;
let currentQuestions = [];
let selectedAnswer = null;
let hintUsed = false;
let questionStartTime = null;

function startAssessment(competency) {
    document.getElementById('assessmentModal').classList.remove('hidden');
    document.getElementById('loadingState').classList.remove('hidden');
    document.getElementById('questionDisplay').classList.add('hidden');
    document.getElementById('resultsDisplay').classList.add('hidden');
    
    // Set modal title
    const competencyNames = {
        'Number_Algebra': 'Number and Algebra',
        'Measurement_Geometry': 'Measurement and Geometry',
        'Data_Probability': 'Data and Probability'
    };
    document.getElementById('modalTitle').textContent = competencyNames[competency] + ' Assessment';
    document.getElementById('modalSubtitle').textContent = 'Adaptive assessment powered by BKT algorithm';

    // Start assessment via API
    fetch('/assessments/start', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            competency: competency
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            currentSession = {
                session_id: data.session_id,
                competency: competency,
                initial_mastery: data.initial_mastery,
                diagnostic: data.diagnostic
            };
            currentQuestions = data.questions;
            currentQuestionIndex = 0;
            
            if (data.diagnostic) {
                document.getElementById('modalSubtitle').textContent = 'Diagnostic Assessment - Determining your skill level';
            }
            
            document.getElementById('loadingState').classList.add('hidden');
            document.getElementById('questionDisplay').classList.remove('hidden');
            document.getElementById('totalQuestions').textContent = currentQuestions.length;
            
            displayCurrentQuestion();
        } else {
            alert('Failed to start assessment: ' + data.message);
            closeAssessmentModal();
        }
    })
    .catch(error => {
        console.error('Error starting assessment:', error);
        alert('An error occurred while starting the assessment.');
        closeAssessmentModal();
    });
}

function displayCurrentQuestion() {
    if (currentQuestionIndex >= currentQuestions.length) {
        completeAssessment();
        return;
    }

    const question = currentQuestions[currentQuestionIndex];
    const currentQuestionNum = currentQuestionIndex + 1;
    
    // Update progress
    document.getElementById('currentQuestion').textContent = currentQuestionNum;
    const progressPercent = (currentQuestionNum / currentQuestions.length) * 100;
    document.getElementById('progressBar').style.width = progressPercent + '%';
    
    // Display question
    document.getElementById('questionText').textContent = question.question_text;
    
    // Reset state
    selectedAnswer = null;
    hintUsed = false;
    questionStartTime = new Date();
    document.getElementById('submitAnswer').disabled = true;
    document.getElementById('hintButton').classList.remove('hidden');
    document.getElementById('hintDisplay').classList.add('hidden');
    document.getElementById('feedbackDisplay').classList.add('hidden');
    
    // Display options based on question type
    const optionsContainer = document.getElementById('questionOptions');
    optionsContainer.innerHTML = '';
    
    if (question.question_type === 'Multiple_Choice') {
        const choices = ['A', 'B', 'C', 'D'];
        const options = [question.choice_a, question.choice_b, question.choice_c, question.choice_d];
        
        choices.forEach((choice, index) => {
            if (options[index]) {
                const optionDiv = document.createElement('div');
                optionDiv.className = 'flex items-center space-x-3 p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors';
                optionDiv.onclick = () => selectAnswer(choice);
                
                optionDiv.innerHTML = `
                    <input type="radio" name="answer" value="${choice}" class="text-blue-600 pointer-events-none">
                    <label class="flex-1 cursor-pointer">${choice}. ${options[index]}</label>
                `;
                
                optionsContainer.appendChild(optionDiv);
            }
        });
    } else if (question.question_type === 'True_False') {
        ['True', 'False'].forEach(option => {
            const optionDiv = document.createElement('div');
            optionDiv.className = 'flex items-center space-x-3 p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors';
            optionDiv.onclick = () => selectAnswer(option);
            
            optionDiv.innerHTML = `
                <input type="radio" name="answer" value="${option}" class="text-blue-600 pointer-events-none">
                <label class="flex-1 cursor-pointer">${option}</label>
            `;
            
            optionsContainer.appendChild(optionDiv);
        });
    } else if (question.question_type === 'Fill_Blanks') {
        const inputDiv = document.createElement('div');
        inputDiv.innerHTML = `
            <input type="text" id="fillBlankAnswer" class="w-full p-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                   placeholder="Enter your answer here..." 
                   onkeyup="handleTextInput(this.value)">
        `;
        optionsContainer.appendChild(inputDiv);
    }
}

function selectAnswer(answer) {
    selectedAnswer = answer;
    
    // Update radio button states
    const radioButtons = document.querySelectorAll('input[name="answer"]');
    radioButtons.forEach(radio => {
        radio.checked = radio.value === answer;
        radio.parentElement.classList.toggle('bg-blue-50', radio.checked);
        radio.parentElement.classList.toggle('border-blue-300', radio.checked);
    });
    
    document.getElementById('submitAnswer').disabled = false;
}

function handleTextInput(value) {
    selectedAnswer = value.trim();
    document.getElementById('submitAnswer').disabled = selectedAnswer === '';
}

function showHint() {
    const question = currentQuestions[currentQuestionIndex];
    if (question.hint_text) {
        document.getElementById('hintText').textContent = question.hint_text;
        document.getElementById('hintDisplay').classList.remove('hidden');
        document.getElementById('hintButton').classList.add('hidden');
        hintUsed = true;
    }
}

function submitCurrentAnswer() {
    if (!selectedAnswer) return;
    
    const question = currentQuestions[currentQuestionIndex];
    const timeTaken = Math.round((new Date() - questionStartTime) / 1000);
    
    // Disable submit button
    document.getElementById('submitAnswer').disabled = true;
    
    // Submit answer to backend
    fetch('/assessments/submit-answer', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            session_id: currentSession.session_id,
            question_id: question.question_id,
            answer: selectedAnswer,
            time_taken: timeTaken,
            hint_used: hintUsed
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showFeedback(data.is_correct, data.explanation, data.new_mastery);
        } else {
            alert('Error submitting answer: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error submitting answer:', error);
        alert('An error occurred while submitting your answer.');
    });
}

function showFeedback(isCorrect, explanation, newMastery) {
    const feedbackDiv = document.getElementById('feedbackDisplay');
    const feedbackText = document.getElementById('feedbackText');
    const explanationText = document.getElementById('explanationText');
    
    if (isCorrect) {
        feedbackDiv.className = 'mt-4 p-4 bg-green-50 border border-green-200 rounded-lg';
        feedbackText.className = 'font-medium text-green-800';
        feedbackText.textContent = '✅ Correct!';
    } else {
        feedbackDiv.className = 'mt-4 p-4 bg-red-50 border border-red-200 rounded-lg';
        feedbackText.className = 'font-medium text-red-800';
        feedbackText.textContent = '❌ Incorrect';
    }
    
    if (explanation) {
        explanationText.textContent = explanation;
        explanationText.className = 'mt-2 text-sm text-gray-700';
    }
    
    // Update mastery display
    document.getElementById('currentMastery').textContent = Math.round(newMastery * 100) + '%';
    
    feedbackDiv.classList.remove('hidden');
}

function nextQuestion() {
    currentQuestionIndex++;
    displayCurrentQuestion();
}

function completeAssessment() {
    // Call completion endpoint
    fetch('/assessments/complete', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            session_id: currentSession.session_id,
            competency: currentSession.competency
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showResults(data.results);
        } else {
            alert('Error completing assessment: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error completing assessment:', error);
        alert('An error occurred while completing the assessment.');
    });
}

function showResults(results) {
    document.getElementById('questionDisplay').classList.add('hidden');
    document.getElementById('resultsDisplay').classList.remove('hidden');
    
    const resultsContent = document.getElementById('resultsContent');
    const masteryChange = results.mastery_change;
    const changeIcon = masteryChange > 0 ? '📈' : masteryChange < 0 ? '📉' : '➡️';
    const changeColor = masteryChange > 0 ? 'text-green-600' : masteryChange < 0 ? 'text-red-600' : 'text-gray-600';
    
    resultsContent.innerHTML = `
        <div class="grid grid-cols-2 gap-4 mt-4 text-sm">
            <div class="bg-gray-50 p-3 rounded">
                <div class="font-medium text-gray-900">${results.correct_answers}/${results.total_questions}</div>
                <div class="text-gray-600">Questions Correct</div>
            </div>
            <div class="bg-gray-50 p-3 rounded">
                <div class="font-medium text-gray-900">${Math.round(results.accuracy_rate * 100)}%</div>
                <div class="text-gray-600">Accuracy</div>
            </div>
            <div class="bg-gray-50 p-3 rounded">
                <div class="font-medium text-gray-900">${results.total_points}</div>
                <div class="text-gray-600">Points Earned</div>
            </div>
            <div class="bg-gray-50 p-3 rounded">
                <div class="font-medium text-gray-900">${results.new_difficulty_level}</div>
                <div class="text-gray-600">Current Level</div>
            </div>
        </div>
        <div class="mt-4 p-4 bg-blue-50 rounded-lg">
            <div class="flex items-center justify-center space-x-2">
                <span class="text-lg">${changeIcon}</span>
                <div class="text-center">
                    <div class="text-sm text-gray-600">Mastery Level</div>
                    <div class="font-bold ${changeColor}">
                        ${Math.round(results.initial_mastery * 100)}% → ${Math.round(results.final_mastery * 100)}%
                    </div>
                </div>
            </div>
        </div>
    `;
}

function closeAssessmentModal() {
    document.getElementById('assessmentModal').classList.add('hidden');
    // Refresh the page to update mastery data
    window.location.reload();
}

// Close modal when clicking outside
document.getElementById('assessmentModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeAssessmentModal();
    }
});
</script>

@endsection