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

// Get teacher sections and students data from server
const teacherSections = @json($teacherSections ?? []);
const studentsDataFromServer = @json($studentsData ?? []);

// Initialize students data by section
studentsDataFromServer.forEach(student => {
    if (!studentsData[student.section]) {
        studentsData[student.section] = [];
    }
    studentsData[student.section].push(student);
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
});
</script>
@endsection