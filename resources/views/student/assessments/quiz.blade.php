@extends('layouts.user_layout')

@section('title', 'Quiz - ' . $assessment->title)

@section('content')
@if(isset($assignment) && $assignment->status === 'Completed')
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-2xl w-full">
            <div class="bg-gradient-to-br from-emerald-50 to-teal-50 border-2 border-emerald-200 rounded-2xl p-12 text-center shadow-xl">
                <div class="bg-emerald-100 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6">
                    <span class="material-symbols-outlined text-emerald-600 text-5xl">check_circle</span>
                </div>
                <h1 class="text-3xl font-bold text-emerald-900 mb-3">Assessment Completed!</h1>
                <p class="text-emerald-700 text-lg mb-8">You've already finished this assessment. Ready to see how you did?</p>
                <a href="{{ route('teacher-assessments.show', $assessment->id) }}" 
                   class="inline-block bg-emerald-600 text-white px-8 py-4 rounded-xl hover:bg-emerald-700 transition-all duration-200 shadow-lg hover:shadow-xl font-semibold">
                    View Your Results
                </a>
            </div>
        </div>
    </div>
@else
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 py-8 px-4">
    <div class="max-w-5xl mx-auto">
        <!-- Header Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8 mb-6 border border-gray-100">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="flex-1">
                    <div class="inline-block bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-4 py-1 rounded-full text-sm font-semibold mb-3">
                        Live Assessment
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $assessment->title }}</h1>
                    <p class="text-gray-600 text-lg">{{ $assessment->description }}</p>
                </div>
                <div class="lg:text-right">
                    <div class="bg-gradient-to-br from-orange-50 to-red-50 rounded-2xl p-6 border-2 border-orange-200">
                        <div class="text-sm font-semibold text-orange-600 uppercase tracking-wide mb-2">Time Remaining</div>
                        <div id="timer" class="text-4xl font-bold bg-gradient-to-r from-orange-600 to-red-600 bg-clip-text text-transparent">{{ $assessment->time_limit }}:00</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progress Card -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-6 border border-gray-100">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-indigo-600">quiz</span>
                    <span class="font-semibold text-gray-800">Your Progress</span>
                </div>
                <div class="bg-indigo-100 text-indigo-700 px-4 py-2 rounded-full font-bold text-sm">
                    <span id="current-question">1</span> / {{ count($questionDetails) }}
                </div>
            </div>
            <div class="relative w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                <div id="progress-bar" class="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 h-3 rounded-full transition-all duration-500 shadow-lg" style="width: 0%"></div>
            </div>
        </div>

        <!-- Question Card -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-8 py-4">
                <div class="flex items-center gap-2 text-white">
                    <span class="material-symbols-outlined">help</span>
                    <span class="font-semibold">Question</span>
                </div>
            </div>
            <div id="question-container" class="p-8">
                <!-- Question will be loaded here dynamically -->
            </div>
        </div>

        <!-- Navigation Card -->
        <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="flex flex-wrap gap-3 w-full sm:w-auto">
                    <button id="submit-btn" class="flex-1 sm:flex-none px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed font-semibold shadow-lg hover:shadow-xl transition-all duration-200 flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-xl">send</span>
                        Submit Answer
                    </button>
                    <button id="next-btn" class="invisible flex-1 sm:flex-none px-8 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-xl hover:from-emerald-700 hover:to-teal-700 disabled:opacity-50 disabled:cursor-not-allowed font-semibold shadow-lg hover:shadow-xl transition-all duration-200 flex items-center justify-center gap-2">
                        Next Question
                        <span class="material-symbols-outlined text-xl">arrow_forward</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Complete Quiz Section -->
        <div id="complete-section" class="invisible text-center mt-6 hidden">
            <div class="bg-gradient-to-r from-emerald-600 to-teal-600 rounded-2xl p-8 shadow-2xl">
                <div class="bg-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-emerald-600 text-4xl">flag</span>
                </div>
                <h3 class="text-white text-2xl font-bold mb-3">Ready to Finish?</h3>
                <p class="text-emerald-100 mb-6">You've reached the final question!</p>
                <button id="complete-btn" class="bg-white text-emerald-700 px-10 py-4 rounded-xl hover:bg-emerald-50 text-lg font-bold shadow-xl hover:shadow-2xl transition-all duration-200">
                    Complete Quiz
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loading-overlay" class="fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-2xl p-8 text-center shadow-2xl max-w-sm mx-4">
        <div class="relative w-20 h-20 mx-auto mb-6">
            <div class="absolute inset-0 border-4 border-blue-200 rounded-full"></div>
            <div class="absolute inset-0 border-4 border-blue-600 rounded-full border-t-transparent animate-spin"></div>
        </div>
        <p class="text-gray-800 font-semibold text-lg">Processing your answer...</p>
        <p class="text-gray-500 text-sm mt-2">Please wait a moment</p>
    </div>
</div>

<script>
let currentQuestionIndex = 0;
let questions = @json($questionDetails);
let timeLimit = {{ $assessment->time_limit }}; // in minutes
let timeRemaining = timeLimit * 60; // in seconds
let timerInterval;
let questionStartTime;

// Initialize quiz
document.addEventListener('DOMContentLoaded', function() {
    loadQuestion(0);
    startTimer();
});

function startTimer() {
    timerInterval = setInterval(function() {
        timeRemaining--;
        
        const minutes = Math.floor(timeRemaining / 60);
        const seconds = timeRemaining % 60;
        document.getElementById('timer').textContent = 
            `${minutes}:${seconds.toString().padStart(2, '0')}`;
        
        if (timeRemaining <= 0) {
            clearInterval(timerInterval);
            alert('Time\'s up! The quiz will be submitted automatically.');
            completeQuiz();
        }
    }, 1000);
}

function loadQuestion(index) {
    if (index < 0 || index >= questions.length) return;
    
    currentQuestionIndex = index;
    const question = questions[index];
    questionStartTime = Date.now();
    
    // Update progress
    document.getElementById('current-question').textContent = index + 1;
    document.getElementById('progress-bar').style.width = 
        `${((index + 1) / questions.length) * 100}%`;
    
    // Update navigation buttons
    document.getElementById('next-btn').disabled = index === questions.length - 1;
    
    // Show/hide complete button
    if (index === questions.length - 1) {
        document.getElementById('complete-section').classList.remove('hidden');
        document.getElementById('next-btn').classList.add('hidden');
    } else {
        document.getElementById('complete-section').classList.add('hidden');
        document.getElementById('next-btn').classList.remove('hidden');
    }
    
    // Load question content
    const container = document.getElementById('question-container');
    container.innerHTML = generateQuestionHTML(question);
    
    // Clear any previous selections
    clearSelections();
}

function generateQuestionHTML(question) {
    let html = `
        <div class="mb-6">
            <div class="flex items-start gap-3 mb-6">
                <div class="bg-indigo-100 text-indigo-700 font-bold text-sm px-3 py-1 rounded-lg flex-shrink-0">
                    Q${currentQuestionIndex + 1}
                </div>
                <h3 class="text-xl font-bold text-gray-900 flex-1">
                    ${question.question_text}
                </h3>
            </div>
    `;
    
    if (question.question_type === 'multiple_choice') {
        html += `
            <div class="space-y-3">
                <label class="group flex items-center p-4 border-2 border-gray-200 rounded-xl hover:border-indigo-400 hover:bg-indigo-50 cursor-pointer transition-all duration-200">
                    <input type="radio" name="answer" value="A" class="w-5 h-5 mr-4 text-indigo-600 focus:ring-indigo-500">
                    <div class="flex items-center gap-3 flex-1">
                        <span class="font-bold text-indigo-600 bg-indigo-100 w-8 h-8 rounded-lg flex items-center justify-center group-hover:bg-indigo-200">A</span>
                        <span class="text-gray-800">${question.choice_a}</span>
                    </div>
                </label>
                <label class="group flex items-center p-4 border-2 border-gray-200 rounded-xl hover:border-indigo-400 hover:bg-indigo-50 cursor-pointer transition-all duration-200">
                    <input type="radio" name="answer" value="B" class="w-5 h-5 mr-4 text-indigo-600 focus:ring-indigo-500">
                    <div class="flex items-center gap-3 flex-1">
                        <span class="font-bold text-indigo-600 bg-indigo-100 w-8 h-8 rounded-lg flex items-center justify-center group-hover:bg-indigo-200">B</span>
                        <span class="text-gray-800">${question.choice_b}</span>
                    </div>
                </label>
                <label class="group flex items-center p-4 border-2 border-gray-200 rounded-xl hover:border-indigo-400 hover:bg-indigo-50 cursor-pointer transition-all duration-200">
                    <input type="radio" name="answer" value="C" class="w-5 h-5 mr-4 text-indigo-600 focus:ring-indigo-500">
                    <div class="flex items-center gap-3 flex-1">
                        <span class="font-bold text-indigo-600 bg-indigo-100 w-8 h-8 rounded-lg flex items-center justify-center group-hover:bg-indigo-200">C</span>
                        <span class="text-gray-800">${question.choice_c}</span>
                    </div>
                </label>
                <label class="group flex items-center p-4 border-2 border-gray-200 rounded-xl hover:border-indigo-400 hover:bg-indigo-50 cursor-pointer transition-all duration-200">
                    <input type="radio" name="answer" value="D" class="w-5 h-5 mr-4 text-indigo-600 focus:ring-indigo-500">
                    <div class="flex items-center gap-3 flex-1">
                        <span class="font-bold text-indigo-600 bg-indigo-100 w-8 h-8 rounded-lg flex items-center justify-center group-hover:bg-indigo-200">D</span>
                        <span class="text-gray-800">${question.choice_d}</span>
                    </div>
                </label>
            </div>
        `;
    } else if (question.question_type === 'true_false') {
        html += `
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <label class="group flex flex-col items-center justify-center p-6 border-2 border-gray-200 rounded-xl hover:border-emerald-400 hover:bg-emerald-50 cursor-pointer transition-all duration-200">
                    <input type="radio" name="answer" value="True" class="w-5 h-5 mb-3 text-emerald-600 focus:ring-emerald-500">
                    <span class="material-symbols-outlined text-4xl text-emerald-600 mb-2">check_circle</span>
                    <span class="font-bold text-lg text-gray-800">True</span>
                </label>
                <label class="group flex flex-col items-center justify-center p-6 border-2 border-gray-200 rounded-xl hover:border-red-400 hover:bg-red-50 cursor-pointer transition-all duration-200">
                    <input type="radio" name="answer" value="False" class="w-5 h-5 mb-3 text-red-600 focus:ring-red-500">
                    <span class="material-symbols-outlined text-4xl text-red-600 mb-2">cancel</span>
                    <span class="font-bold text-lg text-gray-800">False</span>
                </label>
            </div>
        `;
    } else if (question.question_type === 'fill_blanks') {
        html += `
            <div class="space-y-3">
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">edit</span>
                    <input type="text" name="answer" placeholder="Type your answer here..." 
                           class="w-full border-2 border-gray-200 rounded-xl pl-12 pr-4 py-4 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-lg">
                </div>
            </div>
        `;
    }
    
    html += `
        </div>
        ${question.hint_text ? `
            <div class="mt-6 p-4 bg-gradient-to-r from-amber-50 to-yellow-50 border-l-4 border-amber-400 rounded-xl">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-amber-600 text-2xl">lightbulb</span>
                    <div>
                        <div class="font-semibold text-amber-800 mb-1">Hint</div>
                        <span class="text-amber-700">${question.hint_text}</span>
                    </div>
                </div>
            </div>
        ` : ''}
    `;
    
    return html;
}

function clearSelections() {
    const radioButtons = document.querySelectorAll('input[type="radio"]');
    radioButtons.forEach(radio => radio.checked = false);
    
    const textInputs = document.querySelectorAll('input[type="text"]');
    textInputs.forEach(input => input.value = '');
}

function getSelectedAnswer() {
    const radioButtons = document.querySelectorAll('input[name="answer"]:checked');
    if (radioButtons.length > 0) {
        return radioButtons[0].value;
    }
    
    const textInput = document.querySelector('input[name="answer"]');
    if (textInput) {
        return textInput.value.trim();
    }
    
    return null;
}

// Event Listeners

document.getElementById('next-btn').addEventListener('click', function() {
    if (currentQuestionIndex < questions.length - 1) {
        loadQuestion(currentQuestionIndex + 1);
    }
});

document.getElementById('submit-btn').addEventListener('click', function() {
    const answer = getSelectedAnswer();
    if (!answer) {
        alert('Please select an answer before submitting.');
        return;
    }
    
    submitAnswer(answer);
});

document.getElementById('complete-btn').addEventListener('click', function() {
    const answer = getSelectedAnswer();
    if (answer) {
        submitAnswer(answer, true);
    } else {
        completeQuiz();
    }
});

function submitAnswer(answer, isLastQuestion = false) {
    const question = questions[currentQuestionIndex];
    const timeTaken = Math.floor((Date.now() - questionStartTime) / 1000);
    
    // Show loading overlay
    document.getElementById('loading-overlay').classList.remove('hidden');
    
    fetch(`{{ route('teacher-assessments.submit-answer', $assessment->id) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            question_id: question.question_id,
            answer: answer,
            time_taken: timeTaken
        })
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('loading-overlay').classList.add('hidden');
        
        if (data.success) {
            // Track answered questions
            const answeredQuestions = sessionStorage.getItem('answeredQuestions') ? 
                JSON.parse(sessionStorage.getItem('answeredQuestions')) : [];
            if (!answeredQuestions.includes(question.question_id)) {
                answeredQuestions.push(question.question_id);
                sessionStorage.setItem('answeredQuestions', JSON.stringify(answeredQuestions));
            }
            
            if (isLastQuestion || data.is_last_question) {
                completeQuiz();
            } else {
                loadQuestion(currentQuestionIndex + 1);
            }
        } else {
            alert('Error submitting answer: ' + data.message);
        }
    })
    .catch(error => {
        document.getElementById('loading-overlay').classList.add('hidden');
        console.error('Error:', error);
        alert('An error occurred while submitting your answer.');
    });
}

function completeQuiz() {
    // Check if all questions have been answered
    const answeredQuestions = sessionStorage.getItem('answeredQuestions') ? 
        JSON.parse(sessionStorage.getItem('answeredQuestions')) : [];
    
    if (answeredQuestions.length < questions.length) {
        const unanswered = questions.length - answeredQuestions.length;
        if (!confirm(`You have ${unanswered} unanswered question(s). Are you sure you want to complete the quiz? This action cannot be undone.`)) {
            return;
        }
    } else {
        if (!confirm('Are you sure you want to complete the quiz? This action cannot be undone.')) {
            return;
        }
    }
    
    // Clear timer and remove beforeunload warning
    clearInterval(timerInterval);
    window.removeEventListener('beforeunload', preventUnload);
    
    // Clear session storage
    sessionStorage.removeItem('answeredQuestions');
    
    fetch(`{{ route('teacher-assessments.complete', $assessment->id) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (response.ok) {
            // Force redirect to results page
            window.location.replace(`{{ route('teacher-assessments.show', $assessment->id) }}`);
        } else {
            alert('Error completing quiz. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while completing the quiz.');
    });
}

// Prevent page refresh during quiz
function preventUnload(e) {
    e.preventDefault();
    e.returnValue = 'Are you sure you want to leave? Your progress will be lost.';
}

window.addEventListener('beforeunload', preventUnload);
</script>
@endif
@endsection