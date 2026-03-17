@extends('admin.admin.layouts.app')

@section('title', 'AralSipnayan')

@section('content')
    <div class="p-6 bg-gray-50 min-h-screen">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Teacher Management</h1>
                <p class="text-gray-600 text-sm mt-1">Manage teacher accounts and assignments</p>
            </div>

            <div class="flex gap-3 mt-4 sm:mt-0">
                <button onclick="openAddTeacherModal()"
                    class="flex items-center gap-2 px-4 py-2 bg-blue-900 text-white rounded-lg text-sm font-medium hover:bg-blue-800 transition">
                    <i class="fas fa-plus"></i> Add Teacher
                </button>
            </div>
        </div>
        
        <!-- Search and Filter -->
        <div class="bg-white rounded-lg border border-gray-200 p-4 mb-6">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1 relative">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input type="text" id="searchTeachers" placeholder="Search by name, username, or email..."
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <select id="filterStatus"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
        </div>

        <!-- Teachers Table -->
        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Teacher
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Username
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Email
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Sections
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Students
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="teachersTableBody">
                        @forelse($teachers as $teacher)
                            <tr class="hover:bg-gray-50 transition teacher-row" data-status="{{ $teacher->status }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $teacherFullName = trim($teacher->firstname . ' ' . $teacher->lastname);
                                        $teacherPhoto = $teacher->profile_url ?? null;
                                        $teacherPhotoSrc = $teacherPhoto
                                            ? (\Illuminate\Support\Str::startsWith($teacherPhoto, ['http://', 'https://'])
                                                ? $teacherPhoto
                                                : asset('storage/' . ltrim($teacherPhoto, '/')))
                                            : 'https://ui-avatars.com/api/?name=' . urlencode($teacherFullName) . '&background=3B82F6&color=fff';
                                    @endphp
                                    <div class="flex items-center">
                                        <img src="{{ $teacherPhotoSrc }}" alt="{{ $teacherFullName }}"
                                            class="w-9 h-9 rounded-full object-cover border border-gray-200 mr-3">

                                        <div>
                                            <div class="text-sm font-medium text-gray-900 teacher-name">
                                                {{ $teacher->firstname }} {{ $teacher->lastname }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $teacher->school_name }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 teacher-username">{{ $teacher->username }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-600 teacher-email">{{ $teacher->email }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">
                                        @if ($teacher->sections && $teacher->sections !== 'No sections assigned')
                                            @php
                                                $sectionArray = explode(', ', $teacher->sections);
                                            @endphp
                                            <div class="flex flex-wrap gap-1">
                                                @foreach ($sectionArray as $section)
                                                    <span
                                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                                        {{ $section }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-400">No sections</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <i class="fas fa-users mr-1 text-xs"></i>
                                            {{ $teacher->student_count }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($teacher->status === 'active')
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-circle mr-1" style="font-size: 6px;"></i>
                                            Active
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            <i class="fas fa-circle mr-1" style="font-size: 6px;"></i>
                                            Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center gap-2">
                                        <button onclick="editTeacher({{ $teacher->id }})"
                                            class="text-blue-600 hover:text-blue-900 transition" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="openArchiveTeacherModal({{ $teacher->id }}, '{{ $teacher->firstname }} {{ $teacher->lastname }}', '{{ $teacher->status }}')"
                                            class="text-yellow-600 hover:text-yellow-900 transition" title="{{ $teacher->status === 'active' ? 'Archive' : 'Activate' }}">
                                            <i class="fas fa-{{ $teacher->status === 'active' ? 'archive' : 'check' }}"></i>
                                        </button>
                                       
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="text-gray-400 mb-4">
                                        <i class="fas fa-chalkboard-teacher text-4xl"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No teachers found</h3>
                                    <p class="text-gray-500">Add your first teacher to get started.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Teacher Modal -->
        @include('admin.components.add-teacher-modal')

        <!-- Edit Teacher Modal -->
        @include('admin.components.edit-teacher-modal')

        <!-- Archive Teacher Modal -->
        @include('admin.components.archive-teacher-modal')


    </div>
@endsection

@push('scripts')
    <script>
        let teacherToDelete = null;
        let availableSections = [];

        // Load available sections on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadAvailableSections();
            
            // Comprehensive modal check
            const editModal = document.getElementById('editTeacherModal');
            const editForm = document.getElementById('editTeacherForm');
            const editIdField = document.getElementById('editTeacherId');

        });

        // Load available sections
        function loadAvailableSections() {
            apiFetch('/admin/management/teachers/sections')
                .then(data => {
                    availableSections = data.sections;
                })
                .catch(error => console.error('Error loading sections:', error));
        }

        // Open Add Teacher Modal
        function openAddTeacherModal() {
            loadSections('addTeacherSections');
            document.getElementById('addTeacherModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        // Close Add Teacher Modal
        function closeAddTeacherModal() {
            document.getElementById('addTeacherModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
            document.getElementById('addTeacherForm').reset();
            clearErrors();
        }

        // Improved Load sections
        function loadSections(containerId) {
            const container = document.getElementById(containerId);
            const loadingElement = document.getElementById('editSectionsLoading');
            const emptyElement = document.getElementById('editSectionsEmpty');
            
            if (!container) {
                console.error('Container not found:', containerId);
                return Promise.reject('Container not found');
            }

            // Show loading state
            if (loadingElement) loadingElement.classList.remove('hidden');
            if (emptyElement) emptyElement.classList.add('hidden');
            
            return apiFetch('/admin/management/teachers/sections')
                .then(data => {
                    // Hide loading
                    if (loadingElement) loadingElement.classList.add('hidden');
                    
                    if (container && data.sections && data.sections.length > 0) {
                        container.innerHTML = '';
                        
                        data.sections.forEach(section => {
                            const wrapper = document.createElement('div');
                            wrapper.className = 'flex items-center';
                            
                            const label = document.createElement('label');
                            label.className = 'flex items-center cursor-pointer hover:bg-gray-100 p-2 rounded transition';
                            label.innerHTML = `
                                <input type="checkbox" 
                                    name="sections[]" 
                                    value="${escapeHtml(section)}" 
                                    class="section-checkbox w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 mr-2"
                                    onchange="updateSelectedSectionsDisplay()">
                                <span class="text-sm text-gray-700">${escapeHtml(section)}</span>
                            `;
                            
                            wrapper.appendChild(label);
                            container.appendChild(wrapper);
                        });
                        
                        if (emptyElement) emptyElement.classList.add('hidden');
                    } else {
                        // Show empty state
                        container.innerHTML = '';
                        if (emptyElement) emptyElement.classList.remove('hidden');
                    }
                    
                    return data;
                })
                .catch(error => {
                    console.error('Error loading sections:', error);
                    if (loadingElement) loadingElement.classList.add('hidden');
                    if (container) {
                        container.innerHTML = '<div class="col-span-full text-center py-4 text-red-500 text-sm"><i class="fas fa-exclamation-circle mr-2"></i>Failed to load sections</div>';
                    }
                    throw error;
                });
        }

        // Update selected sections display
        function updateSelectedSectionsDisplay() {
            const checkboxes = document.querySelectorAll('#editTeacherSections .section-checkbox');
            const selectedSections = Array.from(checkboxes)
                .filter(cb => cb.checked)
                .map(cb => cb.value);
            
            const displayContainer = document.getElementById('editSelectedSectionsDisplay');
            const listContainer = document.getElementById('editSelectedSectionsList');
            
            if (!displayContainer || !listContainer) return;
            
            if (selectedSections.length > 0) {
                displayContainer.classList.remove('hidden');
                listContainer.innerHTML = selectedSections.map(section => 
                    `<span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800">
                        ${escapeHtml(section)}
                    </span>`
                ).join('');
            } else {
                displayContainer.classList.add('hidden');
                listContainer.innerHTML = '';
            }
        }

       // Improved Edit Teacher function
        function editTeacher(teacherId) {
            // Clear previous state
            clearEditModalState();
            
            // Load sections first
            loadSections('editTeacherSections')
                .then(() => {
                    // Then fetch teacher data
                    return apiFetch(`/admin/management/teachers/${teacherId}`);
                })
                .then(data => {
                    if (data.teacher) {
                        const teacher = data.teacher;
                        
                        // Populate form fields
                        populateEditForm(teacher);
                        
                        // Set sections after a short delay to ensure checkboxes are rendered
                        setTimeout(() => {
                            setTeacherSections(teacher.sections || []);
                        }, 150);

                        // Show modal
                        const modal = document.getElementById('editTeacherModal');
                        if (modal) {
                            modal.classList.remove('hidden');
                            document.body.style.overflow = 'hidden';
                        }
                    } else {
                        throw new Error('Teacher data not found');
                    }
                })
                .catch(error => {
                    console.error('Error loading teacher data:', error);
                    showNotification('Failed to load teacher data: ' + error.message, 'error');
                });
        }

        // Populate edit form with teacher data
        function populateEditForm(teacher) {
            const fields = {
                'editTeacherId': teacher.id,
                'editFirstname': teacher.firstname || '',
                'editLastname': teacher.lastname || '',
                'editUsername': teacher.username || '',
                'editEmail': teacher.email || '',
                'editSchoolName': teacher.school_name || '',
                'editStatus': teacher.status || 'active'
            };
            
            Object.keys(fields).forEach(fieldId => {
                const element = document.getElementById(fieldId);
                if (element) {
                    element.value = fields[fieldId];
                }
            });
            
            // Clear password field
            const passwordField = document.getElementById('editPassword');
            if (passwordField) passwordField.value = '';
        }

        // Set teacher sections
        function setTeacherSections(teacherSections) {
            // Ensure teacherSections is an array
            const sectionsArray = Array.isArray(teacherSections) ? teacherSections : [];
            
            // Clear all checkboxes first
            const checkboxes = document.querySelectorAll('#editTeacherSections .section-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            
            // Check the appropriate boxes
            if (sectionsArray.length > 0) {
                sectionsArray.forEach(section => {
                    const checkbox = Array.from(checkboxes).find(cb => cb.value === section);
                    if (checkbox) {
                        checkbox.checked = true;
                    }
                });
            }
            
            // Update display
            updateSelectedSectionsDisplay();
        }

        // Clear edit modal state
        function clearEditModalState() {
            // Reset form
            const form = document.getElementById('editTeacherForm');
            if (form) form.reset();
            
            // Clear sections
            const sectionsContainer = document.getElementById('editTeacherSections');
            if (sectionsContainer) {
                sectionsContainer.innerHTML = '<div class="col-span-full text-center py-4 text-gray-500 text-sm"><i class="fas fa-spinner fa-spin mr-2"></i>Loading sections...</div>';
            }
            
            // Hide selected sections display
            const displayContainer = document.getElementById('editSelectedSectionsDisplay');
            if (displayContainer) displayContainer.classList.add('hidden');
            
            // Clear errors
            clearErrors();
        }

        // Close Edit Teacher Modal
        function closeEditTeacherModal() {
            document.getElementById('editTeacherModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
            document.getElementById('editTeacherForm').reset();
            clearErrors();
        }

        // Open Archive Teacher Modal
        function openArchiveTeacherModal(teacherId, teacherName, currentStatus) {
            document.getElementById('archiveTeacherId').value = teacherId;
            document.getElementById('archiveTeacherCurrentStatus').value = currentStatus;
            document.getElementById('archiveTeacherName').textContent = teacherName;
            
            // Update modal content based on current status
            const isActive = currentStatus === 'active';
            const title = document.getElementById('archiveTeacherTitle');
            const message = document.getElementById('archiveTeacherMessage');
            const button = document.getElementById('archiveTeacherButton');
            const icon = document.getElementById('archiveTeacherIcon');
            const info = document.getElementById('archiveTeacherInfo');
            const actions = document.getElementById('archiveTeacherActions');
            
            if (isActive) {
                // Archive mode
                title.textContent = 'Inactivate Teacher';
                message.innerHTML = `Are you sure you want to Inactivate <span class="font-semibold text-gray-900">${teacherName}</span>?`;
                button.innerHTML = '<i class="fas fa-archive mr-2"></i> Inactivate Teacher';
                button.className = 'px-4 py-2 bg-yellow-600 text-white rounded-lg text-sm font-medium hover:bg-yellow-700 transition';
                icon.className = 'bg-yellow-100 rounded-full p-2';
                icon.innerHTML = '<i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>';
                info.className = 'bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4';
                actions.innerHTML = `
                    <li>Set the teacher status to "Inactive"</li>
                    <li>Remove them from active teacher lists</li>
                    <li>Preserve all their data and records</li>
                    <li>Can be reversed by reactivating the teacher</li>
                `;
            } else {
                // Activate mode
                title.textContent = 'Activate Teacher';
                message.innerHTML = `Are you sure you want to activate <span class="font-semibold text-gray-900">${teacherName}</span>?`;
                button.innerHTML = '<i class="fas fa-check mr-2"></i> Activate Teacher';
                button.className = 'px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition';
                icon.className = 'bg-green-100 rounded-full p-2';
                icon.innerHTML = '<i class="fas fa-check-circle text-green-600 text-xl"></i>';
                info.className = 'bg-green-50 border border-green-200 rounded-lg p-4 mb-4';
                actions.innerHTML = `
                    <li>Set the teacher status to "Active"</li>
                    <li>Add them back to active teacher lists</li>
                    <li>Restore full access to the system</li>
                    <li>Can be archived again if needed</li>
                `;
            }
            
            document.getElementById('archiveTeacherModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        // Close Archive Teacher Modal
        function closeArchiveTeacherModal() {
            document.getElementById('archiveTeacherModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

       

        // Add Teacher Form Submit
        document.getElementById('addTeacherForm')?.addEventListener('submit', function(e) {
            e.preventDefault();

            if (!validateTeacherForm()) return;

            const formData = new FormData(this);

            apiFetch('/admin/management/teachers', {
                    method: 'POST',
                    body: formData
                })
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                        closeAddTeacherModal();
                        setTimeout(() => window.location.reload(), 1000);
                    } else {
                        showNotification(data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('An error occurred', 'error');
                });
        });

        // Improved Edit Teacher Form Submit
        document.addEventListener('DOMContentLoaded', function() {
            const editForm = document.getElementById('editTeacherForm');
            if (editForm) {
                editForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const teacherId = document.getElementById('editTeacherId')?.value;
                    if (!teacherId) {
                        showNotification('Teacher ID not found', 'error');
                        return;
                    }

                    // Get all checked sections
                    const checkedSections = Array.from(
                        document.querySelectorAll('#editTeacherSections .section-checkbox:checked')
                    ).map(cb => cb.value);

                    // Create FormData
                    const formData = new FormData(this);
                    formData.append('_method', 'PUT');
                    
                    // IMPORTANT: Ensure sections array is always sent
                    // Remove any existing sections[] entries first
                    formData.delete('sections[]');
                    
                    // Add all checked sections
                    if (checkedSections.length > 0) {
                        checkedSections.forEach(section => {
                            formData.append('sections[]', section);
                        });
                    } else {
                        // Send empty array explicitly
                        formData.append('sections', JSON.stringify([]));
                    }

                    // Disable submit button
                    const submitBtn = this.querySelector('button[type="submit"]');
                    const originalText = submitBtn?.innerHTML || '';
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Updating...';
                    }

                    apiFetch(`/admin/management/teachers/${teacherId}`, {
                        method: 'POST',
                        body: formData
                    })
                    .then(data => {
                        if (data.success) {
                            showNotification(data.message, 'success');
                            closeEditTeacherModal();
                            setTimeout(() => window.location.reload(), 1000);
                        } else {
                            throw new Error(data.message || 'Update failed');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showNotification(error.message || 'An error occurred', 'error');
                    })
                    .finally(() => {
                        // Re-enable submit button
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalText;
                        }
                    });
                });
            }
        });

        // Archive Teacher Form Submit
        document.getElementById('archiveTeacherForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const teacherId = document.getElementById('archiveTeacherId').value;
            
            apiFetch(`/admin/management/teachers/${teacherId}/archive`, { method: 'POST' })
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    closeArchiveTeacherModal();
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred', 'error');
            });
        });

        // Search functionality
        document.getElementById('searchTeachers')?.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('.teacher-row');

            rows.forEach(row => {
                const name = row.querySelector('.teacher-name')?.textContent.toLowerCase() || '';
                const username = row.querySelector('.teacher-username')?.textContent.toLowerCase() || '';
                const email = row.querySelector('.teacher-email')?.textContent.toLowerCase() || '';

                if (name.includes(searchTerm) || username.includes(searchTerm) || email.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // Filter by status
        document.getElementById('filterStatus')?.addEventListener('change', function(e) {
            const status = e.target.value;
            const rows = document.querySelectorAll('.teacher-row');

            rows.forEach(row => {
                if (status === '' || row.dataset.status === status) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // Form validation
        function validateTeacherForm() {
            let isValid = true;
            const firstname = document.getElementById('teacher_firstname');
            const lastname = document.getElementById('teacher_lastname');
            const username = document.getElementById('teacher_username');
            const email = document.getElementById('teacher_email');
            const password = document.getElementById('teacher_password');
            const passwordConfirm = document.getElementById('teacher_password_confirmation');

            clearErrors();

            if (!firstname?.value.trim()) {
                showError('teacher_firstname', 'First name is required');
                isValid = false;
            }

            if (!lastname?.value.trim()) {
                showError('teacher_lastname', 'Last name is required');
                isValid = false;
            }

            if (!username?.value.trim()) {
                showError('teacher_username', 'Username is required');
                isValid = false;
            }

            if (!email?.value.trim()) {
                showError('teacher_email', 'Email is required');
                isValid = false;
            } else if (!isValidEmail(email.value)) {
                showError('teacher_email', 'Please enter a valid email address');
                isValid = false;
            }

            if (password && password.value) {
                if (password.value.length < 8) {
                    showError('teacher_password', 'Password must be at least 8 characters');
                    isValid = false;
                }

                if (passwordConfirm && password.value !== passwordConfirm.value) {
                    showError('teacher_password_confirmation', 'Passwords do not match');
                    isValid = false;
                }
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

        function clearErrors() {
            document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
            document.querySelectorAll('.border-red-500').forEach(el => {
                el.classList.remove('border-red-500');
                el.classList.add('border-gray-300');
            });
        }

        function isValidEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }

        // Notification system
        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 px-6 py-4 rounded-lg shadow-lg z-50 ${
                type === 'success' ? 'bg-green-500' : 'bg-red-500'
            } text-white`;
            notification.innerHTML = `
                <div class="flex items-center gap-3">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                    <span>${message}</span>
                </div>
            `;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        // Unified API fetch with safe JSON parsing and headers
        async function apiFetch(url, options = {}) {
            const defaultHeaders = {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            };
            const csrf = document.querySelector('meta[name="csrf-token"]');
            if (csrf) {
                defaultHeaders['X-CSRF-TOKEN'] = csrf.content;
            }

            const init = { ...options };
            init.headers = {
                ...(options && options.headers ? options.headers : {}),
                ...defaultHeaders
            };

            const response = await fetch(url, init);
            const contentType = response.headers.get('content-type') || '';

            if (!contentType.includes('application/json')) {
                const text = await response.text();
                const snippet = text.slice(0, 200).replace(/\s+/g, ' ').trim();
                throw new Error(`Unexpected response (status ${response.status}). Not JSON. Preview: ${snippet}`);
            }

            const data = await response.json();
            if (!response.ok) {
                const message = (data && (data.message || data.error)) || `HTTP ${response.status}`;
                throw new Error(message);
            }
            return data;
        }

        // Close modals when clicking outside
        document.addEventListener('DOMContentLoaded', function() {
            const modals = ['addTeacherModal', 'editTeacherModal', 'archiveTeacherModal', 'deleteTeacherModal'];
            modals.forEach(modalId => {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.addEventListener('click', function(e) {
                        if (e.target === this) {
                            this.classList.add('hidden');
                            document.body.style.overflow = 'auto';
                        }
                    });
                }
            });
        });

        // Utility function to escape HTML
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>
@endpush