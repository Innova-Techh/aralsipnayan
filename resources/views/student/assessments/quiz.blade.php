<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'AralSipnayan')</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/quiz.css') }}">
    <!-- Vite Assets (includes SweetAlert2) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Baloo Font -->
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
       
    </style>
</head>

<body class="bg-[#C2DAFF]">
    @extends('layouts.user_layout')

    @section('title', 'Quiz - ' . $assessment->title)

    @section('content')
        @if(isset($assignment) && $assignment->status === 'Completed')
            <div class="min-h-screen flex items-center justify-center px-4">
                <div class="max-w-2xl w-full">
                    <div
                        class="bg-gradient-to-br from-emerald-50 to-teal-50 border-2 border-emerald-200 rounded-2xl p-12 text-center shadow-xl">
                        <div class="bg-emerald-100 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6">
                            <span class="material-symbols-outlined text-emerald-600 text-5xl">check_circle</span>
                        </div>
                        <h1 class="text-3xl font-bold text-emerald-900 mb-3">Assessment Completed!</h1>
                        <p class="text-emerald-700 text-lg mb-8">You've already finished this assessment. Ready to see how you
                            did?</p>
                        
                        <div class="flex flex-col sm:flex-row gap-4 justify-center">
                            <a href="{{ route('teacher-assessments.show', $assessment->id) }}"
                                class="inline-block bg-emerald-600 text-white px-8 py-4 rounded-xl hover:bg-emerald-700 transition-all duration-200 shadow-lg hover:shadow-xl font-semibold">
                                View Your Results
                            </a>
                            
                            <form action="{{ route('teacher-assessments.retake', $assessment->id) }}" method="POST" class="inline-block">
                                @csrf
                                <button type="submit"
                                    onclick="return confirm('Are you sure you want to retake this assessment? Your previous score will be replaced with your new score.')"
                                    class="w-full bg-blue-600 text-white px-8 py-4 rounded-xl hover:bg-blue-700 transition-all duration-200 shadow-lg hover:shadow-xl font-semibold">
                                    <span class="material-symbols-outlined text-xl inline-block mr-2 align-middle">refresh</span>
                                    Retake Quiz
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="space-y-8 font-baloo mt-8 px-4 xs:px-4 sm:px-4 md:px-8 lg:px-12 pb-24 sm:pb-20 md:pb-16 lg:pb-20">

                <!-- Header Section -->
                <div class="flex justify-between items-center mb-2 sm:mb-2 md:mb-4 gap-2 sm:gap-4">

                    <!-- Question Counter with Progress -->
                    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 backdrop-blur-sm rounded-full px-3 py-1.5 sm:px-4 sm:py-2 md:px-6 md:py-3 lg:px-9 lg:py-4 border-b-6 border-indigo-800"
                        style="box-shadow: 0 6px 0 #4c1d95;">
                        <span class="text-white font-semibold text-xs sm:text-sm md:text-base lg:text-lg xl:text-xl">
                            Question <span id="current-question">1</span> / {{ count($questionDetails) }}
                        </span>
                    </div>

                    <!-- Timer -->
                    <div class="bg-gradient-to-r from-orange-500 to-red-500 rounded-full px-3 py-1.5 sm:px-4 sm:py-2 md:px-6 md:py-3 lg:px-9 lg:py-4 flex items-center gap-2 sm:gap-3 md:gap-4 border-b-6 border-[#cc4713]"
                        style="box-shadow: 0 6px 0 #cc4713;">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 md:w-6 md:h-6 lg:w-7 lg:h-7 xl:w-8 xl:h-8 text-white"
                            fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="text-white font-bold text-xs sm:text-sm md:text-base lg:text-lg xl:text-lg"
                            id="timer">{{ $assessment->time_limit }}:00</span>
                    </div>
                </div>

                <!-- Progress Bar Card -->
                <div class="bg-white rounded-2xl shadow-lg p-4 md:p-6 border border-gray-100">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-indigo-600">quiz</span>
                            <span class="font-semibold text-gray-800">Your Progress</span>
                        </div>
                        <div class="bg-indigo-100 text-indigo-700 px-4 py-2 rounded-full font-bold text-sm">
                            <span id="progress-percentage">0</span>%
                        </div>
                    </div>
                    <div class="relative w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                        <div id="progress-bar"
                            class="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 h-3 rounded-full transition-all duration-500 shadow-lg"
                            style="width: 0%"></div>
                    </div>
                </div>

                <!-- Quiz Card -->
                <div class="bg-white rounded-2xl md:rounded-3xl p-6 md:p-8 lg:p-10 shadow-2xl">

                    <!-- Question Container -->
                    <div id="question-container" class="mb-8 md:mb-10">
                        <!-- Question will be loaded here dynamically -->
                    </div>

                    <!-- Buttons Row -->
                    <div class="flex justify-between items-center gap-2 sm:gap-4 mt-6">
                        <!-- Submit Button -->
                        <button id="submit-btn"
                            class="bg-gradient-to-r from-emerald-500 to-teal-500 text-white px-4 py-2 sm:px-6 sm:py-2.5 md:px-8 md:py-3 lg:px-12 lg:py-4 rounded-2xl font-bold text-xs sm:text-sm md:text-base lg:text-lg transition-all duration-200 transform hover:scale-105 border-b-6 border-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed"
                            style="box-shadow: 0 6px 0 #047857; text-shadow: -1px -1px 0 #047857, 1px -1px 0 #047857,-1px 1px 0 #047857, 1px 1px 0 #047857, 0 2px 0 #047857;">
                            <span class="material-symbols-outlined text-xl inline-block mr-2">send</span>
                            Submit Answer
                        </button>

                        <!-- Next Button -->
                        <button id="next-btn"
                            class="invisible bg-gradient-to-r from-blue-500 to-indigo-600 text-white px-4 py-2 sm:px-6 sm:py-2.5 md:px-8 md:py-3 lg:px-12 lg:py-4 rounded-2xl font-bold text-xs sm:text-sm md:text-base lg:text-lg transition-all duration-200 transform hover:scale-105 border-b-6 border-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
                            style="box-shadow: 0 6px 0 #1e40af; text-shadow: -1px -1px 0 #1e40af, 1px -1px 0 #1e40af,-1px 1px 0 #1e40af, 1px 1px 0 #1e40af, 0 2px 0 #1e40af;">
                            Next Question
                            <span class="material-symbols-outlined text-xl inline-block ml-2">arrow_forward</span>
                        </button>
                    </div>

                </div>

                <!-- Complete Quiz Section -->
                <div id="complete-section" class="invisible text-center mt-6 hidden">
                    <div class="bg-gradient-to-r from-emerald-600 to-teal-600 rounded-2xl p-8 shadow-2xl border-b-6 border-emerald-800"
                        style="box-shadow: 0 6px 0 #047857;">
                        <div class="bg-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="material-symbols-outlined text-emerald-600 text-4xl">flag</span>
                        </div>
                        <h3 class="text-white text-2xl font-bold mb-3">Ready to Finish?</h3>
                        <p class="text-emerald-100 mb-6">You've reached the final question!</p>
                        <button id="complete-btn"
                            class="bg-white text-emerald-700 px-10 py-4 rounded-xl hover:bg-emerald-50 text-lg font-bold shadow-xl hover:shadow-2xl transition-all duration-200 transform hover:scale-105">
                            Complete Quiz
                        </button>
                    </div>
                </div>
            </div>

            <!-- Loading Overlay -->
            <div id="loading-overlay"
                class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur-sm flex items-center justify-center z-50 hidden">
                <div class="text-center">
                    <!-- Spinner -->
                    <svg class="pl mx-auto mb-6" width="200" height="200" viewBox="0 0 240 240">
                        <circle class="pl__ring pl__ring--a" cx="120" cy="120" r="105" fill="none"></circle>
                        <circle class="pl__ring pl__ring--b" cx="120" cy="120" r="35" fill="none"></circle>
                        <circle class="pl__ring pl__ring--c" cx="85" cy="120" r="70" fill="none"></circle>
                        <circle class="pl__ring pl__ring--d" cx="155" cy="120" r="70" fill="none"></circle>
                    </svg>

                    <!-- Loading Text -->
                    <div class="text-white">
                        <h3 class="text-2xl font-bold mb-2">Processing your answer...</h3>
                        <p class="text-base opacity-80">Please wait a moment</p>
                    </div>
                </div>
            </div>

            <script>
                let currentQuestionIndex = {{ $currentQuestionIndex ?? 0 }};
                let questions = @json($questionDetails);
                let timeLimit = {{ $assessment->time_limit }}; // in minutes
                let timeRemaining = timeLimit * 60; // in seconds
                let timerInterval;
                let questionStartTime;
                
                // Initialize answered questions from server if available
                const serverAnsweredQuestions = @json($answeredQuestionIds ?? []);
                if (serverAnsweredQuestions.length > 0) {
                    sessionStorage.setItem('answeredQuestions', JSON.stringify(serverAnsweredQuestions));
                }

                // Initialize quiz
                document.addEventListener('DOMContentLoaded', function () {
                    loadQuestion(currentQuestionIndex);
                    startTimer();
                });

                function startTimer() {
                    timerInterval = setInterval(function () {
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
                    const progressPercentage = Math.round(((index + 1) / questions.length) * 100);
                    document.getElementById('progress-bar').style.width = `${progressPercentage}%`;
                    document.getElementById('progress-percentage').textContent = progressPercentage;

                    // Update navigation buttons
                    document.getElementById('next-btn').disabled = index === questions.length - 1;

                    // Show/hide complete button and next button
                    const isLastQuestion = index === questions.length - 1;
                    if (isLastQuestion) {
                        document.getElementById('next-btn').classList.add('hidden');
                        // Don't show complete section yet - wait for answer submission
                        document.getElementById('complete-section').classList.add('hidden');
                        document.getElementById('complete-section').classList.add('invisible');
                    } else {
                        document.getElementById('complete-section').classList.add('hidden');
                        document.getElementById('complete-section').classList.add('invisible');
                        document.getElementById('next-btn').classList.remove('hidden');
                    }

                    // Load question content
                    const container = document.getElementById('question-container');
                    container.innerHTML = generateQuestionHTML(question);

                    // Re-enable submit button for new question
                    const submitBtn = document.getElementById('submit-btn');
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');

                    // Clear any previous selections
                    clearSelections();

                    // Add event listeners for option selection
                    addOptionListeners();
                }

                function generateQuestionHTML(question) {
                    let html = `
                            <div class="mb-6">
                                <div class="flex items-start gap-3 mb-6">
                                    <div class="bg-indigo-100 text-indigo-700 font-bold text-sm px-3 py-1 rounded-lg flex-shrink-0">
                                        Q${currentQuestionIndex + 1}
                                    </div>
                                    <h2 class="text-gray-800 text-lg md:text-xl lg:text-2xl font-semibold leading-relaxed flex-1">
                                        ${question.question_text}
                                    </h2>
                                </div>
                        `;

                    if (question.question_type === 'multiple_choice') {
                        html += '<div class="space-y-3">';
                        const choices = [
                            { letter: 'A', text: question.choice_a },
                            { letter: 'B', text: question.choice_b },
                            { letter: 'C', text: question.choice_c },
                            { letter: 'D', text: question.choice_d }
                        ];

                        choices.forEach(choice => {
                            if (choice.text) {
                                html += `
                                        <label class="flex items-center p-4 bg-white border-2 rounded-xl cursor-pointer transition-all duration-200 option-label hover:border-indigo-300"
                                            style="border-color: #E2E8F0;">
                                            <input type="radio" name="answer" value="${choice.letter}" class="hidden">
                                            <div class="flex items-center justify-center w-9 h-9 rounded-xl font-bold text-sm mr-3 option-circle flex-shrink-0"
                                                style="background-color: #1E293B; color: white;">
                                                ${choice.letter}
                                            </div>
                                            <span class="text-gray-700 font-normal text-base option-text">
                                                ${choice.text}
                                            </span>
                                        </label>
                                    `;
                            }
                        });
                        html += '</div>';
                    } else if (question.question_type === 'true_false') {
                        html += `
                                <div class="space-y-3">
                                    <label class="flex items-center p-4 bg-white border-2 rounded-xl cursor-pointer transition-all duration-200 option-label hover:border-indigo-300"
                                        style="border-color: #E2E8F0;">
                                        <input type="radio" name="answer" value="True" class="hidden">
                                        <div class="flex items-center justify-center w-9 h-9 rounded-xl font-bold text-sm mr-3 option-circle flex-shrink-0"
                                            style="background-color: #1E293B; color: white;">
                                            T
                                        </div>
                                        <span class="text-gray-700 font-normal text-base option-text">True</span>
                                    </label>
                                    <label class="flex items-center p-4 bg-white border-2 rounded-xl cursor-pointer transition-all duration-200 option-label hover:border-indigo-300"
                                        style="border-color: #E2E8F0;">
                                        <input type="radio" name="answer" value="False" class="hidden">
                                        <div class="flex items-center justify-center w-9 h-9 rounded-xl font-bold text-sm mr-3 option-circle flex-shrink-0"
                                            style="background-color: #1E293B; color: white;">
                                            F
                                        </div>
                                        <span class="text-gray-700 font-normal text-base option-text">False</span>
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

                function addOptionListeners() {
                    const labels = document.querySelectorAll('.option-label');
                    labels.forEach(label => {
                        label.addEventListener('click', function () {
                            // Remove selection from all labels
                            document.querySelectorAll('.option-label').forEach(l => {
                                l.style.borderColor = '#E2E8F0';
                                l.style.backgroundColor = 'white';
                                const circle = l.querySelector('.option-circle');
                                if (circle) {
                                    circle.style.backgroundColor = '#1E293B';
                                }
                            });

                            // Apply selection to clicked label
                            this.style.borderColor = '#6366F1';
                            this.style.backgroundColor = '#EEF2FF';
                            const circle = this.querySelector('.option-circle');
                            if (circle) {
                                circle.style.backgroundColor = '#6366F1';
                            }

                            // Check the radio button
                            const radio = this.querySelector('input[type="radio"]');
                            if (radio) {
                                radio.checked = true;
                            }
                        });
                    });
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

                document.getElementById('next-btn').addEventListener('click', function () {
                    if (currentQuestionIndex < questions.length - 1) {
                        // Hide next button after clicking
                        this.classList.add('invisible');
                        this.classList.remove('visible');
                        loadQuestion(currentQuestionIndex + 1);
                    }
                });

                document.getElementById('submit-btn').addEventListener('click', function () {
                    const answer = getSelectedAnswer();
                    if (!answer) {
                        alert('Please select an answer before submitting.');
                        return;
                    }

                    submitAnswer(answer);
                });

                document.getElementById('complete-btn').addEventListener('click', function () {
                    // Last answer should already be submitted, just complete the quiz
                    completeQuiz();
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

                                // Disable submit button after submission
                                const submitBtn = document.getElementById('submit-btn');
                                submitBtn.disabled = true;
                                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');

                                // Check if this is the last question
                                const isCurrentlyLastQuestion = currentQuestionIndex === questions.length - 1;

                                if (isCurrentlyLastQuestion || data.is_last_question) {
                                    // On last question, show complete section instead of auto-completing
                                    console.log('Showing complete section - last question detected');
                                    const completeSection = document.getElementById('complete-section');
                                    completeSection.classList.remove('hidden');
                                    completeSection.classList.remove('invisible');
                                } else {
                                    // Show next button for non-last questions
                                    document.getElementById('next-btn').classList.remove('invisible');
                                    document.getElementById('next-btn').classList.add('visible');
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

                    // Show loading overlay
                    document.getElementById('loading-overlay').classList.remove('hidden');

                    // Clear timer and remove beforeunload warning
                    clearInterval(timerInterval);
                    window.removeEventListener('beforeunload', preventUnload);

                    // Clear session storage
                    sessionStorage.removeItem('answeredQuestions');

                    // Create and submit form to complete quiz
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `{{ route('teacher-assessments.complete', $assessment->id) }}`;
                    
                    // Add CSRF token
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    form.appendChild(csrfInput);
                    
                    // Append form to body and submit
                    document.body.appendChild(form);
                    form.submit();
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
</body>

</html>