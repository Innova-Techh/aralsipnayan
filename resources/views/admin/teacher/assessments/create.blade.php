@extends('admin.teacher.layouts.app')

@section('title', 'Aralsipnayan')

@section('content')
<div>
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center space-x-4">
                <a href="{{ route('teacher.assessments') }}" class="text-gray-600 hover:text-gray-800">
                    <span class="material-symbols-outlined">arrow_back</span>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Create New Assessment</h1>
                    <p class="text-gray-600 mt-1">Build a new assessment for your students</p>
                </div>
            </div>
        </div>

        <!-- Assessment Form -->
        <div class="bg-white rounded-lg shadow p-6">
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex">
                        <span class="material-symbols-outlined text-red-400 mr-2">error</span>
                        <div>
                            <h3 class="text-sm font-medium text-red-800">Please correct the following errors:</h3>
                            <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <form id="assessment-create-form" method="POST" action="{{ route('teacher.assessments.store') }}">
                @csrf
                <!-- Basic Information -->
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Assessment Title</label>
                            <input type="text" name="title" placeholder="Enter assessment title" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('title') border-red-500 @enderror"
                                   value="{{ old('title') }}" required>
                            @error('title')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                            <select name="category" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('category') border-red-500 @enderror" required>
                                <option value="">Select a category</option>
                                <option value="Number & Algebra" {{ old('category') == 'Number & Algebra' ? 'selected' : '' }}>Number & Algebra</option>
                                <option value="Measurement & Geometry" {{ old('category') == 'Measurement & Geometry' ? 'selected' : '' }}>Measurement & Geometry</option>
                                <option value="Data & Probability" {{ old('category') == 'Data & Probability' ? 'selected' : '' }}>Data & Probability</option>
                            </select>
                            @error('category')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea name="description" placeholder="Brief description of the assessment" rows="3"
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Assessment Settings -->
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Assessment Settings</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Number of Questions</label>
                            <input type="number" name="number_of_questions" value="{{ old('number_of_questions', 15) }}" min="5" max="50"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('number_of_questions') border-red-500 @enderror" required>
                            @error('number_of_questions')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Time Limit (minutes)</label>
                            <input type="number" name="time_limit" value="{{ old('time_limit', 30) }}" min="10" max="120"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('time_limit') border-red-500 @enderror" required>
                            @error('time_limit')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Difficulty Level</label>
                            <select name="difficulty" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('difficulty') border-red-500 @enderror" required>
                                <option value="Easy" {{ old('difficulty') == 'Easy' ? 'selected' : '' }}>Easy</option>
                                <option value="Medium" {{ old('difficulty') == 'Medium' ? 'selected' : '' }}>Medium</option>
                                <option value="Hard" {{ old('difficulty') == 'Hard' ? 'selected' : '' }}>Hard</option>
                                <option value="Mixed" {{ old('difficulty') == 'Mixed' ? 'selected' : '' }}>Mixed</option>
                            </select>
                            @error('difficulty')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Live Quiz Option -->
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Quiz Type</h2>
                    <div class="space-y-3">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_live_quiz" value="1" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" {{ old('is_live_quiz') ? 'checked' : '' }}>
                            <span class="ml-3 text-sm text-gray-700">Live Quiz (Still in Development)</span>
                        </label>
                    </div>
                </div>

                <!-- Section Assignment -->
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Assign to Sections (Optional)</h2>
                    <p class="text-sm text-gray-600 mb-4">Leave unselected to save as draft. Select sections to immediately assign the assessment.</p>
                    <div class="space-y-3">
                        @if(!empty($teacherSections))
                            @foreach($teacherSections as $section)
                                <label class="flex items-center">
                                    <input type="checkbox" name="sections[]" value="{{ $section }}" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" {{ in_array($section, old('sections', [])) ? 'checked' : '' }}>
                                    <span class="ml-3 text-sm text-gray-700">Section {{ $section }}</span>
                                </label>
                            @endforeach
                        @else
                            <p class="text-sm text-gray-500">No sections assigned to your account. Contact your administrator.</p>
                        @endif
                    </div>
                    
                    <!-- Pick Specific Students Option -->
                    <div class="mt-4">
                        <label class="flex items-center">
                            <input type="checkbox" id="pickSpecificStudents" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-3 text-sm text-gray-700">Pick specific students instead of entire sections</span>
                        </label>
                    </div>
                    
                    <!-- Student Selection (Hidden by default) -->
                    <div id="studentSelectionSection" class="mt-4 hidden">
                        <h3 class="text-md font-medium text-gray-900 mb-3">Select Students</h3>
                        <div class="space-y-4" id="studentSectionsContainer">
                            @if(!empty($teacherSections))
                                @foreach($teacherSections as $section)
                                    <div id="section{{ strtolower($section) }}Students" class="hidden">
                                        <h4 class="text-sm font-medium text-gray-700 mb-2">Section {{ $section }} Students</h4>
                                        <div class="max-h-32 overflow-y-auto border border-gray-200 rounded-lg p-3 bg-gray-50">
                                            <div id="section{{ strtolower($section) }}StudentsList"></div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        
                        <!-- Selected Students Summary -->
                        <div id="selectedStudentsSummary" class="mt-4 hidden">
                            <h4 class="text-sm font-medium text-gray-900 mb-2">Selected Students</h4>
                            <div id="selectedStudentsList" class="max-h-32 overflow-y-auto border border-gray-200 rounded-lg p-3 bg-blue-50">
                                <!-- Selected students will appear here -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Question Source -->
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Question Source</h2>
                    <div class="space-y-4">
                        <div class="flex space-x-4">
                            <label class="flex items-center">
                                <input type="radio" name="question_source" value="question_bank" class="mr-3 text-blue-600" checked>
                                <span class="text-sm text-gray-700">Use Question Bank</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="question_source" value="create_custom" class="mr-3 text-blue-600">
                                <span class="text-sm text-gray-700">Create Custom Questions</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="question_source" value="mixed" class="mr-3 text-blue-600">
                                <span class="text-sm text-gray-700">Mix Both</span>
                            </label>
                        </div>
                        
                        <!-- Question Bank Section -->
                        <div id="questionBankSection" class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                            <h3 class="text-md font-medium text-gray-900 mb-3">Browse Question Bank</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Topic</label>
                                    <select id="topicFilter" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                        <option value="">All Topics</option>
                                        <!-- Topics will be populated dynamically based on selected category -->
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Difficulty</label>
                                    <select id="difficultyFilter" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                        <option value="">All Difficulties</option>
                                        <option value="Easy">Beginner</option>
                                        <option value="Medium">Intermediate</option>
                                        <option value="Hard">Advanced</option>
                                    </select>
                                </div>
                                <div class="flex items-end">
                                    <button type="button" id="loadQuestions" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                                        Load Questions
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Question Bank Results -->
                            <div id="questionBankResults" class="max-h-96 overflow-y-auto border border-gray-200 rounded-lg bg-white">
                                <div class="p-4 text-center text-gray-500">
                                    Click "Load Questions" to browse available questions
                                </div>
                            </div>
                            
                            <!-- Selected Questions Summary -->
                            <div id="selectedQuestionsFromBank" class="mt-4 hidden">
                                <h4 class="text-sm font-medium text-gray-900 mb-2">Selected Questions from Bank (<span id="bankQuestionCount">0</span>)</h4>
                                <div id="selectedBankQuestionsList" class="max-h-32 overflow-y-auto border border-gray-200 rounded-lg p-3 bg-blue-50">
                                    <!-- Selected questions will appear here -->
                                </div>
                            </div>
                        </div>
                        
                        <!-- Custom Questions Section -->
                        <div id="customQuestionsSection" class="border border-gray-200 rounded-lg p-4 bg-gray-50 hidden">
                            <h3 class="text-md font-medium text-gray-900 mb-3">Create Custom Questions</h3>
                            
                            <!-- Question Type Selection -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Question Type</label>
                                <select id="customQuestionType" class="w-full md:w-1/3 border border-gray-300 rounded-lg px-3 py-2">
                                    <option value="multiple_choice">Multiple Choice</option>
                                    <option value="true_false">True/False</option>
                                    <option value="fill_blanks">Fill in the Blank</option>
                                </select>
                            </div>
                            
                            <!-- Custom Question Form -->
                            <div id="customQuestionForm" class="space-y-4 border border-gray-300 rounded-lg p-4 bg-white">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Question Text</label>
                                    <textarea id="customQuestionText" placeholder="Enter your question here..." rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2"></textarea>
                                </div>
                                
                                <!-- Multiple Choice Options -->
                                <div id="multipleChoiceOptions">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Answer Choices</label>
                                    <div class="space-y-2">
                                        <div class="flex items-center space-x-2">
                                            <input type="radio" name="customCorrectAnswer" value="A" class="text-blue-600">
                                            <span class="text-sm font-medium">A.</span>
                                            <input type="text" id="customChoiceA" placeholder="Choice A" class="flex-1 border border-gray-300 rounded px-3 py-1 text-sm">
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <input type="radio" name="customCorrectAnswer" value="B" class="text-blue-600">
                                            <span class="text-sm font-medium">B.</span>
                                            <input type="text" id="customChoiceB" placeholder="Choice B" class="flex-1 border border-gray-300 rounded px-3 py-1 text-sm">
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <input type="radio" name="customCorrectAnswer" value="C" class="text-blue-600">
                                            <span class="text-sm font-medium">C.</span>
                                            <input type="text" id="customChoiceC" placeholder="Choice C" class="flex-1 border border-gray-300 rounded px-3 py-1 text-sm">
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <input type="radio" name="customCorrectAnswer" value="D" class="text-blue-600">
                                            <span class="text-sm font-medium">D.</span>
                                            <input type="text" id="customChoiceD" placeholder="Choice D" class="flex-1 border border-gray-300 rounded px-3 py-1 text-sm">
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- True/False Options -->
                                <div id="trueFalseOptions" class="hidden">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Correct Answer</label>
                                    <div class="flex space-x-4">
                                        <label class="flex items-center">
                                            <input type="radio" name="customTrueFalseAnswer" value="True" class="mr-2 text-blue-600">
                                            <span class="text-sm">True</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" name="customTrueFalseAnswer" value="False" class="mr-2 text-blue-600">
                                            <span class="text-sm">False</span>
                                        </label>
                                    </div>
                                </div>
                                
                                <!-- Fill in the Blank Options -->
                                <div id="fillBlankOptions" class="hidden">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Correct Answer</label>
                                    <input type="text" id="customFillBlankAnswer" placeholder="Enter the correct answer" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                                    <p class="text-xs text-gray-500 mt-1">Use underscores (_____) in your question text to indicate where the blank should be.</p>
                                </div>
                                
                                <!-- Additional Fields -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Topic Tag</label>
                                        <input type="text" id="customTopicTag" placeholder="e.g., Basic Operations" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Points</label>
                                        <input type="number" id="customPoints" value="1" min="1" max="100" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Hint (Optional)</label>
                                    <textarea id="customHint" placeholder="Provide a helpful hint..." rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2"></textarea>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Explanation (Optional)</label>
                                    <textarea id="customExplanation" placeholder="Explain the correct answer..." rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2"></textarea>
                                </div>
                                
                                <div class="flex justify-end">
                                    <button type="button" id="addCustomQuestion" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                        Add Question
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Custom Questions List -->
                            <div id="customQuestionsList" class="mt-4 hidden">
                                <h4 class="text-sm font-medium text-gray-900 mb-2">Created Questions (<span id="customQuestionCount">0</span>)</h4>
                                <div id="customQuestionsContainer" class="space-y-3 max-h-64 overflow-y-auto">
                                    <!-- Custom questions will appear here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Schedule -->
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Schedule</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Available From</label>
                            <input type="datetime-local" name="available_from" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('available_from') border-red-500 @enderror"
                                   value="{{ old('available_from') }}">
                            @error('available_from')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Available Until</label>
                            <input type="datetime-local" name="available_until" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('available_until') border-red-500 @enderror"
                                   value="{{ old('available_until') }}">
                            @error('available_until')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Hidden field to carry selected students (CSV) -->
                <input type="hidden" name="selected_students" id="selected_students_field" value="">
                
                <!-- Hidden fields for questions -->
                <input type="hidden" name="selected_bank_questions" id="selected_bank_questions_field" value="">
                <input type="hidden" name="custom_questions" id="custom_questions_field" value="">

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('teacher.assessments') }}" 
                       class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Create Assessment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let selectedStudents = [];
let studentsData = {};
let selectedBankQuestions = [];
let customQuestions = [];
let questionIdCounter = 1;

// Get teacher sections and students data from server
const teacherSections = @json($teacherSections ?? []);
const studentsDataFromServer = @json($studentsData ?? []);
const availableTopics = @json($availableTopics ?? []);

// Initialize students data by section
studentsDataFromServer.forEach(student => {
    if (!studentsData[student.section]) {
        studentsData[student.section] = [];
    }
    studentsData[student.section].push(student);
});

// Update topic filter when category changes
document.querySelector('select[name="category"]').addEventListener('change', function() {
    updateTopicFilter(this.value);
});

function updateTopicFilter(category) {
    const topicFilter = document.getElementById('topicFilter');
    topicFilter.innerHTML = '<option value="">All Topics</option>';
    
    if (category && availableTopics[category]) {
        availableTopics[category].forEach(topic => {
            const option = document.createElement('option');
            option.value = topic;
            option.textContent = topic;
            topicFilter.appendChild(option);
        });
    }
}

// Initialize topic filter on page load
document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.querySelector('select[name="category"]');
    if (categorySelect.value) {
        updateTopicFilter(categorySelect.value);
    }
});

// Toggle specific students selection
document.getElementById('pickSpecificStudents').addEventListener('change', function() {
    const studentSelectionSection = document.getElementById('studentSelectionSection');
    if (this.checked) {
        studentSelectionSection.classList.remove('hidden');
        loadStudentsForSelectedSections();
    } else {
        studentSelectionSection.classList.add('hidden');
        selectedStudents = [];
        updateSelectedStudentsList();
    }
});

// Listen for section checkbox changes
document.querySelectorAll('input[name="sections[]"]').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        if (document.getElementById('pickSpecificStudents').checked) {
            loadStudentsForSelectedSections();
        }
    });
});

function loadStudentsForSelectedSections() {
    const selectedSections = Array.from(document.querySelectorAll('input[name="sections[]"]:checked'))
        .map(cb => cb.value);
    
    // Hide all section student lists dynamically
    const teacherSections = @json($teacherSections ?? []);
    teacherSections.forEach(section => {
        const sectionDiv = document.getElementById(`section${section.toLowerCase()}Students`);
        if (sectionDiv) {
            sectionDiv.classList.add('hidden');
        }
    });
    
    // Show and populate student lists for selected sections
    selectedSections.forEach(section => {
        const sectionId = section.toLowerCase();
        const studentsDiv = document.getElementById(`section${sectionId}Students`);
        const studentsListDiv = document.getElementById(`section${sectionId}StudentsList`);
        
        if (studentsData[section] && studentsData[section].length > 0) {
            studentsDiv.classList.remove('hidden');
            studentsListDiv.innerHTML = studentsData[section].map(student => `
                <label class="flex items-center p-2 hover:bg-gray-50 rounded">
                    <input type="checkbox" value="${student.user_id}" class="mr-3 rounded border-gray-300 text-blue-600 focus:ring-blue-500 student-checkbox">
                    <span class="text-sm">${student.firstname} ${student.lastname}</span>
                </label>
            `).join('');
            
            // Add event listeners to checkboxes
            studentsListDiv.querySelectorAll('.student-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', updateSelectedStudentsList);
            });
        }
    });
    
    updateSelectedStudentsList();
}

function updateSelectedStudentsList() {
    const selectedStudentIds = Array.from(document.querySelectorAll('.student-checkbox:checked'))
        .map(cb => cb.value);
    
    selectedStudents = [];
    Object.values(studentsData).flat().forEach(student => {
        if (selectedStudentIds.includes(student.user_id.toString())) {
            selectedStudents.push(student);
        }
    });
    
    const selectedStudentsSummary = document.getElementById('selectedStudentsSummary');
    const selectedStudentsList = document.getElementById('selectedStudentsList');
    
    if (selectedStudents.length > 0) {
        selectedStudentsSummary.classList.remove('hidden');
        selectedStudentsList.innerHTML = selectedStudents.map(student => `
            <div class="flex items-center justify-between py-1">
                <span class="text-sm text-gray-700">${student.firstname} ${student.lastname} (${student.section})</span>
                <button type="button" onclick="removeStudent('${student.user_id}')" class="text-red-600 hover:text-red-800 text-xs">Remove</button>
            </div>
        `).join('');
    } else {
        selectedStudentsSummary.classList.add('hidden');
    }
}

function removeStudent(userId) {
    const checkbox = document.querySelector(`input[value="${userId}"]`);
    if (checkbox) {
        checkbox.checked = false;
        updateSelectedStudentsList();
    }
}

// Keep a single hidden input up-to-date with selected student IDs (CSV)
function syncSelectedStudentsHiddenField() {
    const hidden = document.getElementById('selected_students_field');
    hidden.value = selectedStudents.map(s => s.user_id).join(',');
}

// Hook into our form explicitly (avoid binding to other forms like logout)
const createForm = document.getElementById('assessment-create-form');

// Ensure hidden input is synced whenever list changes
const __origUpdateSelectedStudentsList = updateSelectedStudentsList;
updateSelectedStudentsList = function() {
    __origUpdateSelectedStudentsList();
    syncSelectedStudentsHiddenField();
}

// Question Source Management
document.querySelectorAll('input[name="question_source"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const questionBankSection = document.getElementById('questionBankSection');
        const customQuestionsSection = document.getElementById('customQuestionsSection');
        
        if (this.value === 'question_bank') {
            questionBankSection.classList.remove('hidden');
            customQuestionsSection.classList.add('hidden');
        } else if (this.value === 'create_custom') {
            questionBankSection.classList.add('hidden');
            customQuestionsSection.classList.remove('hidden');
        } else if (this.value === 'mixed') {
            questionBankSection.classList.remove('hidden');
            customQuestionsSection.classList.remove('hidden');
        }
    });
});

// Custom Question Type Management
document.getElementById('customQuestionType').addEventListener('change', function() {
    const multipleChoiceOptions = document.getElementById('multipleChoiceOptions');
    const trueFalseOptions = document.getElementById('trueFalseOptions');
    const fillBlankOptions = document.getElementById('fillBlankOptions');
    
    // Hide all options first
    multipleChoiceOptions.classList.add('hidden');
    trueFalseOptions.classList.add('hidden');
    fillBlankOptions.classList.add('hidden');
    
    // Show relevant options
    if (this.value === 'multiple_choice') {
        multipleChoiceOptions.classList.remove('hidden');
    } else if (this.value === 'true_false') {
        trueFalseOptions.classList.remove('hidden');
    } else if (this.value === 'fill_blanks') {
        fillBlankOptions.classList.remove('hidden');
    }
});

// Load Questions from Bank
document.getElementById('loadQuestions').addEventListener('click', function() {
    const category = document.querySelector('select[name="category"]').value;
    const topicFilter = document.getElementById('topicFilter').value;
    const difficultyFilter = document.getElementById('difficultyFilter').value;
    
    if (!category) {
        alert('Please select a category first.');
        return;
    }
    
    // Show loading state
    const resultsContainer = document.getElementById('questionBankResults');
    resultsContainer.innerHTML = '<div class="p-4 text-center text-gray-500">Loading questions...</div>';
    
    // Make AJAX request to load questions
    fetch('/teacher/questions/load', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            category: category,
            topic: topicFilter,
            difficulty: difficultyFilter
        })
    })
    .then(response => response.json())
    .then(data => {
        displayQuestionBankResults(data.questions || []);
    })
    .catch(error => {
        console.error('Error loading questions:', error);
        resultsContainer.innerHTML = '<div class="p-4 text-center text-red-500">Error loading questions. Please try again.</div>';
    });
});

function displayQuestionBankResults(questions) {
    const resultsContainer = document.getElementById('questionBankResults');
    
    if (questions.length === 0) {
        resultsContainer.innerHTML = '<div class="p-4 text-center text-gray-500">No questions found matching your criteria.</div>';
        return;
    }
    
    const questionsHtml = questions.map(question => `
        <div class="border-b border-gray-200 p-4 hover:bg-gray-50">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center mb-2">
                        <input type="checkbox" value="${question.question_id}" class="mr-3 bank-question-checkbox">
                        <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">${question.difficulty_level}</span>
                        <span class="text-xs bg-gray-100 text-gray-800 px-2 py-1 rounded ml-2">${question.question_id}</span>
                        <span class="text-xs bg-gray-100 text-gray-800 px-2 py-1 rounded ml-2">${question.topic_tag}</span>
                    </div>
                    <p class="text-sm text-gray-900 mb-2">${question.question_text}</p>
                    ${question.question_type === 'multiple_choice' ? `
                        <div class="text-xs text-gray-600 space-y-1">
                            <div>A. ${question.choice_a}</div>
                            <div>B. ${question.choice_b}</div>
                            <div>C. ${question.choice_c}</div>
                            <div>D. ${question.choice_d}</div>
                            <div class="font-medium text-green-600">Correct: ${question.correct_answer}</div>
                        </div>
                    ` : `
                        <div class="text-xs text-gray-600">
                            <div class="font-medium text-green-600">Answer: ${question.correct_answer}</div>
                        </div>
                    `}
                </div>
            </div>
        </div>
    `).join('');
    
    resultsContainer.innerHTML = questionsHtml;
    
    // Add event listeners to checkboxes
    document.querySelectorAll('.bank-question-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedBankQuestions);
    });
}

function updateSelectedBankQuestions() {
    const checkedBoxes = document.querySelectorAll('.bank-question-checkbox:checked');
    selectedBankQuestions = Array.from(checkedBoxes).map(cb => cb.value);
    
    const summaryDiv = document.getElementById('selectedQuestionsFromBank');
    const countSpan = document.getElementById('bankQuestionCount');
    const listDiv = document.getElementById('selectedBankQuestionsList');
    
    countSpan.textContent = selectedBankQuestions.length;
    
    if (selectedBankQuestions.length > 0) {
        summaryDiv.classList.remove('hidden');
        listDiv.innerHTML = selectedBankQuestions.map(questionId => `
            <div class="flex items-center justify-between py-1">
                <span class="text-sm text-gray-700">${questionId}</span>
                <button type="button" onclick="removeBankQuestion('${questionId}')" class="text-red-600 hover:text-red-800 text-xs">Remove</button>
            </div>
        `).join('');
    } else {
        summaryDiv.classList.add('hidden');
    }
    
    // Update hidden field
    document.getElementById('selected_bank_questions_field').value = selectedBankQuestions.join(',');
}

function removeBankQuestion(questionId) {
    const checkbox = document.querySelector(`input[value="${questionId}"]`);
    if (checkbox) {
        checkbox.checked = false;
        updateSelectedBankQuestions();
    }
}

// Add Custom Question
document.getElementById('addCustomQuestion').addEventListener('click', function() {
    const questionType = document.getElementById('customQuestionType').value;
    const questionText = document.getElementById('customQuestionText').value.trim();
    const topicTag = document.getElementById('customTopicTag').value.trim();
    const points = document.getElementById('customPoints').value;
    const hint = document.getElementById('customHint').value.trim();
    const explanation = document.getElementById('customExplanation').value.trim();
    
    if (!questionText) {
        alert('Please enter the question text.');
        return;
    }
    
    let correctAnswer = '';
    let choices = {};
    
    if (questionType === 'multiple_choice') {
        const choiceA = document.getElementById('customChoiceA').value.trim();
        const choiceB = document.getElementById('customChoiceB').value.trim();
        const choiceC = document.getElementById('customChoiceC').value.trim();
        const choiceD = document.getElementById('customChoiceD').value.trim();
        const selectedAnswer = document.querySelector('input[name="customCorrectAnswer"]:checked');
        
        if (!choiceA || !choiceB || !choiceC || !choiceD) {
            alert('Please fill in all answer choices.');
            return;
        }
        
        if (!selectedAnswer) {
            alert('Please select the correct answer.');
            return;
        }
        
        choices = { A: choiceA, B: choiceB, C: choiceC, D: choiceD };
        correctAnswer = selectedAnswer.value;
    } else if (questionType === 'true_false') {
        const selectedAnswer = document.querySelector('input[name="customTrueFalseAnswer"]:checked');
        if (!selectedAnswer) {
            alert('Please select the correct answer (True or False).');
            return;
        }
        correctAnswer = selectedAnswer.value;
    } else if (questionType === 'fill_blanks') {
        correctAnswer = document.getElementById('customFillBlankAnswer').value.trim();
        if (!correctAnswer) {
            alert('Please enter the correct answer for the fill-in-the-blank question.');
            return;
        }
    }
    
    const customQuestion = {
        id: 'custom_' + questionIdCounter++,
        question_type: questionType,
        question_text: questionText,
        choices: choices,
        correct_answer: correctAnswer,
        topic_tag: topicTag || 'Custom',
        base_points: parseInt(points),
        hint_text: hint,
        explanation: explanation
    };
    
    customQuestions.push(customQuestion);
    displayCustomQuestions();
    clearCustomQuestionForm();
    
    // Update hidden field
    document.getElementById('custom_questions_field').value = JSON.stringify(customQuestions);
});

function displayCustomQuestions() {
    const listDiv = document.getElementById('customQuestionsList');
    const countSpan = document.getElementById('customQuestionCount');
    const containerDiv = document.getElementById('customQuestionsContainer');
    
    countSpan.textContent = customQuestions.length;
    
    if (customQuestions.length > 0) {
        listDiv.classList.remove('hidden');
        containerDiv.innerHTML = customQuestions.map((question, index) => `
            <div class="border border-gray-200 rounded-lg p-3 bg-white">
                <div class="flex items-start justify-between mb-2">
                    <div class="flex-1">
                        <div class="flex items-center mb-2">
                            <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">${question.question_type.replace('_', ' ')}</span>
                            <span class="text-xs bg-gray-100 text-gray-800 px-2 py-1 rounded ml-2">${question.topic_tag}</span>
                            <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded ml-2">${question.base_points} pts</span>
                        </div>
                        <p class="text-sm text-gray-900 mb-2">${question.question_text}</p>
                        ${question.question_type === 'multiple_choice' ? `
                            <div class="text-xs text-gray-600 space-y-1">
                                <div>A. ${question.choices.A}</div>
                                <div>B. ${question.choices.B}</div>
                                <div>C. ${question.choices.C}</div>
                                <div>D. ${question.choices.D}</div>
                                <div class="font-medium text-green-600">Correct: ${question.correct_answer}</div>
                            </div>
                        ` : `
                            <div class="text-xs text-gray-600">
                                <div class="font-medium text-green-600">Answer: ${question.correct_answer}</div>
                            </div>
                        `}
                    </div>
                    <button type="button" onclick="removeCustomQuestion(${index})" class="text-red-600 hover:text-red-800 text-sm ml-2">
                        Remove
                    </button>
                </div>
            </div>
        `).join('');
    } else {
        listDiv.classList.add('hidden');
    }
}

function removeCustomQuestion(index) {
    customQuestions.splice(index, 1);
    displayCustomQuestions();
    document.getElementById('custom_questions_field').value = JSON.stringify(customQuestions);
}

function clearCustomQuestionForm() {
    document.getElementById('customQuestionText').value = '';
    document.getElementById('customChoiceA').value = '';
    document.getElementById('customChoiceB').value = '';
    document.getElementById('customChoiceC').value = '';
    document.getElementById('customChoiceD').value = '';
    document.getElementById('customFillBlankAnswer').value = '';
    document.getElementById('customTopicTag').value = '';
    document.getElementById('customPoints').value = '10';
    document.getElementById('customHint').value = '';
    document.getElementById('customExplanation').value = '';
    
    // Clear radio buttons
    document.querySelectorAll('input[name="customCorrectAnswer"]').forEach(radio => radio.checked = false);
    document.querySelectorAll('input[name="customTrueFalseAnswer"]').forEach(radio => radio.checked = false);
}

// Validate and finalize payload on submit
createForm.addEventListener('submit', function(e) {
    const pickingSpecific = document.getElementById('pickSpecificStudents').checked;
    if (pickingSpecific) {
        if (selectedStudents.length === 0) {
            e.preventDefault();
            alert('Please select at least one student or uncheck "Pick specific students".');
            return;
        }
        // When targeting specific students, do not submit sections
        document.querySelectorAll('input[name="sections[]"]').forEach(checkbox => {
            checkbox.checked = false;
        });
        syncSelectedStudentsHiddenField();
    }
    
    // Validate questions
    const questionSource = document.querySelector('input[name="question_source"]:checked').value;
    const totalQuestions = selectedBankQuestions.length + customQuestions.length;
    const requiredQuestions = parseInt(document.querySelector('input[name="number_of_questions"]').value);
    
    if (questionSource === 'question_bank' && selectedBankQuestions.length === 0) {
        e.preventDefault();
        alert('Please select questions from the question bank or change the question source.');
        return;
    }
    
    if (questionSource === 'create_custom' && customQuestions.length === 0) {
        e.preventDefault();
        alert('Please create at least one custom question or change the question source.');
        return;
    }
    
    if (questionSource === 'mixed' && totalQuestions === 0) {
        e.preventDefault();
        alert('Please select questions from the bank or create custom questions.');
        return;
    }
    
    if (totalQuestions < requiredQuestions) {
        e.preventDefault();
        alert(`You have selected/created ${totalQuestions} questions, but the assessment requires ${requiredQuestions} questions. Please add more questions or reduce the number of questions required.`);
        return;
    }
});
</script>
@endsection