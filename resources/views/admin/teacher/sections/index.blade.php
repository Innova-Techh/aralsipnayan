@extends('admin.teacher.layouts.app')

@section('title', 'Aralsipnayan')

@php
    // Helper function for border colors
    function getBorderColor($index) {
        $colors = ['blue', 'green', 'purple', 'yellow', 'pink', 'indigo'];
        return $colors[$index % count($colors)];
    }

    // Helper function for performance colors
    function getPerformanceColor($performance) {
        if ($performance >= 80) return 'green';
        if ($performance >= 60) return 'yellow';
        return 'red';
    }
@endphp

@section('content')
<div>
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Section Management</h1>
            <p class="text-gray-600 mt-1">Organize and manage your class sections</p>
        </div>
        <button id="createSectionBtn" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center">
            <span class="material-symbols-outlined mr-2">add</span>
            Create Section
        </button>
    </div>

    <!-- Success/Error Messages -->
    <div id="messageContainer" class="mb-4 hidden">
        <div id="successMessage" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 hidden">
            <span id="successText"></span>
        </div>
        <div id="errorMessage" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 hidden">
            <span id="errorText"></span>
        </div>
    </div>

    <!-- Sections Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="sectionsGrid">
        @forelse($sectionsData as $index => $section)
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-{{ getBorderColor($index) }}-500" data-section="{{ $section['section'] }}">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">{{ $section['section'] }}</h3>
                    <p class="text-sm text-gray-500">Grade 6 Mathematics</p>
                </div>
                <div class="flex items-center space-x-2">
                    <button onclick="editSection('{{ $section['section'] }}')" class="text-gray-400 hover:text-gray-600">
                        <span class="material-symbols-outlined">edit</span>
                    </button>
                    <button onclick="deleteSection('{{ $section['section'] }}')" class="text-gray-400 hover:text-red-600">
                        <span class="material-symbols-outlined">delete</span>
                    </button>
                </div>
            </div>
            
            <div class="space-y-3 mb-4">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Students:</span>
                    <span class="text-sm font-medium text-gray-900">{{ $section['student_count'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Active Assessments:</span>
                    <span class="text-sm font-medium text-gray-900">{{ $section['active_assessments'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Avg Performance:</span>
                    <span class="text-sm font-medium text-{{ getPerformanceColor($section['average_performance']) }}-600">{{ $section['average_performance'] }} pts</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Last Activity:</span>
                    <span class="text-sm text-gray-500">{{ $section['last_activity'] }}</span>
                </div>
            </div>

            <div class="flex space-x-2">
                <button onclick="viewStudents('{{ $section['section'] }}')" class="flex-1 bg-blue-50 text-blue-600 py-2 px-3 rounded text-sm font-medium hover:bg-blue-100 transition-colors">
                    Manage Students
                </button>
                <button onclick="assignQuiz('{{ $section['section'] }}')" class="flex-1 bg-gray-50 text-gray-600 py-2 px-3 rounded text-sm font-medium hover:bg-gray-100 transition-colors">
                    Assign Quiz
                </button>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12" id="emptyState">
            <div class="text-gray-500 mb-4">
                <span class="material-symbols-outlined text-6xl">school</span>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No sections found</h3>
            <p class="text-gray-500 mb-4">Create your first section to start managing your students</p>
            <button onclick="openCreateSectionModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                Create Section
            </button>
        </div>
        @endforelse
    </div>

    <!-- Create Section Modal -->
    <div id="createSectionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Create New Section</h3>
                    <button onclick="closeCreateSectionModal()" class="text-gray-400 hover:text-gray-600">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                <form id="createSectionForm">
                    @csrf
                    <div class="mb-4">
                        <label for="sectionName" class="block text-sm font-medium text-gray-700 mb-2">Section Name</label>
                        <input type="text" id="sectionName" name="section" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g., 7-A, 8-Einstein" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Grade Level</label>
                        <div class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-600">
                            Grade 6 (Fixed)
                        </div>
                    </div>
                    <div class="mb-6">
                        <label for="schoolYear" class="block text-sm font-medium text-gray-700 mb-2">School Year (Optional)</label>
                        <input type="text" id="schoolYear" name="school_year" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g., 2024-2025">
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeCreateSectionModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                            Create Section
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Section Modal -->
    <div id="editSectionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Edit Section</h3>
                    <button onclick="closeEditSectionModal()" class="text-gray-400 hover:text-gray-600">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                <form id="editSectionForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editOriginalSection" name="original_section">
                    <div class="mb-4">
                        <label for="editSectionName" class="block text-sm font-medium text-gray-700 mb-2">Section Name</label>
                        <input type="text" id="editSectionName" name="section" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Grade Level</label>
                        <div class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-600">
                            Grade 6 (Fixed)
                        </div>
                    </div>
                    <div class="mb-6">
                        <label for="editSchoolYear" class="block text-sm font-medium text-gray-700 mb-2">School Year (Optional)</label>
                        <input type="text" id="editSchoolYear" name="school_year" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeEditSectionModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                            Update Section
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteSectionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <span class="material-symbols-outlined text-red-600">warning</span>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Delete Section</h3>
                <p class="text-sm text-gray-500 mb-4">Are you sure you want to delete section "<span id="deleteSectionName"></span>"? This action cannot be undone.</p>
                <div class="flex justify-center space-x-3">
                    <button onclick="closeDeleteSectionModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                        Cancel
                    </button>
                    <button onclick="confirmDeleteSection()" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                        Delete Section
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Student Management Modal -->
    <div id="studentManagementModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-5 mx-auto p-6 border w-11/12 max-w-7xl shadow-lg rounded-md bg-white min-h-[90vh]">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Manage Students - <span id="currentSectionName"></span></h3>
                    <button onclick="closeStudentManagementModal()" class="text-gray-400 hover:text-gray-600">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                
                <!-- Add Student Button -->
                <div class="mb-4">
                    <button onclick="openAddStudentModal()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors flex items-center">
                        <span class="material-symbols-outlined mr-2">person_add</span>
                        Add New Student
                    </button>
                </div>

                <!-- Students Table -->
                <div class="w-full">
                    <table class="w-full bg-white border border-gray-200 table-fixed">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="w-24 px-3 py-4 text-left text-sm font-semibold text-gray-700 uppercase">LRN</th>
                                <th class="w-32 px-3 py-4 text-left text-sm font-semibold text-gray-700 uppercase">Name</th>
                                <th class="w-40 px-3 py-4 text-left text-sm font-semibold text-gray-700 uppercase">Email</th>
                                <th class="w-16 px-3 py-4 text-center text-sm font-semibold text-gray-700 uppercase">Pts</th>
                                <th class="w-16 px-3 py-4 text-center text-sm font-semibold text-gray-700 uppercase">Streak</th>
                                <th class="w-24 px-3 py-4 text-left text-sm font-semibold text-gray-700 uppercase">Activity</th>
                                <th class="w-28 px-3 py-4 text-center text-sm font-semibold text-gray-700 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="studentsTableBody" class="bg-white divide-y divide-gray-200">
                            <!-- Students will be loaded here -->
                        </tbody>
                    </table>
                </div>

                <!-- Empty State for Students -->
                <div id="studentsEmptyState" class="text-center py-8 hidden">
                    <div class="text-gray-500 mb-4">
                        <span class="material-symbols-outlined text-4xl">group</span>
                    </div>
                    <h4 class="text-md font-medium text-gray-900 mb-2">No students in this section</h4>
                    <p class="text-gray-500 mb-4">Add students to start managing this section</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Student Modal -->
    <div id="addStudentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Add New Student</h3>
                    <button onclick="closeAddStudentModal()" class="text-gray-400 hover:text-gray-600">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                <form id="addStudentForm">
                    @csrf
                    <input type="hidden" id="studentSection" name="section">
                    <div class="mb-4">
                        <label for="studentFirstName" class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                        <input type="text" id="studentFirstName" name="firstname" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div class="mb-4">
                        <label for="studentMiddleName" class="block text-sm font-medium text-gray-700 mb-2">Middle Name (Optional)</label>
                        <input type="text" id="studentMiddleName" name="middlename" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="mb-4">
                        <label for="studentLastName" class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                        <input type="text" id="studentLastName" name="lastname" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div class="mb-4">
                        <label for="studentEmail" class="block text-sm font-medium text-gray-700 mb-2">Email(Should be Unique)</label>
                        <input type="email" id="studentEmail" name="email" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div class="mb-4">
                        <label for="studentSchoolYear" class="block text-sm font-medium text-gray-700 mb-2">School Year (Optional)</label>
                        <input type="text" id="studentSchoolYear" name="school_year" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g., 2024-2025">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Grade Level</label>
                        <div class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-600">
                            Grade 6 (Fixed)
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Student ID (LRN)</label>
                        <div class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-600">
                            Auto-generated 6-digit number
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeAddStudentModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                            Add Student
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Student Modal -->
    <div id="editStudentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Edit Student</h3>
                    <button onclick="closeEditStudentModal()" class="text-gray-400 hover:text-gray-600">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                <form id="editStudentForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editStudentId" name="student_id">
                    <input type="hidden" id="editStudentSection" name="section">
                    <div class="mb-4">
                        <label for="editStudentFirstName" class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                        <input type="text" id="editStudentFirstName" name="firstname" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div class="mb-4">
                        <label for="editStudentMiddleName" class="block text-sm font-medium text-gray-700 mb-2">Middle Name (Optional)</label>
                        <input type="text" id="editStudentMiddleName" name="middlename" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="mb-4">
                        <label for="editStudentLastName" class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                        <input type="text" id="editStudentLastName" name="lastname" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div class="mb-4">
                        <label for="editStudentEmail" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" id="editStudentEmail" name="email" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div class="mb-4">
                        <label for="editStudentSchoolYear" class="block text-sm font-medium text-gray-700 mb-2">School Year (Optional)</label>
                        <input type="text" id="editStudentSchoolYear" name="school_year" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g., 2024-2025">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Grade Level</label>
                        <div class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-600">
                            Grade 6 (Fixed)
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeEditStudentModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                            Update Student
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Student Confirmation Modal -->
    <div id="deleteStudentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <span class="material-symbols-outlined text-red-600">warning</span>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Delete Student</h3>
                <p class="text-sm text-gray-500 mb-4">Are you sure you want to delete "<span id="deleteStudentName"></span>"? This action cannot be undone.</p>
                <div class="flex justify-center space-x-3">
                    <button onclick="closeDeleteStudentModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                        Cancel
                    </button>
                    <button onclick="confirmDeleteStudent()" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                        Delete Student
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let sectionToDelete = null;

// Create Section Modal Functions
function openCreateSectionModal() {
    document.getElementById('createSectionModal').classList.remove('hidden');
}

function closeCreateSectionModal() {
    document.getElementById('createSectionModal').classList.add('hidden');
    document.getElementById('createSectionForm').reset();
}

// Edit Section Modal Functions
function editSection(sectionName) {
    document.getElementById('editOriginalSection').value = sectionName;
    document.getElementById('editSectionName').value = sectionName;
    document.getElementById('editSectionModal').classList.remove('hidden');
}

function closeEditSectionModal() {
    document.getElementById('editSectionModal').classList.add('hidden');
    document.getElementById('editSectionForm').reset();
}

// Delete Section Modal Functions
function deleteSection(sectionName) {
    sectionToDelete = sectionName;
    document.getElementById('deleteSectionName').textContent = sectionName;
    document.getElementById('deleteSectionModal').classList.remove('hidden');
}

function closeDeleteSectionModal() {
    document.getElementById('deleteSectionModal').classList.add('hidden');
    sectionToDelete = null;
}

function confirmDeleteSection() {
    if (!sectionToDelete) return;
    
    fetch(`/teacher/sections/${encodeURIComponent(sectionToDelete)}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage(data.message, 'success');
            // Remove the section card from the grid
            const sectionCard = document.querySelector(`[data-section="${sectionToDelete}"]`);
            if (sectionCard) {
                sectionCard.remove();
            }
            // Check if no sections remain and show empty state
            const remainingSections = document.querySelectorAll('[data-section]');
            if (remainingSections.length === 0) {
                showEmptyState();
            }
        } else {
            showMessage(data.error || 'Failed to delete section', 'error');
        }
        closeDeleteSectionModal();
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('An error occurred while deleting the section', 'error');
        closeDeleteSectionModal();
    });
}

// Form Submissions
document.getElementById('createSectionForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('/teacher/sections', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage(data.message, 'success');
            closeCreateSectionModal();
            // Reload the page to show the new section
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showMessage(data.error || 'Failed to create section', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('An error occurred while creating the section', 'error');
    });
});

document.getElementById('editSectionForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const originalSection = document.getElementById('editOriginalSection').value;
    
    fetch(`/teacher/sections/${encodeURIComponent(originalSection)}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage(data.message, 'success');
            closeEditSectionModal();
            // Reload the page to show the updated section
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showMessage(data.error || 'Failed to update section', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('An error occurred while updating the section', 'error');
    });
});

// Event Listeners
document.getElementById('createSectionBtn').addEventListener('click', openCreateSectionModal);

// Add Student Form Submission
document.getElementById('addStudentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('/teacher/sections/students', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage(data.message, 'success');
            closeAddStudentModal();
            loadStudents(currentSection); // Reload students
        } else {
            showMessage(data.error || 'Failed to add student', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('An error occurred while adding the student', 'error');
    });
});

// Edit Student Form Submission
document.getElementById('editStudentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const studentId = document.getElementById('editStudentId').value;
    
    fetch(`/teacher/sections/students/${studentId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage(data.message, 'success');
            closeEditStudentModal();
            loadStudents(currentSection); // Reload students
        } else {
            showMessage(data.error || 'Failed to update student', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('An error occurred while updating the student', 'error');
    });
});

// Student Management Functions
let currentSection = null;
let studentToDelete = null;

function viewStudents(sectionName) {
    currentSection = sectionName;
    document.getElementById('currentSectionName').textContent = sectionName;
    document.getElementById('studentSection').value = sectionName;
    document.getElementById('editStudentSection').value = sectionName;
    
    // Load students for this section
    loadStudents(sectionName);
    
    // Show the modal
    document.getElementById('studentManagementModal').classList.remove('hidden');
}

function closeStudentManagementModal() {
    document.getElementById('studentManagementModal').classList.add('hidden');
    currentSection = null;
}

function loadStudents(sectionName) {
    fetch(`/teacher/sections/students/${encodeURIComponent(sectionName)}`)
        .then(response => response.json())
        .then(data => {
            if (data.students && data.students.length > 0) {
                displayStudents(data.students);
                document.getElementById('studentsEmptyState').classList.add('hidden');
            } else {
                document.getElementById('studentsTableBody').innerHTML = '';
                document.getElementById('studentsEmptyState').classList.remove('hidden');
            }
        })
        .catch(error => {
            console.error('Error loading students:', error);
            showMessage('Failed to load students', 'error');
        });
}

function displayStudents(students) {
    const tbody = document.getElementById('studentsTableBody');
    tbody.innerHTML = '';
    
    students.forEach(student => {
        const row = document.createElement('tr');
        // Truncate long names and emails for better fit
        const truncatedName = student.name.length > 20 ? student.name.substring(0, 20) + '...' : student.name;
        const truncatedEmail = student.email.length > 25 ? student.email.substring(0, 25) + '...' : student.email;
        const shortActivity = student.last_activity.replace(' ago', '').replace('hours', 'h').replace('days', 'd').replace('minutes', 'm');
        
        row.innerHTML = `
            <td class="px-3 py-4 text-sm font-semibold text-gray-900 truncate" title="${student.student_id || 'N/A'}">${student.student_id || 'N/A'}</td>
            <td class="px-3 py-4 text-sm font-medium text-gray-900 truncate" title="${student.name}">${truncatedName}</td>
            <td class="px-3 py-4 text-sm text-gray-600 truncate" title="${student.email}">${truncatedEmail}</td>
            <td class="px-3 py-4 text-sm text-gray-600 text-center font-medium">${student.total_points}</td>
            <td class="px-3 py-4 text-sm text-gray-600 text-center font-medium">${student.current_streak}</td>
            <td class="px-3 py-4 text-sm text-gray-600 truncate" title="${student.last_activity}">${shortActivity}</td>
            <td class="px-3 py-4 text-sm font-medium text-center">
                <button onclick="editStudent(${student.user_id}, '${student.firstname}', '${student.middlename || ''}', '${student.lastname}', '${student.email}', '${student.school_year || ''}')" class="bg-blue-500 text-white px-2 py-1 rounded text-xs hover:bg-blue-600 mr-1 transition-colors" title="Edit Student">Edit</button>
                <button onclick="deleteStudent(${student.user_id}, '${student.name}')" class="bg-red-500 text-white px-2 py-1 rounded text-xs hover:bg-red-600 transition-colors" title="Delete Student">Delete</button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

// Add Student Modal Functions
function openAddStudentModal() {
    document.getElementById('addStudentModal').classList.remove('hidden');
}

function closeAddStudentModal() {
    document.getElementById('addStudentModal').classList.add('hidden');
    document.getElementById('addStudentForm').reset();
}

// Edit Student Modal Functions
function editStudent(userId, firstName, middleName, lastName, email, schoolYear) {
    document.getElementById('editStudentId').value = userId;
    document.getElementById('editStudentFirstName').value = firstName;
    document.getElementById('editStudentMiddleName').value = middleName;
    document.getElementById('editStudentLastName').value = lastName;
    document.getElementById('editStudentEmail').value = email;
    document.getElementById('editStudentSchoolYear').value = schoolYear;
    document.getElementById('editStudentModal').classList.remove('hidden');
}

function closeEditStudentModal() {
    document.getElementById('editStudentModal').classList.add('hidden');
    document.getElementById('editStudentForm').reset();
}

// Delete Student Modal Functions
function deleteStudent(userId, name) {
    studentToDelete = userId;
    document.getElementById('deleteStudentName').textContent = name;
    document.getElementById('deleteStudentModal').classList.remove('hidden');
}

function closeDeleteStudentModal() {
    document.getElementById('deleteStudentModal').classList.add('hidden');
    studentToDelete = null;
}

function confirmDeleteStudent() {
    if (!studentToDelete) return;
    
    fetch(`/teacher/sections/students/${studentToDelete}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage(data.message, 'success');
            loadStudents(currentSection); // Reload students
        } else {
            showMessage(data.error || 'Failed to delete student', 'error');
        }
        closeDeleteStudentModal();
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('An error occurred while deleting the student', 'error');
        closeDeleteStudentModal();
    });
}

function assignQuiz(sectionName) {
    window.location.href = `/teacher/assessments?section=${encodeURIComponent(sectionName)}`;
}

// Utility Functions
function showMessage(message, type) {
    const messageContainer = document.getElementById('messageContainer');
    const successMessage = document.getElementById('successMessage');
    const errorMessage = document.getElementById('errorMessage');
    const successText = document.getElementById('successText');
    const errorText = document.getElementById('errorText');
    
    // Hide all messages first
    successMessage.classList.add('hidden');
    errorMessage.classList.add('hidden');
    
    if (type === 'success') {
        successText.textContent = message;
        successMessage.classList.remove('hidden');
    } else {
        errorText.textContent = message;
        errorMessage.classList.remove('hidden');
    }
    
    messageContainer.classList.remove('hidden');
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        messageContainer.classList.add('hidden');
    }, 5000);
}

function showEmptyState() {
    const sectionsGrid = document.getElementById('sectionsGrid');
    sectionsGrid.innerHTML = `
        <div class="col-span-full text-center py-12" id="emptyState">
            <div class="text-gray-500 mb-4">
                <span class="material-symbols-outlined text-6xl">school</span>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No sections found</h3>
            <p class="text-gray-500 mb-4">Create your first section to start managing your students</p>
            <button onclick="openCreateSectionModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                Create Section
            </button>
        </div>
    `;
}
</script>
@endsection