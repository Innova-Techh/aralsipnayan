@extends('admin.teacher.layouts.app')

@section('title', 'AralSipnayan')

@section('content')   
    <div class="min-h-screen bg-gray-50">
        <!-- Header Section -->
        <div class="bg-white border-b border-gray-200 rounded-2xl px-8 py-6">
            <div class="flex items-center justify-between mb-4">
                <button onclick="window.location.href='{{ route('teacher.sections') }}'"
                    class="flex items-center gap-2 text-gray-600 hover:text-gray-900 transition-colors">
                    <span class="material-symbols-outlined">arrow_back</span>
                    <span class="font-medium">Back to Sections</span>
                </button>
                <div class="flex items-center gap-3">
                    <!-- Add Student Button -->
                    <button onclick="openAddStudentModal()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors flex items-center">
                        <span class="material-symbols-outlined mr-2">person_add</span>
                        Add New Student
                    </button>
                    <!-- Create Assessment Button -->
                    <button id="createAssessmentBtn"
                        class="bg-blue-600 text-white px-5 py-2.5 rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2 shadow-sm">
                        <span class="material-symbols-outlined">add</span>
                        Create Assessment
                    </button>
                </div>
            </div>

            <!-- Section Title -->
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900">{{ $section['name'] ?? 'Section Name' }}</h1>
                <p class="text-gray-600 mt-1">Basic Mathematics and Arithmetic</p>
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
                    <div class="text-3xl font-bold text-gray-900">{{ $averageScore}}%</div>
                    <p class="text-xs text-gray-500 mt-1">Section average</p>
                </div>

                <!-- Average Level -->
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-medium text-gray-600">Average Level</span>
                        <span class="material-symbols-outlined text-gray-400">trending_up</span>
                    </div>
                    <div class="text-3xl font-bold text-gray-900">{{ $averageLevel ?? 1 }}</div>
                    <p class="text-xs text-gray-500 mt-1">Section average level</p>
                </div>

                <!-- Top Performer -->
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-medium text-gray-600">Top Performer</span>
                        <span class="material-symbols-outlined text-gray-400">emoji_events</span>
                    </div>
                    <div class="text-2xl font-bold text-gray-900">{{ $topPerformer ?? 'Maria Santos' }}</div>
                    <p class="text-xs text-gray-500 mt-1">Rank 1 {{$section['raw_name']}} </p>
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
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    Rank</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    LRN</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    Student</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    Level & Rank</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    Points</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    Last Activity</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    Status</th>
                                <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">
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
                                            <div class="flex items-center justify-center w-8 h-8 font-semibold text-gray-600">#{{ $index + 1 }}</div>
                                        @endif
                                    </td>

                                     <!-- Student LRN -->
                                     <td class="px-4 py-4">
                                        <div class="flex items-center gap-3">
                                            <div>
                                                <div class="font-semibold text-gray-900">{{ $student['student_id'] }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Student Info -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div>
                                                <div class="font-semibold text-gray-900">{{ $student['name'] }}</div>
                                                <div class="text-sm text-gray-500">{{ $student['email'] }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Level & Rank -->
                                    <td class="px-6 py-4">
                                        <div class="space-y-1">
                                            <div class="flex items-center gap-2">
                                                <span class="text-sm font-semibold text-gray-900">Level {{ $student['current_level'] ?? 1 }}</span>
                                                <span class="text-xs text-gray-500">({{ $student['points_in_current_level'] ?? 0 }}/60 pts)</span>
                                            </div>
                                            <div class="text-xs text-gray-600">{{ $student['current_rank'] ?? 'Math Explorer' }}</div>
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

                                    <!-- Actions (Combined: inline button + dropdown from josh-branch) -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-center space-x-2">

                                            <!-- More Actions Dropdown (from josh-branch with main-branch actions) -->
                                            <div class="relative" x-data="{ open: false }">
                                                <button class="p-2 hover:bg-gray-100 rounded-lg transition-colors"
                                                    @click="open = !open; selectedStudentId = {{ $student['id'] }}">
                                                    <span class="material-symbols-outlined text-gray-600">more_vert</span>
                                                </button>

                                                <!-- Dropdown Menu -->
                                                <div x-show="open" @click.away="open = false"
                                                    class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50"
                                                    style="display: none;">
                                                    <!-- View Profile (from josh-branch) -->
                                                    <button
                                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2"
                                                        onclick="viewProfile('{{ $student['id'] }}')">
                                                        <span class="material-symbols-outlined text-blue-600 text-sm">visibility</span>
                                                        View Profile
                                                    </button>

                                                    <!-- Edit Details (from main-branch) -->
                                                    <button
                                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2"
                                                        onclick="openEditStudentModal({{ $student['id'] }})">
                                                        <span class="material-symbols-outlined text-blue-600 text-sm">edit</span>
                                                        Edit Details
                                                    </button>

                                                    <!-- Assign Assessment (from josh-branch) 
                                                    <button
                                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2"
                                                        onclick="assignAssessment('{{ $student['id'] }}', '{{ $student['name'] }}')">
                                                        <span class="material-symbols-outlined text-green-600 text-sm">assignment</span>
                                                        Assign Assessment
                                                    </button> -->

                                                    <!-- Divider -->
                                                    <div class="border-t border-gray-200 my-1"></div>

                                                    <!-- Toggle Activation (from main-branch) -->
                                                    @php
                                                        $isActive = strtolower($student['status'] ?? 'active') === 'active';
                                                    @endphp
                                                    <button
                                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2"
                                                        onclick="toggleActivation({{ $student['id'] }}, '{{ $isActive ? 'deactivate' : 'activate' }}')">
                                                        <span class="material-symbols-outlined {{ $isActive ? 'text-red-600' : 'text-green-600' }} text-sm">
                                                            {{ $isActive ? 'person_off' : 'person' }}
                                                        </span>
                                                        {{ $isActive ? 'Deactivate Account' : 'Activate Account' }}
                                                    </button>

                                                    <!-- Remove from Section (from josh-branch) -->
                                                    <button
                                                        class="w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-50 flex items-center gap-2"
                                                        onclick="removeFromSection('{{ $student['id'] }}', '{{ $student['name'] }}')">
                                                        <span class="material-symbols-outlined text-red-600 text-sm">person_remove</span>
                                                        Remove from Section
                                                    </button>
                                                </div>
                                            </div>
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

    <!-- Global Message Toasts (from main-branch) -->
    <div id="messageContainer"
         class="hidden fixed top-4 left-1/2 -translate-x-1/2 z-[60] space-y-2 w-80"
         aria-live="polite">
        <!-- Success -->
        <div id="successMessage"
            class="invisible flex items-start gap-3 rounded-lg border border-green-200 bg-green-50 p-4 shadow">
            <span class="material-symbols-outlined text-green-600">check_circle</span>
            <div class="text-sm text-green-800">
                <span id="successText"></span>
            </div>
        </div>
        <!-- Error -->
        <div id="errorMessage"
            class="invisible flex items-start gap-3 rounded-lg border border-red-200 bg-red-50 p-4 shadow">
            <span class="material-symbols-outlined text-red-600">error</span>
            <div class="text-sm text-red-800">
                <span id="errorText"></span>
            </div>
        </div>
    </div>

    <!-- Add Student Modal (from main-branch) -->
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
                        <label for="studentEmail" class="block text-sm font-medium text-gray-700 mb-2">Email (Should be Unique)</label>
                        <input type="email" id="studentEmail" name="email" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                     <!-- Gender -->
                     <div>
                        <label for="addStudentGender" class="block text-sm font-medium text-gray-700 mb-2">
                            Gender <span class="text-red-500">*</span>
                        </label>
                        <select id="addStudentGender" name="gender" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Select Gender</option>
                            <option value="male">male</option>
                            <option value="female">female</option>
                            <option value="other">other</option>
                        </select>
                        <p id="addGender_error" class="error-message text-red-500 text-xs mt-1"></p>
                    </div>
                    <!-- Info Box -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-4">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-info-circle text-blue-600 mt-0.5"></i>
                            <div class="text-sm text-blue-800">
                                <p class="font-medium mb-1">Default Credentials</p>
                                <ul class="list-disc list-inside space-y-1 text-xs">
                                    <li>Username will be auto-generated based on section</li>
                                    <li>Default password: <strong>123</strong></li>
                                    <li>Student ID (LRN) will be auto-generated</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3 mt-4">
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

    <!-- Edit Student Modal (from main-branch) -->
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
                    <div>
                        <label for="edit_gender" class="block text-sm font-medium text-gray-700 mb-2">
                            Gender <span class="text-red-500">*</span>
                        </label>
                        <select id="edit_gender" name="gender"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Select Gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                        <p id="edit_gender_error" class="error-message text-red-500 text-xs mt-1"></p>
                    </div>
                    <div class="mb-4">
                        <label for="editStudentPassword" class="block text-sm font-medium text-gray-700 mb-2">Password (Leave blank to keep current)</label>
                        <input type="password" id="editStudentPassword" name="password" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
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

    <script>
        let selectedStudentId = null;
        const currentSection = '{{ $section['raw_name'] ?? ($section['section'] ?? '') }}';

        // Add Student Modal Functions (from main-branch)
        window.openAddStudentModal = function () {
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

        // Add Student Form Submission (from main-branch)
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
                    } else {
                        location.reload();
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

        // Edit Student Modal Functions (from main-branch)
        window.openEditStudentModal = function (studentId) {
            selectedStudentId = studentId || selectedStudentId;
            console.log('edit_gender element:', document.getElementById('edit_gender'));
            if (!selectedStudentId) {
                showMessage('No student selected', 'error');
                return;
            }

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
                        document.getElementById('edit_gender').value = student.gender || '';
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

        // Handle edit form submit (from main-branch)
        document.getElementById('editStudentForm').addEventListener('submit', function (e) {
            e.preventDefault();
            if (!selectedStudentId) {
                showMessage('No student selected', 'error');
                return;
            }

            const formData = new FormData(this);
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
                    } else {
                        location.reload();
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

        // Utility Functions (from main-branch)
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

        // Toggle activation (from main-branch, modified to work with dropdown)
        window.toggleActivation = function (studentId, action) {
            const id = studentId || selectedStudentId;
            
            if (!id) {
                showMessage('No student selected', 'error');
                return;
            }

            const isActivate = action === 'activate';
            const url = isActivate
                ? `/teacher/sections/students/${id}/activate`
                : `/teacher/sections/students/${id}/deactivate`;
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
                    if (typeof loadStudents === 'function') {
                        loadStudents(currentSection);
                    } else {
                        location.reload();
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

        // Action functions (from josh-branch)
        window.viewProfile = function(studentId) {
            console.log('View profile for student:', studentId);
            window.location.href = `/teacher/students/${studentId}/profile`;
        }

       // window.assignAssessment = function(studentId, studentName) {
           // console.log('Assign assessment to:', studentName, studentId);
            // Implement assign assessment logic
           // alert(`Assign assessment to ${studentName}`);
       // }


        window.removeFromSection = function(studentId, studentName) {
            console.log('Remove from section:', studentName, studentId);
            if (confirm(`Are you sure you want to remove ${studentName} from this section?`)) {
                // Implement actual removal via API
                fetch(`/teacher/sections/${studentId}/remove`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ section: currentSection })
                })
                .then(async response => {
                    let data;
                    try { data = await response.json(); } catch (_) { data = {}; }
                    if (response.ok && data.success) {
                        showMessage(`${studentName} removed from section`, 'success');
                        if (typeof loadStudents === 'function') {
                            loadStudents(currentSection);
                        } else {
                            location.reload();
                        }
                    } else {
                        showMessage('Failed to remove student', 'error');
                    }
                })
                .catch(() => {
                    showMessage('An error occurred', 'error');
                });
            }
        }

        // Alpine.js initialization for dropdowns (from josh-branch)
        document.addEventListener('alpine:init', () => {
            Alpine.data('dropdown', () => ({
                open: false,
                toggle() {
                    this.open = !this.open;
                },
                close() {
                    this.open = false;
                }
            }));
        });
    </script>
@endsection