@extends('admin.teacher.layouts.app')

@section('title', 'AralSipnayan')

@section('content')
    <div class="min-h-screen bg-gray-50">
        <!-- Header Section -->
        <div class="bg-white border-b border-gray-200 px-8 py-6">
            <div class="flex items-center justify-between mb-4">
                <button onclick="window.location.href='{{ route('teacher.sections') }}'"
                    class="flex items-center gap-2 text-gray-600 hover:text-gray-900 transition-colors">
                    <span class="material-symbols-outlined">arrow_back</span>
                    <span class="font-medium">Back to Sections</span>
                </button>
                <!-- <button id="createAssessmentBtn"
                    class="bg-blue-600 text-white px-5 py-2.5 rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2 shadow-sm">
                    <span class="material-symbols-outlined">add</span>
                    Create Assessment
                </button> --> 
                <!-- Add Student Button -->
                <div class="mb-4">
                    <button onclick="openAddStudentModal()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors flex items-center">
                        <span class="material-symbols-outlined mr-2">person_add</span>
                        Add New Student
                    </button>
                </div>
            </div>

            <!-- Section Title -->
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900">{{ $section['name'] ?? 'Section Name' }}</h1>
                <p class="text-gray-600 mt-1">Basic Mathematics and Arithmetic</p>
            </div>

            <!-- Section Info Grid -->
            <div class="grid grid-cols-4 gap-6 bg-gray-50 rounded-lg p-6">
                <div>
                    <div class="flex items-center gap-2 text-sm text-gray-600 mb-1">
                        <span class="material-symbols-outlined text-lg">person</span>
                        <span>Teacher</span>
                    </div>
                    <p class="font-semibold text-gray-900">{{ $teacher ?? 'Ms. Johnson' }}</p>
                </div>
                <div>
                    <div class="flex items-center gap-2 text-sm text-gray-600 mb-1">
                        <span class="material-symbols-outlined text-lg">schedule</span>
                        <span>Schedule</span>
                    </div>
                    <p class="font-semibold text-gray-900">MWF 9:00-10:00 AM</p>
                </div>
                <div>
                    <div class="flex items-center gap-2 text-sm text-gray-600 mb-1">
                        <span class="material-symbols-outlined text-lg">door_front</span>
                        <span>Room</span>
                    </div>
                    <p class="font-semibold text-gray-900">{{ $room ?? 'Room 201' }}</p>
                </div>
                <div>
                    <div class="flex items-center gap-2 text-sm text-gray-600 mb-1">
                        <span class="material-symbols-outlined text-lg">tag</span>
                        <span>Section ID</span>
                    </div>
                    <p class="font-semibold text-gray-900">{{ $sectionId ?? 'MATH101-A' }}</p>
                </div>
            </div>
        </div>

        <div class="px-8 py-6">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <!-- Total Students -->
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-medium text-gray-600">Total Students</span>
                        <span class="material-symbols-outlined text-gray-400">group</span>
                    </div>
                    <div class="text-3xl font-bold text-gray-900">{{ $totalStudents ?? 7 }}</div>
                    <p class="text-xs text-gray-500 mt-1">{{ $activeStudents ?? 7 }} active students</p>
                </div>

                <!-- Average Score -->
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-medium text-gray-600">Average Score</span>
                        <span class="material-symbols-outlined text-gray-400">analytics</span>
                    </div>
                    <div class="text-3xl font-bold text-gray-900">{{ $averageScore ?? 86 }}%</div>
                    <p class="text-xs text-gray-500 mt-1">Section average</p>
                </div>

                <!-- Completion Rate -->
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-medium text-gray-600">Completion Rate</span>
                        <span class="material-symbols-outlined text-gray-400">task_alt</span>
                    </div>
                    <div class="text-3xl font-bold text-gray-900">{{ $completionRate ?? 82 }}%</div>
                    <p class="text-xs text-gray-500 mt-1">Assessment completion</p>
                </div>

                <!-- Top Performer -->
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-medium text-gray-600">Top Performer</span>
                        <span class="material-symbols-outlined text-gray-400">emoji_events</span>
                    </div>
                    <div class="text-3xl font-bold text-gray-900">{{ $topPerformerScore ?? 92 }}%</div>
                    <p class="text-xs text-gray-500 mt-1">{{ $topPerformer ?? 'Maria Santos' }}</p>
                </div>
            </div>

            <!-- Section Students Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Section Students</h2>
                    <p class="text-sm text-gray-600 mt-1">All students enrolled in
                        {{ $section['name'] ?? 'Mathematics 101 - Section A' }}
                    </p>
                </div>

                <!-- Students Table -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    Rank</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    Student</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    Overall Score</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    Progress</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    Points</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    Last Activity</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody id="studentsTableBody" class="bg-white divide-y divide-gray-200">
                            @forelse($students ?? [] as $index => $student)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <!-- Rank with Badge -->
                                    <td class="px-6 py-4">
                                        @if($index === 0)
                                            <div class="flex items-center justify-center w-8 h-8">
                                                <span class="material-symbols-outlined text-yellow-500 text-2xl">emoji_events</span>
                                            </div>
                                        @elseif($index === 1)
                                            <div class="flex items-center justify-center w-8 h-8">
                                                <span class="material-symbols-outlined text-gray-400 text-2xl">emoji_events</span>
                                            </div>
                                        @elseif($index === 2)
                                            <div class="flex items-center justify-center w-8 h-8">
                                                <span class="material-symbols-outlined text-orange-600 text-2xl">emoji_events</span>
                                            </div>
                                        @else
                                            <div class="text-center text-sm font-semibold text-gray-600">#{{ $index + 1 }}</div>
                                        @endif
                                    </td>

                                    <!-- Student Info -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden">
                                                <span class="material-symbols-outlined text-gray-500">person</span>
                                            </div>
                                            <div>
                                                <div class="font-semibold text-gray-900">{{ $student['name'] }}</div>
                                                <div class="text-sm text-gray-500">{{ $student['email'] }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Overall Score -->
                                    <td class="px-6 py-4">
                                        <span class="text-lg font-bold text-blue-600">{{ $student['score'] }}%</span>
                                    </td>

                                    <!-- Progress -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-medium text-gray-700">{{ $student['progress'] }}</span>
                                            <div class="flex-1 bg-gray-200 rounded-full h-2 w-24">
                                                @php
                                                    $progressPercent = $student['total'] > 0 ? ($student['completed'] / $student['total']) * 100 : 0;
                                                @endphp
                                                <div class="bg-blue-600 h-2 rounded-full"
                                                    style="width: {{ $progressPercent }}%"></div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Points -->
                                    <td class="px-6 py-4">
                                        <span class="font-semibold text-gray-900">{{ number_format($student['points']) }}</span>
                                    </td>

                                    <!-- Last Activity -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-1 text-sm text-gray-600">
                                            <span class="material-symbols-outlined text-base">schedule</span>
                                            <span>{{ $student['last_activity'] }}</span>
                                        </div>
                                    </td>

                                    <!-- Status -->
                                    <td class="px-6 py-4">
                                        @php
                                            $isActive = strtolower($student['status'] ?? 'active') === 'active';
                                            $badgeBg = $isActive ? 'bg-green-100' : 'bg-gray-200';
                                            $badgeText = $isActive ? 'text-green-800' : 'text-gray-700';
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $badgeBg }} {{ $badgeText }}">
                                            {{ $student['status'] ?? 'active' }}
                                        </span>
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-center">
                                            <button class="p-2 hover:bg-gray-100 rounded-lg transition-colors"
                                                onclick="showStudentActions({{ $student['id'] }})">
                                                <span class="material-symbols-outlined text-gray-600">more_vert</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <span class="material-symbols-outlined text-gray-400 text-5xl mb-3">group</span>
                                            <h3 class="text-lg font-medium text-gray-900 mb-1">No students found</h3>
                                            <p class="text-gray-500">Add students to this section to get started</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Global Message Toasts -->
    <div id="messageContainer"
     class="hidden fixed top-4 left-1/2 -translate-x-1/2 z-[60] space-y-2 w-80"
     aria-live="polite">
        <!-- Success -->
        <div id="successMessage"
            class="hidden flex items-start gap-3 rounded-lg border border-green-200 bg-green-50 p-4 shadow">
            <span class="material-symbols-outlined text-green-600">check_circle</span>
            <div class="text-sm text-green-800">
                <span id="successText"></span>
            </div>
        </div>
        <!-- Error -->
        <div id="errorMessage"
            class="hidden flex items-start gap-3 rounded-lg border border-red-200 bg-red-50 p-4 shadow">
            <span class="material-symbols-outlined text-red-600">error</span>
            <div class="text-sm text-red-800">
                <span id="errorText"></span>
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

    <!-- Student Actions Modal -->
    <div id="studentActionsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Student Actions</h3>
                <div class="space-y-2">
                    <button 
                        onclick="openEditStudentModal()"
                        class="w-full text-left px-4 py-3 hover:bg-gray-50 rounded-lg transition-colors flex items-center gap-3">
                        <span class="material-symbols-outlined text-blue-600">visibility</span>
                        <span class="font-medium text-gray-700">View/Edit Details</span>
                    </button>
                    <button
                        class="w-full text-left px-4 py-3 hover:bg-gray-50 rounded-lg transition-colors flex items-center gap-3">
                        <span class="material-symbols-outlined text-green-600">assignment</span>
                        <span class="font-medium text-gray-700">Assign Assessment</span>
                    </button>
                    <button
                        class="w-full text-left px-4 py-3 hover:bg-gray-50 rounded-lg transition-colors flex items-center gap-3">
                        <span class="material-symbols-outlined text-orange-600">mail</span>
                        <span class="font-medium text-gray-700">Send Message</span>
                    </button>
                    <button
                        class="w-full text-left px-4 py-3 hover:bg-gray-50 rounded-lg transition-colors flex items-center gap-3">
                        <span class="material-symbols-outlined text-red-600">person_remove</span>
                        <span class="font-medium text-gray-700">Remove from Section</span>
                    </button>
                    <button id="toggleActivationBtn"
                        onclick="toggleActivation()"
                        class="w-full text-left px-4 py-3 hover:bg-gray-50 rounded-lg transition-colors flex items-center gap-3">
                        <span id="toggleActivationIcon" class="material-symbols-outlined text-red-600">person_off</span>
                        <span id="toggleActivationText" class="font-medium text-gray-700">Deactivate Account</span>
                    </button>
                </div>
                <button onclick="closeStudentActionsModal()"
                    class="mt-4 w-full px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>

    <script>
        let selectedStudentId = null;
        const currentSection = '{{ $section['raw_name'] ?? ($section['section'] ?? '') }}';

        // Add Student Modal Functions
        window.openAddStudentModal = function () {
            // Ensure the hidden section input is set before showing the modal
            const sectionInput = document.getElementById('studentSection');
            if (sectionInput && currentSection) {
                sectionInput.value = currentSection;
            }
            document.getElementById('addStudentModal').classList.remove('hidden');
        }

        window.closeAddStudentModal = function () {
            document.getElementById('addStudentModal').classList.add('hidden');
            document.getElementById('addStudentForm').reset();
        }

        // Add Student Form Submission
        document.getElementById('addStudentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('/teacher/sections/students', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(async response => {
                let data;
                try { data = await response.json(); } catch (_) { data = {}; }
                if (response.ok && data.success) {
                    showMessage(data.message || 'Student added successfully', 'success');
                    closeAddStudentModal();
                    if (typeof loadStudents === 'function') {
                        loadStudents(currentSection);
                    }
                } else {
                    const msg = (data && (data.error || data.message)) || 'Failed to add student';
                    showMessage(msg, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('An error occurred while adding the student', 'error');
            });
        });

        window.showStudentActions = function (studentId) {
            selectedStudentId = studentId;
            document.getElementById('studentActionsModal').classList.remove('hidden');

            // Update Activate/Deactivate action label and icon based on current status
            try {
                const rows = document.querySelectorAll('#studentsTableBody tr');
                let statusText = 'active';
                rows.forEach(tr => {
                    const btn = tr.querySelector('button[onclick^="showStudentActions("]');
                    if (btn && btn.getAttribute('onclick').includes(String(studentId))) {
                        const statusEl = tr.querySelector('td:nth-child(7) span');
                        if (statusEl) statusText = (statusEl.textContent || '').trim().toLowerCase();
                    }
                });

                const toggleTextEl = document.getElementById('toggleActivationText');
                const toggleIconEl = document.getElementById('toggleActivationIcon');
                const toggleBtnEl = document.getElementById('toggleActivationBtn');
                if (statusText === 'inactive') {
                    toggleTextEl.textContent = 'Activate Account';
                    toggleIconEl.textContent = 'person';
                    toggleIconEl.classList.remove('text-red-600');
                    toggleIconEl.classList.add('text-green-600');
                    if (toggleBtnEl) toggleBtnEl.dataset.action = 'activate';
                } else {
                    toggleTextEl.textContent = 'Deactivate Account';
                    toggleIconEl.textContent = 'person_off';
                    toggleIconEl.classList.remove('text-green-600');
                    toggleIconEl.classList.add('text-red-600');
                    if (toggleBtnEl) toggleBtnEl.dataset.action = 'deactivate';
                }
            } catch (_) { /* ignore */ }
        }

        window.closeStudentActionsModal = function () {
            document.getElementById('studentActionsModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('studentActionsModal').addEventListener('click', function (e) {
            if (e.target === this) {
                closeStudentActionsModal();
            }
        });


        // Utility Functions
        window.showMessage = function (message, type) {
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

        // Edit Student Modal logic
        window.openEditStudentModal = function () {
            if (!selectedStudentId) {
                showMessage('No student selected', 'error');
                return;
            }

            // Close actions modal if open
            closeStudentActionsModal();

            // Populate hidden fields
            const sectionInput = document.getElementById('editStudentSection');
            const idInput = document.getElementById('editStudentId');
            if (sectionInput) sectionInput.value = currentSection || '';
            if (idInput) idInput.value = selectedStudentId;

            // Fetch students for this section, then find the selected one to prefill
            fetch(`/teacher/sections/students/${encodeURIComponent(currentSection)}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(async response => {
                let payload;
                try { payload = await response.json(); } catch (_) { payload = null; }
                if (response.ok && payload && Array.isArray(payload.students)) {
                    const student = payload.students.find(s => String(s.user_id) === String(selectedStudentId));
                    if (student) {
                        document.getElementById('editStudentFirstName').value = student.firstname || '';
                        document.getElementById('editStudentMiddleName').value = student.middlename || '';
                        document.getElementById('editStudentLastName').value = student.lastname || '';
                        document.getElementById('editStudentEmail').value = student.email || '';
                        document.getElementById('editStudentSchoolYear').value = student.school_year || '';
                    }
                }
            })
            .catch(() => { /* ignore prefill errors */ });

            document.getElementById('editStudentModal').classList.remove('hidden');
        }

        window.closeEditStudentModal = function () {
            document.getElementById('editStudentModal').classList.add('hidden');
            document.getElementById('editStudentForm').reset();
        }

        // Handle edit form submit
        document.getElementById('editStudentForm').addEventListener('submit', function (e) {
            e.preventDefault();
            if (!selectedStudentId) {
                showMessage('No student selected', 'error');
                return;
            }

            const formData = new FormData(this);
            // Ensure method override for Laravel
            formData.set('_method', 'PUT');

            fetch(`/teacher/sections/students/${selectedStudentId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(async response => {
                let data;
                try { data = await response.json(); } catch (_) { data = {}; }
                if (response.ok && data.success) {
                    showMessage(data.message || 'Student updated successfully', 'success');
                    closeEditStudentModal();
                    if (typeof loadStudents === 'function') {
                        loadStudents(currentSection);
                    }
                } else {
                    const msg = (data && (data.error || data.message)) || 'Failed to update student';
                    showMessage(msg, 'error');
                }
            })
            .catch(() => {
                showMessage('An error occurred while updating the student', 'error');
            });
        });

        // Toggle activation
        window.toggleActivation = function () {
            if (!selectedStudentId) {
                showMessage('No student selected', 'error');
                return;
            }

            const action = document.getElementById('toggleActivationBtn')?.dataset?.action;
            const isActivate = action === 'activate';
            const url = isActivate
                ? `/teacher/sections/students/${selectedStudentId}/activate`
                : `/teacher/sections/students/${selectedStudentId}/deactivate`;
            const confirmMsg = isActivate ? 'Activate this student account?' : 'Set this student account to inactive?';
            if (!confirm(confirmMsg)) return;

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(async response => {
                let data;
                try { data = await response.json(); } catch (_) { data = {}; }
                if (response.ok && data.success) {
                    showMessage(data.message || (isActivate ? 'Student activated' : 'Student deactivated'), 'success');
                    closeStudentActionsModal();
                    if (typeof loadStudents === 'function') {
                        loadStudents(currentSection);
                    }
                } else {
                    const msg = (data && (data.error || data.message)) || 'Request failed';
                    showMessage(msg, 'error');
                }
            })
            .catch(() => {
                showMessage('An error occurred while updating the student status', 'error');
            });
        }
    </script>
@endsection