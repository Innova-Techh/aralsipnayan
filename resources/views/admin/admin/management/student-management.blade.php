@extends('admin.admin.layouts.app')

@section('title', 'Student Management - ' . $section->name)

@section('content')
    <div class="p-6 bg-gray-50 min-h-screen">
        <!-- Breadcrumb -->
        <nav class="flex mb-4" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('admin.management.sections') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                        <i class="fas fa-book mr-2"></i>
                        Sections
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                        <span class="ml-1 text-sm font-medium text-gray-900 md:ml-2">{{ $section->name }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $section->name }}</h1>
                <p class="text-gray-600 text-sm mt-1">Manage students in this section</p>
            </div>

            <div class="flex gap-3 mt-4 sm:mt-0">
                <button onclick="openAddStudentModal()"
                    class="flex items-center gap-2 px-4 py-2 bg-blue-900 text-white rounded-lg text-sm font-medium hover:bg-blue-800 transition">
                    <i class="fas fa-user-plus"></i> Add Student
                </button>
            </div>
        </div>

        <!-- Section Info Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Total Students -->
            <div class="bg-white rounded-lg border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Total Students</p>
                        <h3 class="text-2xl font-bold text-gray-900">{{ $section->students_count ?? 0 }}</h3>
                        <p class="text-xs text-gray-500 mt-1">Enrolled students</p>
                    </div>
                    <div class="bg-blue-50 rounded-lg p-3">
                        <i class="fas fa-users text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Active Students -->
            <div class="bg-white rounded-lg border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Active Students</p>
                        <h3 class="text-2xl font-bold text-gray-900">{{ $section->active_students_count ?? 0 }}</h3>
                        <p class="text-xs text-gray-500 mt-1">Currently active</p>
                    </div>
                    <div class="bg-green-50 rounded-lg p-3">
                        <i class="fas fa-user-check text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Average Performance -->
            <div class="bg-white rounded-lg border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Avg Performance</p>
                        <h3 class="text-2xl font-bold text-gray-900">85%</h3>
                        <p class="text-xs text-gray-500 mt-1">Section average</p>
                    </div>
                    <div class="bg-purple-50 rounded-lg p-3">
                        <i class="fas fa-chart-line text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Assigned Teacher -->
            <div class="bg-white rounded-lg border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Teacher</p>
                        <h3 class="text-base font-bold text-gray-900">Not Assigned</h3>
                        <p class="text-xs text-gray-500 mt-1">Section adviser</p>
                    </div>
                    <div class="bg-orange-50 rounded-lg p-3">
                        <i class="fas fa-chalkboard-teacher text-orange-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Students Table -->
        <div class="bg-white rounded-lg border border-gray-200">
            <div class="p-5 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Student List</h2>
                <p class="text-sm text-gray-600 mt-1">All students enrolled in this section</p>
            </div>

            <div class="p-5">
                <!-- Search and Filter Bar -->
                <div class="flex flex-col sm:flex-row gap-3 mb-5">
                    <div class="flex-1">
                        <div class="relative">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <input type="text" id="searchInput" placeholder="Search by name, email, or student ID..."
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>

                    <div class="w-full sm:w-48">
                        <select id="statusFilter"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="all">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="archived">Archived</option>
                        </select>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Student ID
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Name
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Email
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Performance
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Enrolled Date
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="studentTableBody">
                            @forelse($students as $student)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $student->studentProfile->student_id ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                @if($student->studentProfile && $student->studentProfile->avatar_url)
                                                    <img class="h-10 w-10 rounded-full object-cover"
                                                        src="{{ asset('storage/' . $student->studentProfile->avatar_url) }}"
                                                        alt="{{ $student->studentProfile->firstname }}">
                                                @else
                                                    <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                        <span class="text-blue-600 font-medium text-sm">
                                                            {{ $student->studentProfile ? strtoupper(substr($student->studentProfile->firstname, 0, 1) . substr($student->studentProfile->lastname, 0, 1)) : strtoupper(substr($student->username, 0, 2)) }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $student->studentProfile ? $student->studentProfile->firstname . ' ' . $student->studentProfile->lastname : $student->username }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    Grade {{ $student->studentProfile->grade_level ?? 'N/A' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $student->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusColors = [
                                                'active' => 'bg-green-100 text-green-800',
                                                'inactive' => 'bg-yellow-100 text-yellow-800',
                                                'archived' => 'bg-gray-100 text-gray-800',
                                            ];
                                            $statusColor = $statusColors[$student->status] ?? 'bg-gray-100 text-gray-800';
                                        @endphp
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColor }}">
                                            {{ ucfirst($student->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-1">
                                                <div class="text-sm font-medium text-gray-900">85%</div>
                                                <div class="w-full bg-gray-200 rounded-full h-1.5 mt-1">
                                                    <div class="bg-blue-600 h-1.5 rounded-full" style="width: 85%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $student->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-2">
                                            <button onclick="openEditStudentModal({{ $student->id }})"
                                                class="text-blue-600 hover:text-blue-900 transition" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button onclick="openArchiveStudentModal({{ $student->id }}, '{{ $student->studentProfile ? $student->studentProfile->firstname . ' ' . $student->studentProfile->lastname : $student->username }}')"
                                                class="text-yellow-600 hover:text-yellow-900 transition" title="Archive">
                                                <i class="fas fa-archive"></i>
                                            </button>
                                            <button onclick="viewStudentDetails({{ $student->id }})"
                                                class="text-gray-600 hover:text-gray-900 transition" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <i class="fas fa-users text-gray-300 text-5xl mb-4"></i>
                                            <h3 class="text-lg font-medium text-gray-900 mb-1">No students found</h3>
                                            <p class="text-sm text-gray-500">Get started by adding your first student</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Results Info -->
                <div class="mt-4 text-sm text-gray-600 text-center">
                    Showing {{ $students->count() }} student(s) in this section
                </div>
            </div>
        </div>

        <!-- Add Student Modal -->
        @include('admin.components.add-student-modal')
        
        <!-- Edit Student Modal -->
        @include('admin.components.edit-student-modal')
        
        <!-- Archive Student Modal -->
        @include('admin.components.archive-student-modal')
    </div>
@endsection

@push('scripts')
    <script>
        function openAddStudentModal() {
            document.getElementById('addStudentModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeAddStudentModal() {
            document.getElementById('addStudentModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
            document.getElementById('addStudentForm').reset();
        }

        function openEditStudentModal(studentId) {
            // Fetch student data and populate modal
            document.getElementById('editStudentModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeEditStudentModal() {
            document.getElementById('editStudentModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function openArchiveStudentModal(studentId, studentName) {
            document.getElementById('archiveStudentId').value = studentId;
            document.getElementById('archiveStudentName').textContent = studentName;
            document.getElementById('archiveStudentModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeArchiveStudentModal() {
            document.getElementById('archiveStudentModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Close modals when clicking outside
        document.addEventListener('DOMContentLoaded', function () {
            const modals = ['addStudentModal', 'editStudentModal', 'archiveStudentModal'];
            modals.forEach(modalId => {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.addEventListener('click', function (e) {
                        if (e.target === this) {
                            this.classList.add('hidden');
                            document.body.style.overflow = 'auto';
                        }
                    });
                }
            });
        });
    </script>
@endpush