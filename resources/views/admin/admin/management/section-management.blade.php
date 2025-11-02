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
                        <h3 class="text-2xl font-bold text-gray-900">{{ $totalSections }}</h3>
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
                        <h3 class="text-2xl font-bold text-gray-900">{{ number_format($totalStudents) }}
                        <span class="text-xs text-gray-500 mt-1 pl-2">Average: {{ $totalSections > 0 ? round($totalStudents / $totalSections) : 0 }} per section </span></h3>
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
                        <h3 class="text-2xl font-bold text-gray-900">{{ $activeSections }}</h3>
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
                        <h3 class="text-2xl font-bold text-gray-900">{{ $assignedTeachers }}</h3>
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
                            <input type="text" id="searchInput" placeholder="Search sections..."
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                </div>

                <!-- Sections Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse($sectionsWithTeachers as $index => $section)
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition section-row">
                        <div class="flex justify-between items-start mb-3">
                            <div class = "section-name">
                                <h3 class="font-semibold text-gray-900 text-sm">{{ $section['section'] }}</h3>
                                <p class="text-xs text-gray-500 mt-0.5">Grade {{ $section['grade_level'] }}</p>
                            </div>
                            <span
                                @class([
                                    'px-2 py-1 text-xs font-medium rounded',
                                    'bg-green-50 text-green-700' => $section['is_active'],
                                    'bg-red-50 text-red-700'     => ! $section['is_active'],
                                ])
                                >
                                {{ $section['is_active'] ? 'Active' : 'Inactive' }}
                            </span>
                        </div>

                        <div class="space-y-3 mb-4">
                            <div class="flex items-center gap-3">
                                <div class="bg-blue-50 rounded p-2">
                                    <i class="fas fa-users text-blue-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Students</p>
                                    <p class="text-sm font-medium text-gray-900">{{ $section['student_count'] }} enrolled</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <div class="bg-blue-50 rounded p-2">
                                    <i class="fas fa-user text-blue-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Teacher</p>
                                    <p class="text-sm font-medium text-gray-900">{{ $section['teacher'] }}</p>
                                </div>
                            </div>
                        </div>


                        <div class="flex gap-2">
                            <a href="{{ route('admin.management.sections.students', $section['section']) }}"
                                class="flex-1 flex items-center justify-center gap-2 px-3 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition">
                                <i class="fas fa-eye text-xs"></i> View
                            </a>
                            <button onclick="toggleSectionStatus('{{ $section['section'] }}', {{ $section['is_active'] ? 'true' : 'false' }})"
                                class="flex items-center justify-center gap-2 px-3 py-2 border 
                                    {{ $section['is_active'] ? 'border-yellow-300 text-yellow-700 hover:bg-yellow-50' : 'border-green-300 text-green-700 hover:bg-green-50' }}
                                    rounded-lg text-sm font-medium transition">
                                <i class="fas {{ $section['is_active'] ? 'fa-archive' : 'fa-undo' }} text-xs"></i>
                                {{ $section['is_active'] ? 'Archive' : 'Activate' }}
                            </button>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-full text-center py-12">
                        <div class="text-gray-400 mb-4">
                            <i class="fas fa-book text-4xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No sections found</h3>
                        <p class="text-gray-500">Start by adding your first section.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Add Section Modal -->
        @include('admin.components.add-section-modal')
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
         document.getElementById('searchInput')?.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('.section-row');

            rows.forEach(row => {
                const name = row.querySelector('.section-name')?.textContent.toLowerCase() || '';

                if (name.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });


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
            } else if (sectionName.value.trim().length > 50) {
                showError('section_name', 'Section name cannot exceed 50 characters');
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

        // Form submission handler for add section
        document.addEventListener('DOMContentLoaded', function() {
            const addSectionForm = document.getElementById('addSectionForm');
            if (addSectionForm) {
                addSectionForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    if (!validateSectionForm()) {
                        return;
                    }
                    
                    const formData = new FormData(this);
                    
                    fetch('{{ route("admin.management.sections.store") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            closeAddSectionModal();
                            location.reload();
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while creating the section.');
                    });
                });
            }
        });

         // Archive or Activate Section
        function toggleSectionStatus(sectionName, isActive) {
            const action = isActive ? 'archive' : 'activate';
            const confirmMessage = isActive
                ? `Are you sure you want to archive the section "${sectionName}"?`
                : `Do you want to reactivate the section "${sectionName}"?`;

            if (confirm(confirmMessage)) {
                fetch(`/admin/management/sections/${encodeURIComponent(sectionName)}/${action}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while processing your request.');
                });
            }
        }
    </script>
@endpush