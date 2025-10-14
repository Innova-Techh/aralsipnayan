@extends('admin.admin.layouts.app')

@section('title', 'Section Management')

@section('content')
    <div class="p-6 bg-gray-50 min-h-screen">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Section Management</h1>
                <p class="text-gray-600 text-sm mt-1">Manage sections and student assignments</p>
            </div>

            <div class="flex gap-3 mt-4 sm:mt-0">
                <button onclick="openAddSectionModal()"
                    class="flex items-center gap-2 px-4 py-2 bg-blue-900 text-white rounded-lg text-sm font-medium hover:bg-blue-800 transition">
                    <i class="fas fa-plus"></i> Add Section
                </button>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Total Sections -->
            <div class="bg-white rounded-lg border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Total Sections</p>
                        <h3 class="text-2xl font-bold text-gray-900">18</h3>
                        <p class="text-xs text-gray-500 mt-1">Across all grade levels</p>
                    </div>
                    <div class="bg-blue-50 rounded-lg p-3">
                        <i class="fas fa-book text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Total Students -->
            <div class="bg-white rounded-lg border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Total Students</p>
                        <h3 class="text-2xl font-bold text-gray-900">1,950</h3>
                        <p class="text-xs text-gray-500 mt-1">Average 30 per section</p>
                    </div>
                    <div class="bg-blue-50 rounded-lg p-3">
                        <i class="fas fa-users text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Active Sections -->
            <div class="bg-white rounded-lg border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Active Sections</p>
                        <h3 class="text-2xl font-bold text-gray-900">18</h3>
                        <p class="text-xs text-gray-500 mt-1">100% active</p>
                    </div>
                    <div class="bg-blue-50 rounded-lg p-3">
                        <i class="fas fa-check-circle text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Assigned Teachers -->
            <div class="bg-white rounded-lg border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Assigned Teachers</p>
                        <h3 class="text-2xl font-bold text-gray-900">18</h3>
                        <p class="text-xs text-gray-500 mt-1">One per section</p>
                    </div>
                    <div class="bg-blue-50 rounded-lg p-3">
                        <i class="fas fa-chalkboard-teacher text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- All Sections Container -->
        <div class="bg-white rounded-lg border border-gray-200">
            <div class="p-5 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">All Sections</h2>
                <p class="text-sm text-gray-600 mt-1">Overview of all sections and their details</p>
            </div>

            <div class="p-5">
                <!-- Search and Filter Bar -->
                <div class="flex flex-col sm:flex-row gap-3 mb-5">
                    <div class="flex-1">
                        <div class="relative">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <input type="text" placeholder="Search sections..."
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                    <div class="w-full sm:w-48">
                        <select
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option selected>Grade Level</option>
                            <option value="7">Grade 7</option>
                            <option value="8">Grade 8</option>
                            <option value="9">Grade 9</option>
                        </select>
                    </div>
                </div>

                <!-- Sections Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Grade 7 - Section A -->
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h3 class="font-semibold text-gray-900 text-sm">Grade 7 - Section A</h3>
                                <p class="text-xs text-gray-500 mt-0.5">ID: 1</p>
                            </div>
                            <span class="px-2 py-1 bg-green-50 text-green-700 text-xs font-medium rounded">Active</span>
                        </div>

                        <div class="space-y-3 mb-4">
                            <div class="flex items-center gap-3">
                                <div class="bg-blue-50 rounded p-2">
                                    <i class="fas fa-users text-blue-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Students</p>
                                    <p class="text-sm font-medium text-gray-900">32 enrolled</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <div class="bg-blue-50 rounded p-2">
                                    <i class="fas fa-user text-blue-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Teacher</p>
                                    <p class="text-sm font-medium text-gray-900">Ms. Maria Santos</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <a href="{{ route('admin.management.sections.students', 'Grade 7 - Section A') }}"
                                class="flex-1 flex items-center justify-center gap-2 px-3 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition">
                                <i class="fas fa-eye text-xs"></i> View
                            </a>
                            <button
                                class="flex items-center justify-center gap-2 px-3 py-2 border border-yellow-300 text-yellow-700 rounded-lg text-sm font-medium hover:bg-yellow-50 transition">
                                <i class="fas fa-archive text-xs"></i> Archive
                            </button>
                        </div>
                    </div>

                    <!-- Grade 7 - Section B -->
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h3 class="font-semibold text-gray-900 text-sm">Grade 7 - Section B</h3>
                                <p class="text-xs text-gray-500 mt-0.5">ID: 2</p>
                            </div>
                            <span class="px-2 py-1 bg-green-50 text-green-700 text-xs font-medium rounded">Active</span>
                        </div>

                        <div class="space-y-3 mb-4">
                            <div class="flex items-center gap-3">
                                <div class="bg-blue-50 rounded p-2">
                                    <i class="fas fa-users text-blue-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Students</p>
                                    <p class="text-sm font-medium text-gray-900">28 enrolled</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <div class="bg-blue-50 rounded p-2">
                                    <i class="fas fa-user text-blue-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Teacher</p>
                                    <p class="text-sm font-medium text-gray-900">Mr. John Cruz</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <a href="{{ route('admin.management.sections.students', 'Grade 7 - Section B') }}"
                                class="flex-1 flex items-center justify-center gap-2 px-3 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition">
                                <i class="fas fa-eye text-xs"></i> View
                            </a>
                            <button
                                class="flex items-center justify-center gap-2 px-3 py-2 border border-yellow-300 text-yellow-700 rounded-lg text-sm font-medium hover:bg-yellow-50 transition">
                                <i class="fas fa-archive text-xs"></i> Archive
                            </button>
                        </div>
                    </div>

                    <!-- Grade 8 - Section A -->
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h3 class="font-semibold text-gray-900 text-sm">Grade 8 - Section A</h3>
                                <p class="text-xs text-gray-500 mt-0.5">ID: 3</p>
                            </div>
                            <span class="px-2 py-1 bg-green-50 text-green-700 text-xs font-medium rounded">Active</span>
                        </div>

                        <div class="space-y-3 mb-4">
                            <div class="flex items-center gap-3">
                                <div class="bg-blue-50 rounded p-2">
                                    <i class="fas fa-users text-blue-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Students</p>
                                    <p class="text-sm font-medium text-gray-900">30 enrolled</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <div class="bg-blue-50 rounded p-2">
                                    <i class="fas fa-user text-blue-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Teacher</p>
                                    <p class="text-sm font-medium text-gray-900">Ms. Ana Reyes</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <a href="{{ route('admin.management.sections.students', 'Grade 8 - Section A') }}"
                                class="flex-1 flex items-center justify-center gap-2 px-3 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition">
                                <i class="fas fa-eye text-xs"></i> View
                            </a>
                            <button
                                class="flex items-center justify-center gap-2 px-3 py-2 border border-yellow-300 text-yellow-700 rounded-lg text-sm font-medium hover:bg-yellow-50 transition">
                                <i class="fas fa-archive text-xs"></i> Archive
                            </button>
                        </div>
                    </div>

                    <!-- Grade 8 - Section B -->
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h3 class="font-semibold text-gray-900 text-sm">Grade 8 - Section B</h3>
                                <p class="text-xs text-gray-500 mt-0.5">ID: 4</p>
                            </div>
                            <span class="px-2 py-1 bg-green-50 text-green-700 text-xs font-medium rounded">Active</span>
                        </div>

                        <div class="space-y-3 mb-4">
                            <div class="flex items-center gap-3">
                                <div class="bg-blue-50 rounded p-2">
                                    <i class="fas fa-users text-blue-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Students</p>
                                    <p class="text-sm font-medium text-gray-900">29 enrolled</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <div class="bg-blue-50 rounded p-2">
                                    <i class="fas fa-user text-blue-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Teacher</p>
                                    <p class="text-sm font-medium text-gray-900">Mr. Carlos Lopez</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <a href="{{ route('admin.management.sections.students', 'Grade 8 - Section B') }}"
                                class="flex-1 flex items-center justify-center gap-2 px-3 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition">
                                <i class="fas fa-eye text-xs"></i> View
                            </a>
                            <button
                                class="flex items-center justify-center gap-2 px-3 py-2 border border-yellow-300 text-yellow-700 rounded-lg text-sm font-medium hover:bg-yellow-50 transition">
                                <i class="fas fa-archive text-xs"></i> Archive
                            </button>
                        </div>
                    </div>

                    <!-- Grade 9 - Section A -->
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h3 class="font-semibold text-gray-900 text-sm">Grade 9 - Section A</h3>
                                <p class="text-xs text-gray-500 mt-0.5">ID: 5</p>
                            </div>
                            <span class="px-2 py-1 bg-green-50 text-green-700 text-xs font-medium rounded">Active</span>
                        </div>

                        <div class="space-y-3 mb-4">
                            <div class="flex items-center gap-3">
                                <div class="bg-blue-50 rounded p-2">
                                    <i class="fas fa-users text-blue-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Students</p>
                                    <p class="text-sm font-medium text-gray-900">27 enrolled</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <div class="bg-blue-50 rounded p-2">
                                    <i class="fas fa-user text-blue-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Teacher</p>
                                    <p class="text-sm font-medium text-gray-900">Ms. Linda Garcia</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <a href="{{ route('admin.management.sections.students', 'Grade 9 - Section A') }}"
                                class="flex-1 flex items-center justify-center gap-2 px-3 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition">
                                <i class="fas fa-eye text-xs"></i> View
                            </a>
                            <button
                                class="flex items-center justify-center gap-2 px-3 py-2 border border-yellow-300 text-yellow-700 rounded-lg text-sm font-medium hover:bg-yellow-50 transition">
                                <i class="fas fa-archive text-xs"></i> Archive
                            </button>
                        </div>
                    </div>

                    <!-- Grade 9 - Section B -->
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h3 class="font-semibold text-gray-900 text-sm">Grade 9 - Section B</h3>
                                <p class="text-xs text-gray-500 mt-0.5">ID: 6</p>
                            </div>
                            <span class="px-2 py-1 bg-green-50 text-green-700 text-xs font-medium rounded">Active</span>
                        </div>

                        <div class="space-y-3 mb-4">
                            <div class="flex items-center gap-3">
                                <div class="bg-blue-50 rounded p-2">
                                    <i class="fas fa-users text-blue-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Students</p>
                                    <p class="text-sm font-medium text-gray-900">25 enrolled</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <div class="bg-blue-50 rounded p-2">
                                    <i class="fas fa-user text-blue-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Teacher</p>
                                    <p class="text-sm font-medium text-gray-900">Mr. Robert Santos</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <a href="{{ route('admin.management.sections.students', 'Grade 9 - Section B') }}"
                                class="flex-1 flex items-center justify-center gap-2 px-3 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition">
                                <i class="fas fa-eye text-xs"></i> View
                            </a>
                            <button
                                class="flex items-center justify-center gap-2 px-3 py-2 border border-yellow-300 text-yellow-700 rounded-lg text-sm font-medium hover:bg-yellow-50 transition">
                                <i class="fas fa-archive text-xs"></i> Archive
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Section Modal -->
        @include('admin.components.add-section-modal')
    </div>
@endsection

@push('scripts')
    <script>
        function openAddSectionModal() {
            document.getElementById('addSectionModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeAddSectionModal() {
            document.getElementById('addSectionModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
            // Reset form
            document.getElementById('addSectionForm').reset();
            // Clear validation errors
            document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
            document.querySelectorAll('.border-red-500').forEach(el => {
                el.classList.remove('border-red-500');
                el.classList.add('border-gray-300');
            });
        }

        // Close modal when clicking outside
        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('addSectionModal');
            if (modal) {
                modal.addEventListener('click', function (e) {
                    if (e.target === this) {
                        closeAddSectionModal();
                    }
                });
            }
        });

        // Form validation
        function validateSectionForm() {
            let isValid = true;
            const sectionName = document.getElementById('section_name');
            const gradeLevel = document.getElementById('grade_level');
            const enrolledStudents = document.getElementById('enrolled_students');
            const assignedTeacher = document.getElementById('assigned_teacher');

            // Clear previous errors
            document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
            document.querySelectorAll('.border-red-500').forEach(el => {
                el.classList.remove('border-red-500');
                el.classList.add('border-gray-300');
            });

            // Validate section name
            if (!sectionName.value.trim()) {
                showError('section_name', 'Section name is required');
                isValid = false;
            }

            // Validate grade level
            if (!gradeLevel.value) {
                showError('grade_level', 'Grade level is required');
                isValid = false;
            }

            // Validate enrolled students
            if (!enrolledStudents.value) {
                showError('enrolled_students', 'Number of enrolled students is required');
                isValid = false;
            } else if (enrolledStudents.value < 0) {
                showError('enrolled_students', 'Number of students cannot be negative');
                isValid = false;
            } else if (enrolledStudents.value > 100) {
                showError('enrolled_students', 'Number of students cannot exceed 100');
                isValid = false;
            }

            // Validate assigned teacher
            if (!assignedTeacher.value) {
                showError('assigned_teacher', 'Assigned teacher is required');
                isValid = false;
            }

            return isValid;
        }

        function showError(fieldId, message) {
            const field = document.getElementById(fieldId);
            const errorElement = document.getElementById(fieldId + '_error');

            if (field) {
                field.classList.remove('border-gray-300');
                field.classList.add('border-red-500');
            }

            if (errorElement) {
                errorElement.textContent = message;
            }
        }
    </script>
@endpush