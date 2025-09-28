@extends('admin.teacher.layouts.app')

@section('title', 'Aralsipnayan')

@section('content')
<div>
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Assessment Management</h1>
            <p class="text-gray-600 mt-1">Create and manage assessments for your students</p>
        </div>
        <a href="{{ route('teacher.assessments.create') }}" 
           class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center">
            <span class="material-symbols-outlined mr-2">add</span>
            New Assessment
        </a>
    </div>

    <!-- Assessment Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($assessments as $assessment)
            <div class="bg-white rounded-lg shadow p-6 border-l-4 
                @if($assessment->status === 'Active') border-green-500
                @elseif($assessment->status === 'Draft') border-yellow-500
                @elseif($assessment->status === 'Completed') border-blue-500
                @else border-gray-500 @endif">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">{{ $assessment->title }}</h3>
                    <span class="
                        @if($assessment->status === 'Active') bg-green-100 text-green-800
                        @elseif($assessment->status === 'Draft') bg-yellow-100 text-yellow-800
                        @elseif($assessment->status === 'Completed') bg-blue-100 text-blue-800
                        @else bg-gray-100 text-gray-800 @endif
                        text-xs font-medium px-2.5 py-0.5 rounded-full">
                        {{ $assessment->status }}
                    </span>
                </div>
                <div class="space-y-2 text-sm text-gray-600 mb-4">
                    <p><span class="font-medium">Category:</span> {{ $assessment->category }}</p>
                    <p><span class="font-medium">Questions:</span> {{ $assessment->number_of_questions }}</p>
                    <p><span class="font-medium">Time Limit:</span> {{ $assessment->time_limit }} mins</p>
                    <p><span class="font-medium">Difficulty:</span> {{ $assessment->difficulty }}</p>
                    @if($assessment->is_live_quiz)
                        <p><span class="font-medium text-blue-600">🔴 Live Quiz</span></p>
                    @endif
                    <p><span class="font-medium">Assigned to:</span>            
                        @if($assessment->assignments->count() > 0)
                            @php
                                $specificStudents = $assessment->assignments->where('student_id', '!=', null);
                                $sectionAssignments = $assessment->assignments->where('student_id', null);
                            @endphp
                            @if($specificStudents->count() > 0)
                                @php
                                    $names = [];
                                    foreach($specificStudents as $assignment) {
                                        if ($assignment->student && $assignment->student->studentProfile) {
                                            $firstname = $assignment->student->studentProfile->firstname ?? '';
                                            $lastname = $assignment->student->studentProfile->lastname ?? '';
                                            $fullName = trim($firstname . ' ' . $lastname);
                                            $names[] = $fullName ?: 'Student ID: ' . $assignment->student_id;
                                        } else {
                                            $names[] = 'Student ID: ' . $assignment->student_id;
                                        }
                                    }
                                @endphp
                                {{ implode(', ', $names) }}
                            @elseif($sectionAssignments->count() > 0)
                                Section {{ $sectionAssignments->pluck('section')->unique()->filter()->implode(', ') }}
                            @else
                                Mixed assignments
                            @endif
                        @else
                            Not assigned (Debug: {{ $assessment->assignments->count() }} assignments found)
                        @endif
                    </p>
                    <p><span class="font-medium">Responses:</span> 
                        @php
                            $completedCount = $assessment->assignments->where('status', 'Completed')->count();
                            $totalStudents = 0;
                            
                            // Count students based on assignment type
                            if ($assessment->assignments->where('student_id', '!=', null)->count() > 0) {
                                // Specific students assigned
                                $totalStudents = $assessment->assignments->where('student_id', '!=', null)->count();
                            } else {
                                // Section-wide assignment
                                $sections = $assessment->assignments->pluck('section')->unique()->filter();
                                foreach ($sections as $section) {
                                    $totalStudents += collect($studentsData)->where('section', $section)->count();
                                }
                            }
                        @endphp
                        {{ $completedCount }}/{{ $totalStudents }}
                    </p>
                </div>
                <div class="flex space-x-2">
                    @if($assessment->status === 'Active' && $assessment->assignments->count() > 0)
                        <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">View Results</button>
                    @elseif($assessment->status === 'Draft')
                        <button onclick="openAssignModal({{ $assessment->id }})" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Assign</button>
                    @endif
                    <button onclick="openEditModal({{ $assessment->id }}, '{{ $assessment->title }}', '{{ $assessment->description }}', '{{ $assessment->category }}', {{ $assessment->number_of_questions }}, {{ $assessment->time_limit }}, '{{ $assessment->difficulty }}', {{ $assessment->is_live_quiz ? 'true' : 'false' }}, '{{ $assessment->available_from }}', '{{ $assessment->available_until }}')" class="text-gray-600 hover:text-gray-800 text-sm font-medium">Edit</button>
                    @if($assessment->status === 'Completed')
                        <button class="text-gray-600 hover:text-gray-800 text-sm font-medium">Archive</button>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <span class="material-symbols-outlined text-6xl text-gray-300 mb-4">quiz</span>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No assessments yet</h3>
                <p class="text-gray-500 mb-4">Create your first assessment to get started</p>
                <a href="{{ route('teacher.assessments.create') }}" 
                   class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    Create Assessment
                </a>
            </div>
        @endforelse
    </div>
</div>

<!-- Edit Assessment Modal -->
<div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-10 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-2/3 xl:w-1/2 shadow-lg rounded-md bg-white">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-4 bg-blue-50 border-b border-blue-200 rounded-t-lg">
            <div class="flex items-center">
                <span class="material-symbols-outlined text-blue-600 mr-2">edit</span>
                <h3 class="text-lg font-semibold text-gray-900">Edit Assessment</h3>
            </div>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6">
            <form id="editAssessmentForm" method="POST">
                @csrf
                @method('PUT')
                
                <!-- Basic Information -->
                <div class="mb-6">
                    <h4 class="text-md font-semibold text-gray-900 mb-4">Basic Information</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Assessment Title</label>
                            <input type="text" name="title" id="edit_title" placeholder="Enter assessment title" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                            <select name="category" id="edit_category" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                <option value="">Select a category</option>
                                <option value="Number & Algebra">Number & Algebra</option>
                                <option value="Measurement & Geometry">Measurement & Geometry</option>
                                <option value="Data & Probability">Data & Probability</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea name="description" id="edit_description" placeholder="Brief description of the assessment" rows="3"
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Assessment Settings -->
                <div class="mb-6">
                    <h4 class="text-md font-semibold text-gray-900 mb-4">Assessment Settings</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Number of Questions</label>
                            <input type="number" name="number_of_questions" id="edit_number_of_questions" min="5" max="50"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Time Limit (minutes)</label>
                            <input type="number" name="time_limit" id="edit_time_limit" min="10" max="120"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Difficulty Level</label>
                            <select name="difficulty" id="edit_difficulty" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                <option value="Easy">Easy</option>
                                <option value="Medium">Medium</option>
                                <option value="Hard">Hard</option>
                                <option value="Mixed">Mixed</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Live Quiz Option -->
                <div class="mb-6">
                    <h4 class="text-md font-semibold text-gray-900 mb-4">Quiz Type</h4>
                    <div class="space-y-3">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_live_quiz" id="edit_is_live_quiz" value="1" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-3 text-sm text-gray-700">Live Quiz (Still in Development)</span>
                        </label>
                    </div>
                </div>

                <!-- Schedule -->
                <div class="mb-6">
                    <h4 class="text-md font-semibold text-gray-900 mb-4">Schedule</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Available From</label>
                            <input type="datetime-local" name="available_from" id="edit_available_from" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Available Until</label>
                            <input type="datetime-local" name="available_until" id="edit_available_until" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

            </form>
        </div>

        <!-- Assignment Management Section -->
        <div class="p-6 border-t border-gray-200">
            <h4 class="text-md font-semibold text-gray-900 mb-4">Assignment Management</h4>
            
            <!-- Current Assignments -->
            <div id="currentAssignments" class="mb-4">
                <h5 class="text-sm font-medium text-gray-700 mb-2">Current Assignments</h5>
                <div id="currentAssignmentsList" class="max-h-32 overflow-y-auto border border-gray-200 rounded-lg p-3 bg-gray-50">
                    <!-- Current assignments will be populated here -->
                </div>
            </div>
            
            <!-- Add New Assignment -->
            <div class="border-t pt-4">
                <h5 class="text-sm font-medium text-gray-700 mb-4">Add New Assignment</h5>
                
                <!-- Section Selection -->
                <div class="mb-4">
                    <h6 class="text-sm font-medium text-gray-700 mb-2">Select Sections</h6>
                    <div class="space-y-2" id="editSectionCheckboxes">
                        <!-- Sections will be populated via JavaScript -->
                    </div>
                    <div class="mt-3">
                        <label class="flex items-center">
                            <input type="checkbox" id="editPickSpecificStudents" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-700">Pick specific students instead of entire sections</span>
                        </label>
                    </div>
                </div>
                
                <!-- Student Selection (Hidden by default) -->
                <div id="editStudentSelectionSection" class="mb-4 hidden">
                    <h6 class="text-sm font-medium text-gray-700 mb-2">Select Students</h6>
                    <div class="space-y-4" id="editStudentSectionsContainer">
                        <!-- Student sections will be populated here -->
                    </div>
                    
                    <!-- Selected Students Summary -->
                    <div id="editSelectedStudentsSummary" class="mt-4 hidden">
                        <h6 class="text-sm font-medium text-gray-700 mb-2">Selected Students</h6>
                        <div id="editSelectedStudentsList" class="max-h-32 overflow-y-auto border border-gray-200 rounded-lg p-3 bg-blue-50">
                            <!-- Selected students will appear here -->
                        </div>
                    </div>
                </div>
                
                <!-- Accommodations -->
                <div class="mb-4">
                    <label class="flex items-center">
                        <input type="checkbox" id="editAccommodations" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">Set up Accommodations</span>
                    </label>
                </div>
                
                <!-- Add Assignment Button -->
                <button type="button" onclick="addEditAssignment()" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    Add Assignment
                </button>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="flex justify-between items-center p-4 bg-gray-50 border-t border-gray-200 rounded-b-lg">
            <div class="text-sm text-gray-600">
                <span class="font-medium">Note:</span> Assignment management is separate from assessment details
            </div>
            <div class="flex space-x-3">
                <button onclick="closeEditModal()" class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    Cancel
                </button>
                <button onclick="submitEditForm()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Update Assessment
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Assign Modal -->
<div id="assignModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-4 bg-pink-50 border-b border-pink-200 rounded-t-lg">
            <div class="flex items-center">
                <span class="material-symbols-outlined text-pink-600 mr-2">person</span>
                <h3 class="text-lg font-semibold text-gray-900">Assign to a class</h3>
            </div>
            <button onclick="closeAssignModal()" class="text-gray-400 hover:text-gray-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6">
            <!-- Search Bar -->
            <div class="flex items-center mb-6">
                <div class="relative flex-1">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">search</span>
                    <input type="text" id="classSearch" placeholder="Search for a class" 
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                </div>
                <button class="ml-4 bg-pink-600 text-white px-4 py-2 rounded-lg hover:bg-pink-700 flex items-center">
                    <span class="material-symbols-outlined mr-2">open_in_new</span>
                    Manage classes
                </button>
            </div>

            <!-- Class List -->
            <div class="max-h-64 overflow-y-auto border border-gray-200 rounded-lg">
                <div id="classList" class="divide-y divide-gray-200">
                    <!-- Classes will be populated here -->
                </div>
            </div>

            <!-- Selected Students Section -->
            <div id="selectedStudentsSection" class="mt-6 hidden">
                <h4 class="text-sm font-medium text-gray-900 mb-3">Selected Students</h4>
                <div id="selectedStudentsList" class="max-h-32 overflow-y-auto border border-gray-200 rounded-lg p-3 bg-gray-50">
                    <!-- Selected students will appear here -->
                </div>
            </div>

            <!-- Accommodations -->
            <div class="mt-6">
                <label class="flex items-center">
                    <input type="checkbox" id="accommodations" class="rounded border-gray-300 text-pink-600 shadow-sm focus:border-pink-300 focus:ring focus:ring-pink-200 focus:ring-opacity-50" checked>
                    <span class="ml-2 text-sm text-gray-700">Set up Accommodations</span>
                </label>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="flex justify-end space-x-3 p-4 bg-gray-50 border-t border-gray-200 rounded-b-lg">
            <button onclick="closeAssignModal()" class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                Cancel
            </button>
            <button onclick="confirmAssignment()" class="px-4 py-2 bg-pink-600 text-white rounded-lg hover:bg-pink-700">
                Next
            </button>
        </div>
    </div>
</div>

<script>
let selectedClass = null;
let selectedStudents = [];
let currentAssessmentId = null;
let currentEditAssessmentId = null;

// Get teacher sections and students data
const teacherSections = @json($teacherSections ?? []);
const studentsData = @json($studentsData ?? []);

console.log('Teacher sections from server:', teacherSections);
console.log('Students data from server:', studentsData);
console.log('Students data length:', studentsData.length);
console.log('Students data structure:', studentsData.map(s => ({ id: s.user_id, name: s.firstname + ' ' + s.lastname, section: s.section })));

// Edit Modal Functions
function openEditModal(assessmentId, title, description, category, numberOfQuestions, timeLimit, difficulty, isLiveQuiz, availableFrom, availableUntil) {
    currentEditAssessmentId = assessmentId;
    
    // Populate form fields
    document.getElementById('edit_title').value = title;
    document.getElementById('edit_description').value = description || '';
    document.getElementById('edit_category').value = category;
    document.getElementById('edit_number_of_questions').value = numberOfQuestions;
    document.getElementById('edit_time_limit').value = timeLimit;
    document.getElementById('edit_difficulty').value = difficulty;
    document.getElementById('edit_is_live_quiz').checked = isLiveQuiz === 'true';
    
    // Handle datetime fields
    if (availableFrom && availableFrom !== 'null') {
        const fromDate = new Date(availableFrom);
        document.getElementById('edit_available_from').value = fromDate.toISOString().slice(0, 16);
    } else {
        document.getElementById('edit_available_from').value = '';
    }
    
    if (availableUntil && availableUntil !== 'null') {
        const untilDate = new Date(availableUntil);
        document.getElementById('edit_available_until').value = untilDate.toISOString().slice(0, 16);
    } else {
        document.getElementById('edit_available_until').value = '';
    }
    
    // Set form action
    document.getElementById('editAssessmentForm').action = `/teacher/assessments/${assessmentId}`;
    
    // Load current assignments
    loadCurrentAssignments(assessmentId);
    
    // Populate sections dropdown
    populateEditSectionsDropdown();
    
    document.getElementById('editModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
    currentEditAssessmentId = null;
}

function submitEditForm() {
    // Validate form before submission
    const form = document.getElementById('editAssessmentForm');
    const title = form.querySelector('input[name="title"]').value;
    const category = form.querySelector('select[name="category"]').value;
    const numberOfQuestions = form.querySelector('input[name="number_of_questions"]').value;
    const timeLimit = form.querySelector('input[name="time_limit"]').value;
    const difficulty = form.querySelector('select[name="difficulty"]').value;
    
    if (!title || !category || !numberOfQuestions || !timeLimit || !difficulty) {
        alert('Please fill in all required fields');
        return;
    }
    
    // Submit the form
    form.submit();
    
    // Show success message and close modal after a short delay
    setTimeout(() => {
        alert('Assessment updated successfully!');
        closeEditModal();
        // Refresh the page to show updated data
        location.reload();
    }, 1000);
}

// Assignment Management Functions for Edit Modal
function loadCurrentAssignments(assessmentId) {
    // Fetch current assignments for this assessment
    fetch(`/teacher/assessments/${assessmentId}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
        .then(response => response.json())
        .then(data => {
            displayCurrentAssignments(data.assignments || []);
        })
        .catch(error => {
            console.error('Error loading assignments:', error);
            document.getElementById('currentAssignmentsList').innerHTML = '<p class="text-gray-500 text-sm">Error loading assignments</p>';
        });
}

function displayCurrentAssignments(assignments) {
    const container = document.getElementById('currentAssignmentsList');
    
    console.log('Displaying assignments:', assignments);
    
    // Debug: Log each assignment to see the structure
    assignments.forEach((assignment, index) => {
        console.log(`Assignment ${index}:`, assignment);
        if (assignment.student) {
            console.log(`Assignment ${index} student:`, assignment.student);
            if (assignment.student.studentProfile) {
                console.log(`Assignment ${index} student profile:`, assignment.student.studentProfile);
            }
        }
    });
    
    if (assignments.length === 0) {
        container.innerHTML = '<p class="text-gray-500 text-sm">No assignments yet</p>';
        return;
    }
    
    const assignmentsHtml = assignments.map(assignment => {
        let assignmentText = '';
        if (assignment.student_id) {
            // Handle specific student assignment
            let studentName = `Student ID: ${assignment.student_id}`;
            
            // Try different ways to get student name
            if (assignment.student?.student_profile) {
                // Check if it's student_profile (snake_case)
                const profile = assignment.student.student_profile;
                if (profile.full_name) {
                    studentName = profile.full_name;
                } else {
                    const firstName = profile.firstname || '';
                    const lastName = profile.lastname || '';
                    if (firstName || lastName) {
                        studentName = `${firstName} ${lastName}`.trim();
                    }
                }
            } else if (assignment.student?.studentProfile) {
                // Check if it's studentProfile (camelCase)
                const profile = assignment.student.studentProfile;
                if (profile.full_name) {
                    studentName = profile.full_name;
                } else {
                    const firstName = profile.firstname || '';
                    const lastName = profile.lastname || '';
                    if (firstName || lastName) {
                        studentName = `${firstName} ${lastName}`.trim();
                    }
                }
            } else if (assignment.student) {
                // Check if student data is directly on the user object
                const student = assignment.student;
                const firstName = student.firstname || '';
                const lastName = student.lastname || '';
                if (firstName || lastName) {
                    studentName = `${firstName} ${lastName}`.trim();
                }
            }
            
            assignmentText = `Student: ${studentName}`;
        } else if (assignment.section) {
            assignmentText = `Section: ${assignment.section}`;
        } else {
            assignmentText = `Assignment ID: ${assignment.id}`;
        }
        
        return `
            <div class="flex items-center justify-between py-2 border-b border-gray-200 last:border-b-0">
                <span class="text-sm text-gray-700">${assignmentText}</span>
                <button onclick="removeEditAssignment(${assignment.id})" class="text-red-600 hover:text-red-800 text-xs">Remove</button>
            </div>
        `;
    }).join('');
    
    container.innerHTML = assignmentsHtml;
}

function populateEditSectionsDropdown() {
    const container = document.getElementById('editSectionCheckboxes');
    container.innerHTML = '';
    
    teacherSections.forEach(section => {
        const sectionDiv = document.createElement('div');
        sectionDiv.className = 'flex items-center';
        sectionDiv.innerHTML = `
            <label class="flex items-center">
                <input type="checkbox" value="${section}" class="mr-3 rounded border-gray-300 text-blue-600 focus:ring-blue-500 edit-section-checkbox">
                <span class="text-sm text-gray-700">Section ${section}</span>
            </label>
        `;
        container.appendChild(sectionDiv);
    });
    
    // Add event listeners for section checkboxes
    container.querySelectorAll('.edit-section-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', handleEditSectionChange);
    });
    
    // Add event listener for "Pick specific students" checkbox
    document.getElementById('editPickSpecificStudents').addEventListener('change', handleEditPickSpecificStudents);
}

function handleEditSectionChange() {
    if (document.getElementById('editPickSpecificStudents').checked) {
        loadEditStudentsForSelectedSections();
    }
}

function handleEditPickSpecificStudents() {
    const studentSelectionSection = document.getElementById('editStudentSelectionSection');
    if (this.checked) {
        studentSelectionSection.classList.remove('hidden');
        loadEditStudentsForSelectedSections();
    } else {
        studentSelectionSection.classList.add('hidden');
        window.editSelectedStudents = [];
        updateEditSelectedStudentsList();
    }
}

function loadEditStudentsForSelectedSections() {
    const selectedSections = Array.from(document.querySelectorAll('.edit-section-checkbox:checked'))
        .map(cb => cb.value);
    
    console.log('Selected sections:', selectedSections);
    console.log('Available students data:', studentsData);
    
    const container = document.getElementById('editStudentSectionsContainer');
    container.innerHTML = '';
    
    // Hide all section student lists
    teacherSections.forEach(section => {
        const sectionDiv = document.getElementById(`editSection${section}Students`);
        if (sectionDiv) {
            sectionDiv.classList.add('hidden');
        }
    });
    
    // Show and populate student lists for selected sections
    selectedSections.forEach(section => {
        const sectionDiv = document.createElement('div');
        sectionDiv.id = `editSection${section}Students`;
        sectionDiv.innerHTML = `
            <h6 class="text-sm font-medium text-gray-600 mb-2">Section ${section} Students</h6>
            <div class="max-h-32 overflow-y-auto border border-gray-200 rounded-lg p-3 bg-gray-50">
                <div id="editSection${section}StudentsList"></div>
            </div>
        `;
        container.appendChild(sectionDiv);
        
        // Populate students for this section
        const studentsInSection = studentsData.filter(student => student.section === section);
        if (studentsInSection.length > 0) {
            const studentsListDiv = document.getElementById(`editSection${section}StudentsList`);
            studentsListDiv.innerHTML = studentsInSection.map(student => `
                <label class="flex items-center p-2 hover:bg-gray-50 rounded">
                    <input type="checkbox" value="${student.user_id}" class="mr-3 rounded border-gray-300 text-blue-600 focus:ring-blue-500 edit-student-checkbox">
                    <span class="text-sm">${student.firstname} ${student.lastname}</span>
                </label>
            `).join('');
            
            // Add event listeners to checkboxes
            studentsListDiv.querySelectorAll('.edit-student-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', updateEditSelectedStudentsList);
            });
        } else {
            const studentsListDiv = document.getElementById(`editSection${section}StudentsList`);
            studentsListDiv.innerHTML = '<p class="text-gray-500 text-sm">No students in this section</p>';
        }
    });
    
    updateEditSelectedStudentsList();
}

function updateEditSelectedStudentsList() {
    const selectedStudentIds = Array.from(document.querySelectorAll('.edit-student-checkbox:checked'))
        .map(cb => cb.value);
    
    window.editSelectedStudents = [];
    studentsData.forEach(student => {
        if (selectedStudentIds.includes(student.user_id.toString())) {
            window.editSelectedStudents.push(student);
        }
    });
    
    const selectedStudentsSummary = document.getElementById('editSelectedStudentsSummary');
    const selectedStudentsList = document.getElementById('editSelectedStudentsList');
    
    if (window.editSelectedStudents.length > 0) {
        selectedStudentsSummary.classList.remove('hidden');
        selectedStudentsList.innerHTML = window.editSelectedStudents.map(student => `
            <div class="flex items-center justify-between py-1">
                <span class="text-sm text-gray-700">${student.firstname} ${student.lastname} (${student.section})</span>
                <button type="button" onclick="removeEditStudent('${student.user_id}')" class="text-red-600 hover:text-red-800 text-xs">Remove</button>
            </div>
        `).join('');
    } else {
        selectedStudentsSummary.classList.add('hidden');
    }
}

function removeEditStudent(userId) {
    const checkbox = document.querySelector(`input[value="${userId}"]`);
    if (checkbox) {
        checkbox.checked = false;
        updateEditSelectedStudentsList();
    }
}


function addEditAssignment() {
    const accommodations = document.getElementById('editAccommodations').checked;
    const pickSpecificStudents = document.getElementById('editPickSpecificStudents').checked;
    
    if (pickSpecificStudents) {
        // Handle specific student selection
        const studentIds = window.editSelectedStudents ? window.editSelectedStudents.map(s => s.user_id) : [];
        
        if (studentIds.length === 0) {
            alert('Please select at least one student');
            return;
        }
        
        const assignmentData = {
            assessment_id: currentEditAssessmentId,
            section: null,
            student_ids: studentIds,
            accommodations: accommodations,
            _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        };
        
        sendEditAssignment(assignmentData);
    } else {
        // Handle section assignment
        const selectedSections = Array.from(document.querySelectorAll('.edit-section-checkbox:checked'))
            .map(cb => cb.value);
        
        if (selectedSections.length === 0) {
            alert('Please select at least one section');
            return;
        }
        
        // Create assignments for each selected section
        selectedSections.forEach(section => {
            const assignmentData = {
                assessment_id: currentEditAssessmentId,
                section: section,
                student_ids: [],
                accommodations: accommodations,
                _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            };
            
            sendEditAssignment(assignmentData);
        });
    }
}

function sendEditAssignment(assignmentData) {
    console.log('Sending assignment data:', assignmentData);
    
    // Send assignment data to the server
    fetch('/teacher/assessments/assign', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(assignmentData)
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        // Check if response is JSON
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            return response.json();
        } else {
            // If not JSON, get text to see what was returned
            return response.text().then(text => {
                console.error('Non-JSON response:', text);
                throw new Error('Server returned non-JSON response');
            });
        }
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            alert('Assignment added successfully!');
            // Reload current assignments
            loadCurrentAssignments(currentEditAssessmentId);
            // Clear form
            clearEditAssignmentForm();
        } else {
            let errorMessage = data.message || 'Failed to add assignment';
            if (data.errors) {
                errorMessage += '\n\nValidation errors:\n';
                Object.keys(data.errors).forEach(field => {
                    errorMessage += `- ${field}: ${data.errors[field].join(', ')}\n`;
                });
            }
            alert('Error: ' + errorMessage);
        }
    })
    .catch(error => {
        console.error('Error adding assignment:', error);
        alert('Error adding assignment: ' + error.message);
    });
}

function clearEditAssignmentForm() {
    // Clear section checkboxes
    document.querySelectorAll('.edit-section-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
    
    // Clear specific students checkbox
    document.getElementById('editPickSpecificStudents').checked = false;
    
    // Hide student selection section
    document.getElementById('editStudentSelectionSection').classList.add('hidden');
    
    // Clear selected students
    window.editSelectedStudents = [];
    document.getElementById('editSelectedStudentsSummary').classList.add('hidden');
    
    // Clear accommodations
    document.getElementById('editAccommodations').checked = false;
}

function removeEditAssignment(assignmentId) {
    if (confirm('Are you sure you want to remove this assignment?')) {
        fetch(`/teacher/assessments/assignments/${assignmentId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Assignment removed successfully!');
                // Reload current assignments
                loadCurrentAssignments(currentEditAssessmentId);
            } else {
                alert('Error: ' + (data.message || 'Failed to remove assignment'));
            }
        })
        .catch(error => {
            console.error('Error removing assignment:', error);
            alert('Error removing assignment');
        });
    }
}

function openAssignModal(assessmentId) {
    currentAssessmentId = assessmentId;
    document.getElementById('assignModal').classList.remove('hidden');
    loadClasses();
}

function closeAssignModal() {
    document.getElementById('assignModal').classList.add('hidden');
    selectedClass = null;
    selectedStudents = [];
    document.getElementById('selectedStudentsSection').classList.add('hidden');
    document.getElementById('selectedStudentsList').innerHTML = '';
}

function loadClasses() {
    const classList = document.getElementById('classList');
    classList.innerHTML = '';
    
    // Check if teacher has sections assigned
    if (teacherSections.length === 0) {
        classList.innerHTML = `
            <div class="p-6 text-center text-gray-500">
                <span class="material-symbols-outlined text-4xl text-gray-300 mb-2">school</span>
                <p class="text-sm">No sections assigned</p>
                <p class="text-xs text-gray-400 mt-1">Contact your administrator to assign sections to your account</p>
            </div>
        `;
        return;
    }
    
    // Create class entries based on teacher sections
    teacherSections.forEach(section => {
        const classDiv = document.createElement('div');
        classDiv.className = 'p-4 hover:bg-gray-50 cursor-pointer';
        classDiv.onclick = () => selectClass(section, classDiv);
        
        // Get student count for this section
        const studentCount = studentsData.filter(student => student.section === section).length;
        
        classDiv.innerHTML = `
            <div class="flex items-center">
                <input type="checkbox" class="mr-3 rounded border-gray-300 text-pink-600 focus:ring-pink-500">
                <div class="flex-1">
                    <div class="font-medium text-gray-900">Section ${section}</div>
                    <div class="text-sm text-gray-500">${studentCount} students</div>
                </div>
            </div>
        `;
        
        classList.appendChild(classDiv);
    });
}

function selectClass(section, classElement) {
    // Remove previous selection
    document.querySelectorAll('#classList > div').forEach(div => {
        div.classList.remove('bg-gray-100');
        div.querySelector('input[type="checkbox"]').checked = false;
    });
    
    // Select current class
    classElement.classList.add('bg-gray-100');
    classElement.querySelector('input[type="checkbox"]').checked = true;
    selectedClass = section;
    
    // Show student selection
    showStudentSelection(section);
}

function showStudentSelection(section) {
    const studentsInSection = studentsData.filter(student => student.section === section);
    
    // Add "Pick specific students" link
    const classElement = document.querySelector('#classList .bg-gray-100');
    if (classElement && !classElement.querySelector('.pick-students-link')) {
        const pickStudentsLink = document.createElement('div');
        pickStudentsLink.className = 'pick-students-link mt-2';
        pickStudentsLink.innerHTML = `
            <button onclick="toggleStudentSelection()" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                + Pick specific students
            </button>
        `;
        classElement.appendChild(pickStudentsLink);
    }
    
    // Show selected students section
    document.getElementById('selectedStudentsSection').classList.remove('hidden');
    updateSelectedStudentsList();
}

function toggleStudentSelection() {
    console.log('Fetching students for section:', selectedClass);
    
    // Fetch students from the selected section via API
    fetch(`/teacher/assessments/students/${selectedClass}`)
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.error) {
                alert('Error: ' + data.error + (data.debug ? '\nDebug: ' + JSON.stringify(data.debug) : ''));
                return;
            }
            
            // Show student selection modal or inline selection
            showStudentSelectionModal(data.students);
        })
        .catch(error => {
            console.error('Error fetching students:', error);
            alert('Error fetching students: ' + error.message);
        });
}

function showStudentSelectionModal(students) {
    // Create a simple student selection interface
    const modal = document.createElement('div');
    modal.id = 'studentSelectionModal';
    modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
    modal.innerHTML = `
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 shadow-lg rounded-md bg-white">
            <div class="flex items-center justify-between p-4 border-b">
                <h3 class="text-lg font-semibold">Select Students</h3>
                <button onclick="closeStudentModal()" class="text-gray-400 hover:text-gray-600">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="p-4 max-h-64 overflow-y-auto">
                <div class="space-y-2">
                    ${students.map(student => `
                        <label class="flex items-center p-2 hover:bg-gray-50 rounded">
                            <input type="checkbox" value="${student.user_id}" class="mr-3 rounded border-gray-300 text-pink-600 focus:ring-pink-500">
                            <span class="text-sm">${student.firstname} ${student.lastname}</span>
                        </label>
                    `).join('')}
                </div>
            </div>
            <div class="flex justify-end space-x-3 p-4 border-t">
                <button onclick="closeStudentModal()" class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                    Cancel
                </button>
                <button onclick="confirmStudentSelection()" class="px-4 py-2 bg-pink-600 text-white rounded-lg hover:bg-pink-700">
                    Select Students
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
}

function closeStudentModal() {
    const modal = document.getElementById('studentSelectionModal');
    if (modal) {
        modal.remove();
    }
}

function confirmStudentSelection() {
    const modal = document.getElementById('studentSelectionModal');
    const checkboxes = modal.querySelectorAll('input[type="checkbox"]:checked');
    const selectedStudentIds = Array.from(checkboxes).map(cb => cb.value);
    
    // Get full student data for selected IDs
    const studentsInSection = studentsData.filter(student => student.section === selectedClass);
    selectedStudents = studentsInSection.filter(student => selectedStudentIds.includes(student.user_id.toString()));
    
    closeStudentModal();
    updateSelectedStudentsList();
}

function updateSelectedStudentsList() {
    const selectedStudentsList = document.getElementById('selectedStudentsList');
    
    if (selectedStudents.length === 0) {
        selectedStudentsList.innerHTML = '<p class="text-gray-500 text-sm">All students in section will be assigned</p>';
    } else {
        selectedStudentsList.innerHTML = selectedStudents.map(student => `
            <div class="flex items-center justify-between py-1">
                <span class="text-sm text-gray-700">${student.firstname} ${student.lastname}</span>
                <button onclick="removeStudent('${student.user_id}')" class="text-red-600 hover:text-red-800 text-xs">Remove</button>
            </div>
        `).join('');
    }
}

function removeStudent(userId) {
    selectedStudents = selectedStudents.filter(student => student.user_id !== userId);
    updateSelectedStudentsList();
}

function confirmAssignment() {
    if (!selectedClass) {
        alert('Please select a class');
        return;
    }
    
    const accommodations = document.getElementById('accommodations').checked;
    const studentIds = selectedStudents.map(student => student.user_id);
    
    // Send assignment data to the server
    fetch('/teacher/assessments/assign', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            assessment_id: currentAssessmentId,
            section: selectedClass,
            student_ids: studentIds,
            accommodations: accommodations
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Assessment assigned successfully!');
            closeAssignModal();
            location.reload(); // Refresh to show updated assignments
        } else {
            alert('Error: ' + (data.message || 'Failed to assign assessment'));
        }
    })
    .catch(error => {
        console.error('Error assigning assessment:', error);
        alert('Error assigning assessment');
    });
}

// Search functionality
document.getElementById('classSearch').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const classItems = document.querySelectorAll('#classList > div');
    
    classItems.forEach(item => {
        const className = item.textContent.toLowerCase();
        if (className.includes(searchTerm)) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
});
</script>
@endsection