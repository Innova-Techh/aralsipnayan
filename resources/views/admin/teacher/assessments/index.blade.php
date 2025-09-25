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
                            Not assigned
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
                    <a href="{{ route('teacher.assessments.edit', $assessment) }}" class="text-gray-600 hover:text-gray-800 text-sm font-medium">Edit</a>
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

// Get teacher sections and students data
const teacherSections = @json($teacherSections ?? []);
const studentsData = @json($studentsData ?? []);

console.log('Teacher sections from server:', teacherSections);
console.log('Students data from server:', studentsData);

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