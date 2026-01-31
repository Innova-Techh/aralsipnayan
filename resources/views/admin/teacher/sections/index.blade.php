@extends('admin.teacher.layouts.app')

@section('title', 'AralSipnayan')

@php
    // Helper function for border colors
    function getBorderColor($index)
    {
        $colors = ['blue', 'green', 'purple', 'yellow', 'pink', 'indigo'];
        return $colors[$index % count($colors)];
    }

    // Helper function for performance colors
    function getPerformanceColor($performance)
    {
        if ($performance >= 80)
            return 'green';
        if ($performance >= 60)
            return 'yellow';
        return 'red';
    }
@endphp

@section('content')
    <div class="min-h-screen bg-gray-50">
        <!-- Header -->
        <div class=" px-8 py-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Section Management</h1>
                    <p class="text-sm text-gray-600 mt-1">Manage your class sections and monitor student progress</p>
                </div>
                </div>
            </div>

            <div class="px-8 py-6">
                <!-- Success/Error Messages -->
                <div id="messageContainer" class="mb-4 hidden">
                    <div id="successMessage"
                        class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 hidden">
                        <span id="successText"></span>
                    </div>
                    <div id="errorMessage" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 hidden">
                        <span id="errorText"></span>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <!-- Total Sections -->
                    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-sm font-medium text-gray-600">Active Sections</span>
                            <span class="material-symbols-outlined text-green-400">book</span>
                        </div>
                        <div class="text-3xl font-bold text-gray-900">{{ count($sectionsData) }}</div>
                    </div>

                    <!-- Total Students -->
                    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-sm font-medium text-gray-600">Total Students</span>
                            <span class="material-symbols-outlined text-gray-400">group</span>
                        </div>
                        <div class="text-3xl font-bold text-gray-900">
                            {{ array_sum(array_column($sectionsData, 'student_count')) }}
                        </div>
                    </div>


                    <!-- Active Assessments -->
                    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-sm font-medium text-gray-600">Active Quizes</span>
                            <span class="material-symbols-outlined text-gray-400">assignment</span>
                        </div>
                        <div class="text-3xl font-bold text-gray-900">
                            {{ array_sum(array_column($sectionsData, 'active_assessments')) }}
                        </div>
                    </div>
                  
                  <!-- Inactive Sections -->
                    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-sm font-medium text-gray-600">Inactive Sections</span>
                            <span class="material-symbols-outlined text-gray-400">block</span>
                        </div>
                        <div class="text-3xl font-bold text-gray-900">
                            {{ count($inactiveSectionsData) }}
                        </div>
                    </div>
                </div>

                <!-- Search and Filters -->
                <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 mb-6">
                    <div class="flex items-center justify-between gap-4 flex-wrap">
                        <div class="flex-1 relative min-w-[200px]">
                            <span
                                class="material-symbols-outlined absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-xl">search</span>
                            <input type="text" id="searchSections" placeholder="Search sections..."
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div class="flex gap-2">
                            <button onclick="filterSections('all')" id="filterAll"
                                class="px-4 py-2.5 rounded-lg text-sm font-medium transition-colors bg-blue-600 text-white hover:bg-blue-700">
                                All Sections
                            </button>
                            <button onclick="filterSections('active')" id="filterActive"
                                class="px-4 py-2.5 rounded-lg text-sm font-medium transition-colors bg-gray-100 text-gray-700 hover:bg-gray-200">
                                Active Only
                            </button>
                            <button onclick="filterSections('inactive')" id="filterInactive"
                                class="px-4 py-2.5 rounded-lg text-sm font-medium transition-colors bg-gray-100 text-gray-700 hover:bg-gray-200">
                                Inactive Only
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Active Sections Container -->
                <div id="activeSectionsContainer">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-700">Active Sections</h3>
                    </div>
                    <!-- Sections Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="sectionsGrid">
                    @forelse($sectionsData as $index => $section)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow"
                            data-section="{{ $section['section'] }}">
                            <!-- Section Header with Color Indicator -->
                            <div class="border-l-4 border-{{ getBorderColor($index) }}-500 p-6">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 rounded-lg bg-{{ getBorderColor($index) }}-100 flex items-center justify-center">
                                            <span
                                                class="material-symbols-outlined text-{{ getBorderColor($index) }}-600">school</span>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900">{{ $section['section'] }}</h3>
                                            <span
                                                class="inline-block px-2 py-0.5 text-xs font-medium bg-gray-100 text-gray-600 rounded mt-1">MATH101-{{ strtoupper(substr($section['section'], -1)) }}</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-1">
                                        <button onclick="editSection('{{ $section['section'] }}')"
                                            class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                            <span class="material-symbols-outlined text-xl">edit</span>
                                        </button>
                                        <button onclick="deleteSection('{{ $section['section'] }}')"
                                            class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-50 rounded-lg transition-colors">
                                            <span class="material-symbols-outlined text-xl">archive</span>
                                        </button>
                                    </div>
                                </div>

                                <p class="text-sm text-gray-600 mb-4">Basic Mathematics and Arithmetic</p>

                                <!-- Section Details -->
                                <div class="space-y-3 mb-4">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <span class="material-symbols-outlined text-gray-400 text-sm">group</span>
                                            <span class="text-sm text-gray-600">Students</span>
                                        </div>
                                        <span class="text-sm font-medium text-gray-900">{{ $section['student_count'] }}
                                            ({{ $section['student_active'] }} active)</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <span class="material-symbols-outlined text-gray-400 text-sm">analytics</span>
                                            <span class="text-sm text-gray-600">Average Score</span>
                                        </div>
                                        <span
                                            class="text-sm font-bold text-{{ getPerformanceColor($section['average_performance']) }}-600">{{ $section['average_performance'] }}%</span>
                                    </div>
                                </div>

                                <!-- Schedule -->
                                <div class="bg-gray-50 rounded-lg p-3 mb-4">
                                    <div class="flex items-center gap-2 text-sm text-gray-600 mb-1">
                                        <span class="material-symbols-outlined text-base">schedule</span>
                                        <span>MWF 9:00-10:00 AM</span>
                                    </div>

                                </div>

                                <!-- Action Buttons -->
                                <div class="flex gap-2">
                                    <a href="{{ route('teacher.sections.show', $section['section']) }}"
                                        class="flex-1 bg-blue-50 text-blue-600 py-2.5 px-3 rounded-lg text-sm font-medium hover:bg-blue-100 transition-colors flex items-center justify-center gap-1">
                                        <span class="material-symbols-outlined text-base">visibility</span>
                                        View Details
                                    </a>
                                    <button onclick="assignQuiz('{{ $section['section'] }}')"
                                        class="flex-1 bg-green-50 text-green-600 py-2.5 px-3 rounded-lg text-sm font-medium hover:bg-green-100 transition-colors flex items-center justify-center gap-1">
                                        <span class="material-symbols-outlined text-base">assignment</span>
                                        Assign Assessment
                                    </button>
                                </div>

                                <!-- Last Activity -->
                                <div class="mt-4 pt-4 border-t border-gray-100">
                                    <p class="text-xs text-gray-500">Last activity: {{ $section['last_activity'] }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-16" id="emptyState">
                            <div class="text-gray-400 mb-4">
                                <span class="material-symbols-outlined text-7xl">school</span>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">No sections found</h3>
                            <p class="text-gray-500 mb-6">Contact your administrator to create sections</p>
                        </div>
                    @endforelse
                </div>
                </div>
                <!-- End Active Sections Container -->

                <!-- Inactive Sections -->
                <div id="inactiveSectionsContainer" class="mt-8 {{ count($inactiveSectionsData) > 0 ? '' : 'hidden' }}">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-700">Inactive Sections</h3>
                        <span class="text-sm text-gray-500">These sections are currently archived</span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="inactiveSectionsGrid">
                        @forelse($inactiveSectionsData as $index => $section)
                            <div class="bg-gray-50 rounded-xl shadow-sm border-2 border-dashed border-gray-300 overflow-hidden opacity-75"
                                data-section="{{ $section['section'] }}">
                                <!-- Section Header with Color Indicator -->
                                <div class="border-l-4 border-gray-400 p-6">
                                    <div class="flex items-start justify-between mb-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-10 h-10 rounded-lg bg-gray-200 flex items-center justify-center">
                                                <span class="material-symbols-outlined text-gray-500">school</span>
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-semibold text-gray-700">{{ $section['section'] }}</h3>
                                                <span
                                                    class="inline-block px-2 py-0.5 text-xs font-medium bg-red-100 text-red-600 rounded mt-1">INACTIVE</span>
                                            </div>
                                        </div>
                                        <button onclick="reactivateSection('{{ $section['section'] }}')"
                                            class="p-1.5 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                                            title="Reactivate Section">
                                            <span class="material-symbols-outlined text-xl">restart_alt</span>
                                        </button>
                                    </div>

                                    <p class="text-sm text-gray-500 mb-4">Basic Mathematics and Arithmetic</p>

                                    <!-- Section Details -->
                                    <div class="space-y-3 mb-4">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-2">
                                                <span class="material-symbols-outlined text-gray-400 text-sm">group</span>
                                                <span class="text-sm text-gray-500">Students</span>
                                            </div>
                                            <span class="text-sm font-medium text-gray-600">{{ $section['student_count'] }}
                                                ({{ $section['student_active'] }} active)</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-2">
                                                <span class="material-symbols-outlined text-gray-400 text-sm">analytics</span>
                                                <span class="text-sm text-gray-500">Average Score</span>
                                            </div>
                                            <span class="text-sm font-bold text-gray-500">{{ $section['average_performance'] }}%</span>
                                        </div>
                                    </div>

                                    <!-- Action Button -->
                                    <button onclick="reactivateSection('{{ $section['section'] }}')"
                                        class="w-full bg-green-50 text-green-600 py-2.5 px-3 rounded-lg text-sm font-medium hover:bg-green-100 transition-colors flex items-center justify-center gap-1">
                                        <span class="material-symbols-outlined text-base">restart_alt</span>
                                        Reactivate Section
                                    </button>

                                    <!-- Last Activity -->
                                    <div class="mt-4 pt-4 border-t border-gray-200">
                                        <p class="text-xs text-gray-500">Last activity: {{ $section['last_activity'] }}</p>
                                    </div>
                                </div>
                            </div>
                        @empty
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Delete Confirmation Modal -->
            <div id="deleteSectionModal"
                class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
                <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                    <div class="mt-3 text-center">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                            <span class="material-symbols-outlined text-red-600">warning</span>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Deactivate Section</h3>
                        <p class="text-sm text-gray-500 mb-4">Are you sure you want to deactivate section "<span
                                id="deleteSectionName"></span>"? This action can only be reverted by admin.</p>
                        <div class="flex justify-center space-x-3">
                            <button onclick="closeDeleteSectionModal()"
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                                Cancel
                            </button>
                            <button onclick="confirmDeleteSection()"
                                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                                Deactivate Section
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Section Modal -->
            <div id="editSectionModal"
                class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
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
                                <label for="editSectionName" class="block text-sm font-medium text-gray-700 mb-2">Section
                                    Name</label>
                                <input type="text" id="editSectionName" name="section"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    required>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Grade Level</label>
                                <div class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-600">
                                    Grade 6 (Fixed)
                                </div>
                            </div>
                            <div class="flex justify-end space-x-3">
                                <button type="button" onclick="closeEditSectionModal()"
                                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                                    Cancel
                                </button>
                                <button type="submit"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                                    Update Section
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script>
            let sectionToDelete = null;

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

            // Section Deactivation Modal Functions
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

                fetch(`/teacher/sections/${encodeURIComponent(sectionToDelete)}/deactivate`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showMessage(data.message, 'success');

                        const remainingSections = document.querySelectorAll('[data-section]');
                        if (remainingSections.length === 0) {
                            showEmptyState();
                        }
                    } else {
                        showMessage(data.error || 'Failed to deactivate section', 'error');
                    }
                    closeDeleteSectionModal();
                })
                .catch(error => {
                    console.error('Error:', error);
                    showMessage('An error occurred while deactivating the section', 'error');
                    closeDeleteSectionModal();
                });
            }

            // Edit Section Form Submission
            document.getElementById('editSectionForm').addEventListener('submit', function (e) {
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

            // Add Student Form Submission
            const addStudentForm = document.getElementById('addStudentForm');
            if (addStudentForm) {
                addStudentForm.addEventListener('submit', function (e) {
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
                                loadStudents(currentSection);
                            } else {
                                showMessage(data.error || 'Failed to add student', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showMessage('An error occurred while adding the student', 'error');
                        });
                });
            }

            // Edit Student Form Submission
            const editStudentForm = document.getElementById('editStudentForm');
            if (editStudentForm) {
                editStudentForm.addEventListener('submit', function (e) {
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
                                loadStudents(currentSection);
                            } else {
                                showMessage(data.error || 'Failed to update student', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showMessage('An error occurred while updating the student', 'error');
                        });
                });
            }

            // Student Management Functions
            let currentSection = null;
            let studentToDelete = null;

            function viewStudents(sectionName) {
                currentSection = sectionName;
                document.getElementById('currentSectionName').textContent = sectionName;
                document.getElementById('studentSection').value = sectionName;
                document.getElementById('editStudentSection').value = sectionName;

                loadStudents(sectionName);
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

            function openAddStudentModal() {
                document.getElementById('addStudentModal').classList.remove('hidden');
            }

            function closeAddStudentModal() {
                document.getElementById('addStudentModal').classList.add('hidden');
                document.getElementById('addStudentForm').reset();
            }

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
                            loadStudents(currentSection);
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

            function showMessage(message, type) {
                const messageContainer = document.getElementById('messageContainer');
                const successMessage = document.getElementById('successMessage');
                const errorMessage = document.getElementById('errorMessage');
                const successText = document.getElementById('successText');
                const errorText = document.getElementById('errorText');

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

                setTimeout(() => {
                    messageContainer.classList.add('hidden');
                }, 5000);
            }

            function showEmptyState() {
                const sectionsGrid = document.getElementById('sectionsGrid');
                sectionsGrid.innerHTML = `
                    <div class="col-span-full text-center py-16" id="emptyState">
                        <div class="text-gray-400 mb-4">
                            <span class="material-symbols-outlined text-7xl">school</span>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">No sections found</h3>
                        <p class="text-gray-500 mb-6">Contact your administrator to create sections</p>
                    </div>
                `;
            }

            // Filter and Search functionality
            let currentFilter = 'all';

            function filterSections(filter) {
                currentFilter = filter;

                // Update button states
                document.getElementById('filterAll').className = 'px-4 py-2.5 rounded-lg text-sm font-medium transition-colors ' +
                    (filter === 'all' ? 'bg-blue-600 text-white hover:bg-blue-700' : 'bg-gray-100 text-gray-700 hover:bg-gray-200');
                document.getElementById('filterActive').className = 'px-4 py-2.5 rounded-lg text-sm font-medium transition-colors ' +
                    (filter === 'active' ? 'bg-blue-600 text-white hover:bg-blue-700' : 'bg-gray-100 text-gray-700 hover:bg-gray-200');
                document.getElementById('filterInactive').className = 'px-4 py-2.5 rounded-lg text-sm font-medium transition-colors ' +
                    (filter === 'inactive' ? 'bg-blue-600 text-white hover:bg-blue-700' : 'bg-gray-100 text-gray-700 hover:bg-gray-200');

                // Show/hide sections based on filter
                const activeContainer = document.getElementById('activeSectionsContainer');
                const inactiveContainer = document.getElementById('inactiveSectionsContainer');

                if (filter === 'active') {
                    activeContainer.classList.remove('hidden');
                    inactiveContainer.classList.add('hidden');
                } else if (filter === 'inactive') {
                    activeContainer.classList.add('hidden');
                    inactiveContainer.classList.remove('hidden');
                } else {
                    // Show both
                    activeContainer.classList.remove('hidden');
                    inactiveContainer.classList.remove('hidden');
                }

                // Reapply search if there's a search term
                const searchTerm = document.getElementById('searchSections').value;
                if (searchTerm) {
                    performSearch(searchTerm);
                }
            }

            function performSearch(searchTerm) {
                searchTerm = searchTerm.toLowerCase();

                // Search active sections
                const activeSectionCards = document.querySelectorAll('#sectionsGrid [data-section]');
                activeSectionCards.forEach(card => {
                    const sectionName = card.getAttribute('data-section').toLowerCase();
                    if (sectionName.includes(searchTerm)) {
                        card.style.display = '';
                    } else {
                        card.style.display = 'none';
                    }
                });

                // Search inactive sections
                const inactiveSectionCards = document.querySelectorAll('#inactiveSectionsGrid [data-section]');
                inactiveSectionCards.forEach(card => {
                    const sectionName = card.getAttribute('data-section').toLowerCase();
                    if (sectionName.includes(searchTerm)) {
                        card.style.display = '';
                    } else {
                        card.style.display = 'none';
                    }
                });
            }

            document.getElementById('searchSections').addEventListener('input', function (e) {
                performSearch(e.target.value);
            });

            // Reactivate Section Function
            function reactivateSection(sectionName) {
                Swal.fire({
                    title: 'Reactivate Section',
                    html: `Are you sure you want to reactivate section "<strong>${sectionName}</strong>"?<br><br>This will make the section active again.`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#10b981',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, Reactivate',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`/teacher/sections/${encodeURIComponent(sectionName)}/reactivate`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                            },
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: 'Success!',
                                    text: data.message,
                                    icon: 'success',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: data.error || 'Failed to reactivate section',
                                    icon: 'error',
                                    confirmButtonColor: '#dc2626'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                title: 'Error!',
                                text: 'An error occurred while reactivating the section',
                                icon: 'error',
                                confirmButtonColor: '#dc2626'
                            });
                        });
                    }
                });
            }
        </script>
@endsection