@extends('layouts.user_layout')

@section('title', 'Quiz - ' . $assessment->title)

@section('content')
@if($assignment->status === 'Completed')
    <div class="max-w-4xl mx-auto text-center py-12">
        <div class="bg-green-100 border border-green-200 rounded-lg p-8">
            <span class="material-symbols-outlined text-green-600 text-6xl mb-4">check_circle</span>
            <h1 class="text-2xl font-bold text-green-800 mb-2">Assessment Already Completed</h1>
            <p class="text-green-700 mb-6">This assessment has already been completed. You cannot retake it.</p>
            <a href="{{ route('teacher-assessments.show', $assessment->id) }}" 
               class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors">
                View Results
            </a>
        </div>
    </div>
@else
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $assessment->title }}</h1>
                <p class="text-gray-600">{{ $assessment->description }}</p>
            </div>
            <div class="text-right">
                <div class="text-sm text-gray-500">Time Remaining</div>
                <div id="timer" class="text-2xl font-bold text-blue-600">{{ $assessment->time_limit }}:00</div>
            </div>
        </div>
    </div>

    <!-- Progress Bar -->
    <div class="mb-6">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-gray-700">Progress</span>
            <span class="text-sm text-gray-500">
                <span id="current-question">1</span> of {{ count($questionDetails) }}
            </span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
            <div id="progress-bar" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
        </div>
    </div>

    <!-- Question Container -->
    <div id="question-container" class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <!-- Question will be loaded here dynamically -->
    </div>

    <!-- Navigation -->
    <div class="flex justify-between items-center">
        
        <div class="flex space-x-2">
            <button id="submit-btn" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed">
                Submit Answer
            </button>
            <button id="next-btn" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed">
                Next Question
            </button>
        </div>
    </div>

    <!-- Complete Quiz Button (hidden initially) -->
    <div id="complete-section" class="text-center mt-6 hidden">
        <button id="complete-btn" class="px-8 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 text-lg font-semibold">
            Complete Quiz
        </button>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loading-overlay" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 text-center">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-4"></div>
        <p class="text-gray-700">Processing your answer...</p>
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
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                Question ${currentQuestionIndex + 1}: ${question.question_text}
            </h3>
    `;
    
    if (question.question_type === 'multiple_choice') {
        html += `
            <div class="space-y-3">
                <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                    <input type="radio" name="answer" value="A" class="mr-3 text-blue-600">
                    <span class="font-medium">A.</span>
                    <span class="ml-2">${question.choice_a}</span>
                </label>
                <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                    <input type="radio" name="answer" value="B" class="mr-3 text-blue-600">
                    <span class="font-medium">B.</span>
                    <span class="ml-2">${question.choice_b}</span>
                </label>
                <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                    <input type="radio" name="answer" value="C" class="mr-3 text-blue-600">
                    <span class="font-medium">C.</span>
                    <span class="ml-2">${question.choice_c}</span>
                </label>
                <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                    <input type="radio" name="answer" value="D" class="mr-3 text-blue-600">
                    <span class="font-medium">D.</span>
                    <span class="ml-2">${question.choice_d}</span>
                </label>
            </div>
        `;
    } else if (question.question_type === 'true_false') {
        html += `
            <div class="space-y-3">
                <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                    <input type="radio" name="answer" value="True" class="mr-3 text-blue-600">
                    <span class="font-medium">True</span>
                </label>
                <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                    <input type="radio" name="answer" value="False" class="mr-3 text-blue-600">
                    <span class="font-medium">False</span>
                </label>
            </div>
        `;
    } else if (question.question_type === 'fill_blanks') {
        html += `
            <div class="space-y-3">
                <input type="text" name="answer" placeholder="Enter your answer here..." 
                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        `;
    }
    
    html += `
        </div>
        ${question.hint_text ? `
            <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                <div class="flex items-center">
                    <span class="material-symbols-outlined text-yellow-600 mr-2">lightbulb</span>
                    <span class="text-sm text-yellow-800">${question.hint_text}</span>
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
