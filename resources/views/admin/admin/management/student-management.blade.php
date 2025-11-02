@extends('admin.admin.layouts.app')


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

            <!-- Assigned Teacher -->
            <div class="bg-white rounded-lg border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Teacher</p>
                        <h3 class="text-base font-bold text-gray-900">{{ $section->teacher }}</h3>
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
                            <option value="archive">Archived</option>
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
                                <tr class="hover:bg-gray-50 transition student-row">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 student-id">
                                            {{ $student->studentProfile->student_id ?? 'N/A' }}
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
                                        @php
                                            $statusColors = [
                                                'active' => 'bg-green-100 text-green-800',
                                                'inactive' => 'bg-yellow-100 text-yellow-800',
                                                'archive' => 'bg-gray-100 text-gray-800',
                                            ];
                                            $statusColor = $statusColors[$student->status] ?? 'bg-gray-100 text-gray-800';
                                        @endphp
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColor }}">
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
// EDIT STUDENT MODAL - AUTO-FILL FUNCTIONALITY
// ===============================================

/**
 * Open edit modal and fetch student data to auto-fill the form
 */
function openEditStudentModal(studentId) {
    // Show loading state
    const modal = document.getElementById('editStudentModal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // Fetch student data
    fetch(`/admin/management/students/${studentId}/edit`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Populate form fields with student data
            document.getElementById('editStudentId').value = data.student.id;
            document.getElementById('editStudentLRN').value = data.student.student_id;
            document.getElementById('editFirstname').value = data.student.firstname;
            document.getElementById('editMiddlename').value = data.student.middlename || '';
            document.getElementById('editLastname').value = data.student.lastname;
            document.getElementById('editStudentGender').value = data.student.gender;
            document.getElementById('editEmail').value = data.student.email;
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

/**
 * Close edit modal
 */
function closeEditStudentModal() {
    document.getElementById('editStudentModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    // Reset form
    document.getElementById('editStudentForm').reset();
}

/**
 * Handle edit student form submission
 */
document.getElementById('editStudentForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const studentId = document.getElementById('editStudentId').value;
    const formData = {
        student_id: document.getElementById('editStudentLRN').value,
        firstname: document.getElementById('editFirstname').value,
        middlename: document.getElementById('editMiddlename').value,
        lastname: document.getElementById('editLastname').value,
        email: document.getElementById('editEmail').value,
        password:document.getElementById('editPassword').value,
        gender: document.getElementById('editStudentGender').value,
        status: document.getElementById('editStatus').value
    };
    
    // Disable submit button
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Updating...';
    
    fetch(`/admin/management/students/${studentId}`, {
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
                // Display validation errors
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
        // Re-enable submit button
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
});


// ===============================================
// ADD STUDENT FORM SUBMISSION
// ===============================================

document.getElementById('addStudentForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const sectionId = "{{ $section->id }}"; // This will be replaced by blade
    const formData = {
            firstname: document.getElementById('addStudentFirstName').value,
            middlename: document.getElementById('addStudentMiddleName').value,
            lastname: document.getElementById('addStudentLastName').value,
            email: document.getElementById('addStudentEmail').value,
            gender: document.getElementById('addStudentGender').value,
    };

    console.log(formData); // check what’s captured
    // Disable submit button
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Adding...';
    
    fetch(`/admin/management/sections/${sectionId}/students`, {
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
                // Display validation errors
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
        // Re-enable submit button
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
});


// ===============================================
// ARCHIVE/ACTIVATE STUDENT
// ===============================================

/**
 * Open archive modal with dynamic content based on student status
 */
function openArchiveStudentModal(studentId, studentName, currentStatus) {
    document.getElementById('archiveStudentId').value = studentId;
    document.getElementById('archiveStudentCurrentStatus').value = currentStatus;
    
    // Update modal content based on current status
    const isActive = currentStatus === 'active';
    const title = document.getElementById('archiveStudentTitle');
    const message = document.getElementById('archiveStudentMessage');
    const button = document.getElementById('archiveStudentButton');
    const icon = document.getElementById('archiveStudentIcon');
    const info = document.getElementById('archiveStudentInfo');
    const actions = document.getElementById('archiveStudentActions');
    
    if (isActive) {
        // Archive/Inactivate mode
        title.textContent = 'Archive Student';
        message.innerHTML = `Are you sure you want to archive <span class="font-semibold text-gray-900">${studentName}</span>?`;
        button.innerHTML = '<i class="fas fa-archive mr-2"></i> Archive Student';
        button.className = 'px-4 py-2 bg-yellow-600 text-white rounded-lg text-sm font-medium hover:bg-yellow-700 transition';
        icon.className = 'bg-yellow-100 rounded-full p-2';
        icon.innerHTML = '<i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>';
        info.className = 'bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4';
        actions.innerHTML = `
            <li>Set ${studentName} status to "Inactive"</li>
            <li>${studentName} will NOT be able to log in</li>
            <li>${studentName} will be REMOVED from their section</li>
            <li>All ${studentName}'s  data and records are preserved</li>
            <li>Can be reversed by reactivating ${studentName}</li>
        `;
    } else {
        // Activate mode
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
            <li>Can be inactivated again if needed</li>
        `;
    }
    
    document.getElementById('archiveStudentModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

/**
 * Close archive modal
 */
function closeArchiveStudentModal() {
    document.getElementById('archiveStudentModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

/**
 * Handle archive student form submission
 */
document.getElementById('archiveStudentForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const studentId = document.getElementById('archiveStudentId').value;
    
    // Disable submit button
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
        // Re-enable submit button
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
});


// ===============================================
// UTILITY FUNCTIONS
// ===============================================

/**
 * Show notification message
 */
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

    setTimeout(() => {
        notification.remove();
    }, 5000);
}

/**
 * Open add student modal
 */
function openAddStudentModal() {
    document.getElementById('addStudentModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

/**
 * Close add student modal
 */
function closeAddStudentModal() {
    document.getElementById('addStudentModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    document.getElementById('addStudentForm').reset();
}

/**
 * Search functionality
 */
document.getElementById('searchInput')?.addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('.student-row');

    rows.forEach(row => {
        const name = row.querySelector('.student-name')?.textContent.toLowerCase() || '';
        const studentId = row.querySelector('.student-id')?.textContent.toLowerCase() || '';
        const email = row.querySelector('.student-email')?.textContent.toLowerCase() || '';

        if (name.includes(searchTerm) || studentId.includes(searchTerm) || email.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

/**
 * Status filter functionality
 */
document.getElementById('statusFilter')?.addEventListener('change', function(e) {
    const filterValue = e.target.value;
    const rows = document.querySelectorAll('.student-row');

    rows.forEach(row => {
        const statusBadge = row.querySelector('.status-badge');
        const statusText = statusBadge?.textContent.toLowerCase().trim() || '';

        if (filterValue === 'all') {
            row.style.display = '';
        } else if (filterValue === 'active' && statusText === 'active') {
            row.style.display = '';
        } else if (filterValue === 'inactive' && statusText === 'inactive') {
            row.style.display = '';
        } else if (filterValue === 'archive' && statusText === 'archived') {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

/**
 * Close modals when clicking outside
 */
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