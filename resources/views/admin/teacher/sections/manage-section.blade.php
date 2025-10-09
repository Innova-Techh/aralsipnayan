@extends('admin.teacher.layouts.app')

@section('title', 'Section Details - Aralsipnayan')

@section('content')
    <div class="min-h-screen bg-gray-50">
        <!-- Header Section -->
        <div class="rounded-xl border border-gray-200 px-8 py-6">
            <div class="flex items-center justify-between mb-4">
                <button onclick="window.location.href='{{ route('teacher.sections') }}'"
                    class="flex items-center gap-2 text-gray-600 hover:text-gray-900 transition-colors">
                    <span class="material-symbols-outlined">arrow_back</span>
                    <span class="font-medium">Back to Sections</span>
                </button>
                <button id="createAssessmentBtn"
                    class="bg-blue-600 text-white px-5 py-2.5 rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2 shadow-sm">
                    <span class="material-symbols-outlined">add</span>
                    Create Assessment
                </button>
            </div>

            <!-- Section Title -->
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900">{{ $section['name'] ?? 'Section Name' }}</h1>
                <p class="text-gray-600 mt-1">Basic Mathematics and Arithmetic</p>
            </div>

            <!-- Section Info Grid -->
            <div class="grid grid-cols-4 gap-6 bg-gray-100 rounded-2l p-6">
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
                                    Rank
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    Student
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    Overall Score
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    Progress
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    Points
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    Last Activity
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    Status
                                </th>
                                <th
                                    class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    Actions
                                </th>
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
                                            <div class="flex-1">
                                                <div class="font-semibold text-gray-900">{{ $student['name'] }}</div>
                                                <div class="text-sm text-gray-500">{{ $student['email'] }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Overall Score -->
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <span class="text-lg font-bold text-blue-600">{{ $student['score'] }}%</span>
                                            <div class="text-xs text-gray-500 mt-1">
                                                {{ $student['completed'] }}/{{ $student['total'] }} assessments
                                            </div>
                                        </div>
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
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            active
                                        </span>
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-center space-x-2">
                                            <!-- Send Message Button -->
                                            <button class="p-2 hover:bg-gray-100 rounded-lg transition-colors group relative"
                                                onclick="sendMessage('{{ $student['id'] }}', '{{ $student['name'] }}')"
                                                title="Send Message">
                                                <span
                                                    class="material-symbols-outlined text-gray-600 group-hover:text-orange-600">mail</span>
                                            </button>

                                            <!-- More Actions Dropdown -->
                                            <div class="relative" x-data="{ open: false }">
                                                <button class="p-2 hover:bg-gray-100 rounded-lg transition-colors"
                                                    @click="open = !open">
                                                    <span class="material-symbols-outlined text-gray-600">more_vert</span>
                                                </button>

                                                <!-- Dropdown Menu -->
                                                <div x-show="open" @click.away="open = false"
                                                    class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                                                    <!-- View Profile -->
                                                    <button
                                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2"
                                                        onclick="viewProfile('{{ $student['id'] }}')">
                                                        <span
                                                            class="material-symbols-outlined text-blue-600 text-sm">visibility</span>
                                                        View Profile
                                                    </button>

                                                    <!-- Assign Assessment -->
                                                    <button
                                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2"
                                                        onclick="assignAssessment('{{ $student['id'] }}', '{{ $student['name'] }}')">
                                                        <span
                                                            class="material-symbols-outlined text-green-600 text-sm">assignment</span>
                                                        Assign Assessment
                                                    </button>

                                                    <!-- Send Message -->
                                                    <button
                                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2"
                                                        onclick="sendMessage('{{ $student['id'] }}', '{{ $student['name'] }}')">
                                                        <span
                                                            class="material-symbols-outlined text-orange-600 text-sm">mail</span>
                                                        Send Message
                                                    </button>

                                                    <!-- Remove from Section -->
                                                    <button
                                                        class="w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-50 flex items-center gap-2"
                                                        onclick="removeFromSection('{{ $student['id'] }}', '{{ $student['name'] }}')">
                                                        <span
                                                            class="material-symbols-outlined text-red-600 text-sm">person_remove</span>
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

    <script>
        // Action functions
        function viewProfile(studentId) {
            console.log('View profile for student:', studentId);
            // Use Laravel route helper to generate the correct URL
            window.location.href = "{{ route('teacher.students.profile', ['student' => '__STUDENT_ID__']) }}".replace('__STUDENT_ID__', studentId);
        }

        function assignAssessment(studentId, studentName) {
            console.log('Assign assessment to:', studentName, studentId);
            // Implement assign assessment logic
            // This could open a modal or redirect to assessment assignment page
            alert(`Assign assessment to ${studentName}`);
        }

        function sendMessage(studentId, studentName) {
            console.log('Send message to:', studentName, studentId);
            // Implement send message logic
            // This could open a messaging modal
            alert(`Send message to ${studentName}`);
        }

        function removeFromSection(studentId, studentName) {
            console.log('Remove from section:', studentName, studentId);
            // Implement remove from section logic
            if (confirm(`Are you sure you want to remove ${studentName} from this section?`)) {
                // Perform removal action
                alert(`${studentName} removed from section`);
            }
        }

        // Alpine.js initialization for dropdowns
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