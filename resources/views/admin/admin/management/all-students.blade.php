@extends('admin.admin.layouts.app')

@section('title', 'All Students Management')

@section('content')
    <div class="p-6 bg-gray-50 min-h-screen">
        <!-- Breadcrumb -->
        <nav class="flex mb-4" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                        <i class="fas fa-home mr-2"></i>
                        Dashboard
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                        <span class="ml-1 text-sm font-medium text-gray-900 md:ml-2">All Students</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">All Students</h1>
                <p class="text-gray-600 text-sm mt-1">Manage all students across all sections</p>
            </div>

            <div class="flex gap-3 mt-4 sm:mt-0">
                <button onclick="openAddStudentModal()"
                    class="flex items-center gap-2 px-4 py-2 bg-blue-900 text-white rounded-lg text-sm font-medium hover:bg-blue-800 transition">
                    <i class="fas fa-user-plus"></i> Add Student
                </button>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Total Students -->
            <div class="bg-white rounded-lg border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Total Students</p>
                        <h3 class="text-2xl font-bold text-gray-900">{{ $totalStudents }}</h3>
                        <p class="text-xs text-gray-500 mt-1">All students</p>
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
                        <h3 class="text-2xl font-bold text-gray-900">{{ $activeStudents }}</h3>
                        <p class="text-xs text-gray-500 mt-1">Currently active</p>
                    </div>
                    <div class="bg-green-50 rounded-lg p-3">
                        <i class="fas fa-user-check text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Archived Students -->
            <div class="bg-white rounded-lg border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Archived Students</p>
                        <h3 class="text-2xl font-bold text-gray-900">{{ $archivedStudents }}</h3>
                        <p class="text-xs text-gray-500 mt-1">Archived</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <i class="fas fa-archive text-gray-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Total Sections -->
            <div class="bg-white rounded-lg border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Total Sections</p>
                        <h3 class="text-2xl font-bold text-gray-900">{{ $totalSections }}</h3>
                        <p class="text-xs text-gray-500 mt-1">Available sections</p>
                    </div>
                    <div class="bg-purple-50 rounded-lg p-3">
                        <i class="fas fa-book text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Students Table -->
        <div class="bg-white rounded-lg border border-gray-200">
            <div class="p-5 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Student List</h2>
                <p class="text-sm text-gray-600 mt-1">All students in the system</p>
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
                            <option value="archive">Archived</option>
                        </select>
                    </div>

                    <div class="w-full sm:w-48">
                        <select id="sectionFilter"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="all">All Sections</option>
                            <option value="unassigned">Unassigned</option>
                            @foreach($sections as $section)
                                <option value="{{ $section }}">{{ $section }}</option>
                            @endforeach
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
                                    Student Username
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
                                    Section
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
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
                                <tr class="hover:bg-gray-50 transition student-row" data-section="{{ $student->studentProfile->section ?? 'unassigned' }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 student-id">
                                            {{ $student->username ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900 student-name">
                                                    {{ $student->studentProfile ? $student->studentProfile->firstname . ' ' . $student->studentProfile->lastname : $student->username }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 student-email">{{ $student->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 student-section">
                                            @if($student->studentProfile && $student->studentProfile->section)
                                                <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-medium">
                                                    {{ $student->studentProfile->section }}
                                                </span>
                                            @else
                                                <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-medium">
                                                    Unassigned
                                                </span>
                                            @endif
                                        </div>
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
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColor }} student-status">
                                            {{ ucfirst($student->status) }}
                                        </span>
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
                                            <button onclick="openArchiveStudentModal({{ $student->id }}, '{{ $student->studentProfile ? $student->studentProfile->firstname . ' ' . $student->studentProfile->lastname : $student->username }}', '{{ $student->status }}')"
                                                class="text-yellow-600 hover:text-yellow-900 transition" title="{{ $student->status === 'active' ? 'Archive' : 'Activate' }}">
                                                <i class="fas fa-{{ $student->status === 'active' ? 'archive' : 'check' }}"></i>
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
                    Showing <span id="visibleCount">{{ $students->count() }}</span> of {{ $students->count() }} student(s)
                </div>
            </div>
        </div>

        <!-- Archive/Activate Student Modal -->
        <div id="archiveStudentModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-1/4 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                    <div class="flex items-center gap-3">
                        <div id="archiveStudentIcon" class="bg-yellow-100 rounded-full p-2">
                            <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
                        </div>
                        <h3 id="archiveStudentTitle" class="text-xl font-semibold text-gray-900">Archive Student</h3>
                    </div>
                    <button onclick="closeArchiveStudentModal()" class="text-gray-400 hover:text-gray-600 transition">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="mt-4">
                    <p id="archiveStudentMessage" class="text-sm text-gray-600 mb-4">
                        Are you sure you want to archive <span id="archiveStudentName"
                            class="font-semibold text-gray-900"></span>?
                    </p>
                    <div id="archiveStudentInfo" class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-info-circle text-yellow-600 mt-0.5"></i>
                            <div class="text-xs text-yellow-800">
                                <p class="font-medium mb-1">This action will:</p>
                                <ul id="archiveStudentActions" class="list-disc list-inside space-y-1">
                                    <li>Set the student status to "Inactive"</li>
                                    <li>Keep them from active class lists</li>
                                    <li>Preserve all their data and records</li>
                                    <li>Can be reversed by reactivating the student</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <form id="archiveStudentForm">
                        <input type="hidden" id="archiveStudentId" name="student_id">
                        <input type="hidden" id="archiveStudentCurrentStatus" name="current_status">

                        <!-- Modal Footer -->
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                            <button type="button" onclick="closeArchiveStudentModal()"
                                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition">
                                Cancel
                            </button>
                            <button type="submit" id="archiveStudentButton"
                                class="px-4 py-2 bg-yellow-600 text-white rounded-lg text-sm font-medium hover:bg-yellow-700 transition">
                                <i class="fas fa-archive mr-2"></i> Archive Student
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Add Student Modal -->
        <div id="addStudentModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-lg bg-white">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-semibold text-gray-900">Add New Student</h3>
                    <button onclick="closeAddStudentModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <form id="addStudentForm">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">First Name *</label>
                            <input type="text" id="addStudentFirstName" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Middle Name</label>
                            <input type="text" id="addStudentMiddleName"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Last Name *</label>
                            <input type="text" id="addStudentLastName" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                            <input type="email" id="addStudentEmail" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Gender *</label>
                            <select id="addStudentGender" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Section</label>
                            <select id="addStudentSection"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Unassigned</option>
                                @foreach($sections as $section)
                                    <option value="{{ $section }}">{{ $section }}</option>
                                @endforeach
                            </select>
                        </div>
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

                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" onclick="closeAddStudentModal()"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-300 transition">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-900 text-white rounded-lg text-sm font-medium hover:bg-blue-800 transition">
                            <i class="fas fa-user-plus mr-2"></i> Add Student
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Student Modal -->
        <div id="editStudentModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-lg bg-white">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-semibold text-gray-900">Edit Student</h3>
                    <button onclick="closeEditStudentModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <form id="editStudentForm">
                    <input type="hidden" id="editStudentId">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Student ID/LRN *</label>
                            <input type="text" id="editStudentLRN" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">First Name *</label>
                            <input type="text" id="editFirstname" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Middle Name</label>
                            <input type="text" id="editMiddlename"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Last Name *</label>
                            <input type="text" id="editLastname" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                            <input type="email" id="editEmail" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Gender *</label>
                            <select id="editStudentGender" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Section</label>
                            <select id="editStudentSection"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Unassigned</option>
                                @foreach($sections as $section)
                                    <option value="{{ $section }}">{{ $section }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                            <select id="editStatus" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="archive">Archive</option>
                            </select>
                        </div>

                        
                    </div>
                    <!-- Password -->
                    <div class = "pt-4">
                        <label for="editPassword" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-key text-gray-400 mr-2"></i>Password <span class="text-red-500"></span>(Leave blank to keep current)
                        </label>
                        <input type="password" id="editPassword" name="password"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="*******">
                    </div>

                     <!-- Information Note -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-6">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-info-circle text-blue-600 mt-1"></i>
                            <div class="text-sm text-blue-800">
                                <p class="font-medium mb-1">Note:</p>
                                <ul class="list-disc list-inside space-y-1 text-xs">
                                    <li>Changing the email and password will update the student's login credentials</li>
                                    <li>Setting status to "Inactive" will prevent the student from logging in</li>
                                    <li>Setting status to "Archive" will remove the student from their section</li>
                                    <li>Can all be reverted by setting status to "Active"</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" onclick="closeEditStudentModal()"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-300 transition">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-900 text-white rounded-lg text-sm font-medium hover:bg-blue-800 transition">
                            <i class="fas fa-save mr-2"></i> Update Student
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function displayErrors(errors, prefix = '') {
        // Clear all previous error messages
        document.querySelectorAll('[id$="_error"]').forEach(el => {
            el.textContent = '';
        });

        Object.keys(errors).forEach(key => {
            // Generate all possible element IDs that might match
            const variants = [];

            if (prefix) {
                variants.push(`${prefix}_${key}_error`); // edit_status_error
                variants.push(`${prefix}${key.charAt(0).toUpperCase() + key.slice(1)}_error`); // editStatus_error
            }

            variants.push(`${key}_error`); // status_error
            variants.push(`${key.charAt(0).toUpperCase() + key.slice(1)}_error`); // Status_error (edge camel case)

            // Find any matching error element
            const errorElement = variants
                .map(id => document.getElementById(id))
                .find(el => el !== null);

            if (errorElement) {
                errorElement.textContent = errors[key][0];
            } else {
                console.warn(`⚠️ No element found for error field: ${key} (${variants.join(', ')})`);
            }
        });
        }
        // ===============================================
        // ADD STUDENT FORM SUBMISSION
        // ===============================================
        document.getElementById('addStudentForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = {
                firstname: document.getElementById('addStudentFirstName').value,
                middlename: document.getElementById('addStudentMiddleName').value,
                lastname: document.getElementById('addStudentLastName').value,
                email: document.getElementById('addStudentEmail').value,
                gender: document.getElementById('addStudentGender').value,
                section: document.getElementById('addStudentSection').value || null
            };

            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Adding...';
            
            fetch('/admin/management/all-students', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    closeAddStudentModal();
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    if (data.errors) {
                        let errorMessage = 'Validation errors:\n';
                        Object.values(data.errors).forEach(error => {
                            errorMessage += error[0] + '\n';
                        });
                        showNotification(errorMessage, 'error');
                    } else {
                        showNotification(data.message || 'Failed to add student', 'error');
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred while adding student', 'error');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });

        // ===============================================
        // ARCHIVE/ACTIVATE STUDENT
        // ===============================================
        function openArchiveStudentModal(studentId, studentName, currentStatus) {
            document.getElementById('archiveStudentId').value = studentId;
            document.getElementById('archiveStudentCurrentStatus').value = currentStatus;
            
            const isActive = currentStatus === 'active';
            const title = document.getElementById('archiveStudentTitle');
            const message = document.getElementById('archiveStudentMessage');
            const button = document.getElementById('archiveStudentButton');
            const icon = document.getElementById('archiveStudentIcon');
            const info = document.getElementById('archiveStudentInfo');
            const actions = document.getElementById('archiveStudentActions');
            
            if (isActive) {
                title.textContent = 'Archive Student';
                message.innerHTML = `Are you sure you want to archive <span class="font-semibold text-gray-900">${studentName}</span>?`;
                button.innerHTML = '<i class="fas fa-archive mr-2"></i> Archive Student';
                button.className = 'px-4 py-2 bg-yellow-600 text-white rounded-lg text-sm font-medium hover:bg-yellow-700 transition';
                icon.className = 'bg-yellow-100 rounded-full p-2';
                icon.innerHTML = '<i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>';
                info.className = 'bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4';
                actions.innerHTML = `
                    <li>Set ${studentName} status to "Archived"</li>
                    <li>${studentName} will NOT be able to log in</li>
                    <li>${studentName} will be REMOVED from their section</li>
                    <li>All ${studentName}'s data and records are preserved</li>
                    <li>Can be reversed by reactivating ${studentName}</li>
                `;
            } else {
                title.textContent = 'Activate Student';
                message.innerHTML = `Are you sure you want to activate <span class="font-semibold text-gray-900">${studentName}</span>?`;
                button.innerHTML = '<i class="fas fa-check mr-2"></i> Activate Student';
                button.className = 'px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition';
                icon.className = 'bg-green-100 rounded-full p-2';
                icon.innerHTML = '<i class="fas fa-check-circle text-green-600 text-xl"></i>';
                info.className = 'bg-green-50 border border-green-200 rounded-lg p-4 mb-4';
                actions.innerHTML = `
                    <li>Set the student status to "Active"</li>
                    <li>Student will be able to log in again</li>
                    <li>Restore full access to the system</li>
                    <li>Can be archived again if needed</li>
                `;
            }
            
            document.getElementById('archiveStudentModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeArchiveStudentModal() {
            document.getElementById('archiveStudentModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        document.getElementById('archiveStudentForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const studentId = document.getElementById('archiveStudentId').value;
            
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Processing...';
            
            fetch(`/admin/management/students/${studentId}/archive`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    closeArchiveStudentModal();
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    showNotification(data.message || 'Failed to update student status', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred', 'error');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });

        // ===============================================
        // UTILITY FUNCTIONS
        // ===============================================
        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 px-6 py-4 rounded-lg shadow-lg z-50 ${
                type === 'success' ? 'bg-green-500' : 'bg-red-500'
            } text-white max-w-md`;
            notification.innerHTML = `
                <div class="flex items-center gap-3">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                    <span class="whitespace-pre-line">${message}</span>
                </div>
            `;

            document.body.appendChild(notification);
            setTimeout(() => notification.remove(), 5000);
        }

        function openAddStudentModal() {
            document.getElementById('addStudentModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeAddStudentModal() {
            document.getElementById('addStudentModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
            document.getElementById('addStudentForm').reset();
        }

        // ===============================================
        // SEARCH AND FILTER FUNCTIONALITY
        // ===============================================
        function updateVisibleCount() {
        const visibleRows = document.querySelectorAll('.student-row:not([style*="display: none"])');
        document.getElementById('visibleCount').textContent = visibleRows.length;
        }

        function applyFilters() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase().trim();
            const statusFilter = document.getElementById('statusFilter').value.toLowerCase();
            const sectionFilter = document.getElementById('sectionFilter').value.toLowerCase();
            const rows = document.querySelectorAll('.student-row');

            rows.forEach(row => {
                const name = (row.querySelector('.student-name')?.textContent || '').toLowerCase();
                const studentId = (row.querySelector('.student-id')?.textContent || '').toLowerCase();
                const email = (row.querySelector('.student-email')?.textContent || '').toLowerCase();
                const status = (row.querySelector('.student-status')?.textContent || '').toLowerCase().trim();
                const section = (row.getAttribute('data-section') || 'unassigned').toLowerCase();

                // Match logic
                const matchesSearch =
                    searchTerm === '' ||
                    name.includes(searchTerm) ||
                    studentId.includes(searchTerm) ||
                    email.includes(searchTerm);

                const matchesStatus =
                    statusFilter === 'all' || status === statusFilter;

                const matchesSection =
                    sectionFilter === 'all' || section === sectionFilter;

                // Show or hide the row
                if (matchesSearch && matchesStatus && matchesSection) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });

            updateVisibleCount();
        }

        // Add event listeners
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('searchInput')?.addEventListener('input', applyFilters);
            document.getElementById('statusFilter')?.addEventListener('change', applyFilters);
            document.getElementById('sectionFilter')?.addEventListener('change', applyFilters);
            updateVisibleCount(); // initialize on page load
        });

        // ===============================================
        // MODAL CLICK OUTSIDE TO CLOSE
        // ===============================================
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

        // ===============================================
        // EDIT STUDENT MODAL - AUTO-FILL FUNCTIONALITY
        // ===============================================
        function openEditStudentModal(studentId) {
            const modal = document.getElementById('editStudentModal');
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            
            fetch(`/admin/management/all-students/${studentId}/edit`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('editStudentId').value = data.student.id;
                    document.getElementById('editStudentLRN').value = data.student.student_id;
                    document.getElementById('editFirstname').value = data.student.firstname;
                    document.getElementById('editMiddlename').value = data.student.middlename || '';
                    document.getElementById('editLastname').value = data.student.lastname;
                    document.getElementById('editStudentGender').value = data.student.gender;
                    document.getElementById('editEmail').value = data.student.email;
                    document.getElementById('editStudentSection').value = data.student.section || '';
                    document.getElementById('editStatus').value = data.student.status;
                } else {
                    showNotification(data.message || 'Failed to load student data', 'error');
                    closeEditStudentModal();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred while loading student data', 'error');
                closeEditStudentModal();
            });
        }

        function closeEditStudentModal() {
            document.getElementById('editStudentModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
            document.getElementById('editStudentForm').reset();
        }

        document.getElementById('editStudentForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const studentId = document.getElementById('editStudentId').value;
            const formData = {
                student_id: document.getElementById('editStudentLRN').value,
                firstname: document.getElementById('editFirstname').value,
                middlename: document.getElementById('editMiddlename').value,
                lastname: document.getElementById('editLastname').value,
                password: document.getElementById('editPassword').value,
                email: document.getElementById('editEmail').value,
                gender: document.getElementById('editStudentGender').value,
                section: document.getElementById('editStudentSection').value || null,
                status: document.getElementById('editStatus').value
            };
            
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Updating...';
            
            fetch(`/admin/management/all-students/${studentId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    closeEditStudentModal();
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    if (data.errors) {
                        let errorMessage = 'Validation errors:\n';
                        Object.values(data.errors).forEach(error => {
                            errorMessage += error[0] + '\n';
                        });
                        showNotification(errorMessage, 'error');
                    } else {
                        showNotification(data.message || 'Failed to update student', 'error');
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred while updating student', 'error');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });
    </script>
@endpush
