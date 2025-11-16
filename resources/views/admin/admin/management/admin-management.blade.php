@extends('admin.admin.layouts.app')

@section('title', 'AralSipnayan')

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
                        <span class="ml-1 text-sm font-medium text-gray-900 md:ml-2">Admin Management</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Admin Management</h1>
                <p class="text-gray-600 text-sm mt-1">Manage system administrators</p>
            </div>

            <div class="flex gap-3 mt-4 sm:mt-0">
                <button onclick="openAddAdminModal()"
                    class="flex items-center gap-2 px-4 py-2 bg-blue-900 text-white rounded-lg text-sm font-medium hover:bg-blue-800 transition">
                    <i class="fas fa-user-plus"></i> Add Admin
                </button>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Total Admins -->
            <div class="bg-white rounded-lg border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Total Admins</p>
                        <h3 class="text-2xl font-bold text-gray-900">{{ $totalAdmins }}</h3>
                        <p class="text-xs text-gray-500 mt-1">All administrators</p>
                    </div>
                    <div class="bg-blue-50 rounded-lg p-3">
                        <i class="fas fa-user-shield text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Active Admins -->
            <div class="bg-white rounded-lg border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Active Admins</p>
                        <h3 class="text-2xl font-bold text-gray-900">{{ $activeAdmins }}</h3>
                        <p class="text-xs text-gray-500 mt-1">Currently active</p>
                    </div>
                    <div class="bg-green-50 rounded-lg p-3">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Archived Admins -->
            <div class="bg-white rounded-lg border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Archived Admins</p>
                        <h3 class="text-2xl font-bold text-gray-900">{{ $archivedAdmins }}</h3>
                        <p class="text-xs text-gray-500 mt-1">Archived</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <i class="fas fa-archive text-gray-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Admins Table -->
        <div class="bg-white rounded-lg border border-gray-200">
            <div class="p-5 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Administrator List</h2>
                <p class="text-sm text-gray-600 mt-1">All system administrators</p>
            </div>

            <div class="p-5">
                <!-- Search and Filter Bar -->
                <div class="flex flex-col sm:flex-row gap-3 mb-5">
                    <div class="flex-1">
                        <div class="relative">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <input type="text" id="searchInput" placeholder="Search by name, username, or email..."
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
                                    Username
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
                                    School
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Created Date
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="adminTableBody">
                            @forelse($admins as $admin)
                                <tr class="hover:bg-gray-50 transition admin-row"
                                    data-username="{{ strtolower($admin->username) }}"
                                    data-name="{{ strtolower($admin->adminProfile ? $admin->adminProfile->firstname . ' ' . $admin->adminProfile->lastname : '') }}"
                                    data-email="{{ strtolower($admin->email) }}"
                                    data-status="{{ strtolower($admin->status) }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 admin-username">
                                            {{ $admin->username }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900 admin-name">
                                                    {{ $admin->adminProfile ? $admin->adminProfile->firstname . ' ' . $admin->adminProfile->lastname : 'N/A' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 admin-email">{{ $admin->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-600">
                                            {{ $admin->adminProfile->school_name ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusColors = [
                                                'active' => 'bg-green-100 text-green-800',
                                                'inactive' => 'bg-yellow-100 text-yellow-800',
                                                'archive' => 'bg-gray-100 text-gray-800',
                                            ];
                                            $statusColor = $statusColors[$admin->status] ?? 'bg-gray-100 text-gray-800';
                                        @endphp
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColor }} admin-status">
                                            {{ ucfirst($admin->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $admin->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-2">
                                            <button onclick="openEditAdminModal({{ $admin->id }})"
                                                class="text-blue-600 hover:text-blue-900 transition" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button onclick="openArchiveAdminModal({{ $admin->id }}, '{{ $admin->adminProfile ? $admin->adminProfile->firstname . ' ' . $admin->adminProfile->lastname : $admin->username }}', '{{ $admin->status }}')"
                                                class="text-yellow-600 hover:text-yellow-900 transition" title="{{ $admin->status === 'active' ? 'Archive' : 'Activate' }}">
                                                <i class="fas fa-{{ $admin->status === 'active' ? 'archive' : 'check' }}"></i>
                                            </button>
                                            <button onclick="openDeleteAdminModal({{ $admin->id }}, '{{ $admin->adminProfile ? $admin->adminProfile->firstname . ' ' . $admin->adminProfile->lastname : $admin->username }}')"
                                                class="text-red-600 hover:text-red-900 transition" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <i class="fas fa-user-shield text-gray-300 text-5xl mb-4"></i>
                                            <h3 class="text-lg font-medium text-gray-900 mb-1">No admins found</h3>
                                            <p class="text-sm text-gray-500">Get started by adding your first admin</p>
                                            <p id="noResults" class="hidden text-center text-gray-500 py-4">No admins found.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Results Info -->
                <div class="mt-4 text-sm text-gray-600 text-center">
                    Showing <span id="visibleCount">{{ $admins->count() }}</span> of {{ $admins->count() }} admin(s)
                </div>
            </div>
        </div>

        <!-- Modals -->
        @include('admin.components.add-admin-modal')
        @include('admin.components.edit-admin-modal')
        @include('admin.components.archive-admin-modal')
        @include('admin.components.delete-admin-modal')
    </div>
@endsection

@push('scripts')
   <script>
    
    // ===============================================
    // UTILITY FUNCTIONS
    // ===============================================
    // Search and Filter
    document.addEventListener("DOMContentLoaded", () => {
        const searchInput = document.getElementById("searchInput");
        const statusFilter = document.getElementById("statusFilter");
        const rows = document.querySelectorAll(".admin-row");

        function filterAdmins() {
            const searchValue = searchInput.value.toLowerCase().trim();
            const statusValue = statusFilter.value.toLowerCase();

            rows.forEach(row => {
                const name = row.dataset.name;
                const username = row.dataset.username;
                const email = row.dataset.email;
                const status = row.dataset.status;

                const matchesSearch =
                    name.includes(searchValue) ||
                    username.includes(searchValue) ||
                    email.includes(searchValue);

                const matchesStatus =
                    statusValue === "all" || status === statusValue;

                if (matchesSearch && matchesStatus) {
                    row.classList.remove("hidden");
                } else {
                    row.classList.add("hidden");
                }
            });
        }

        searchInput.addEventListener("input", filterAdmins);
        statusFilter.addEventListener("change", filterAdmins);
    });

    function filterAdmins() {
        const searchValue = searchInput.value.toLowerCase().trim();
        const statusValue = statusFilter.value.toLowerCase();

        let visibleCount = 0;
        rows.forEach(row => {
            const name = row.dataset.name;
            const username = row.dataset.username;
            const email = row.dataset.email;
            const status = row.dataset.status;

            const matchesSearch =
                name.includes(searchValue) ||
                username.includes(searchValue) ||
                email.includes(searchValue);

            const matchesStatus =
                statusValue === "all" || status === statusValue;

            if (matchesSearch && matchesStatus) {
                row.classList.remove("hidden");
                visibleCount++;
            } else {
                row.classList.add("hidden");
            }
        });

        document.getElementById("noResults").classList.toggle("hidden", visibleCount > 0);
    }
    
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
    // Clear all validation error messages
    function clearErrors() {
        document.querySelectorAll('.error-message').forEach(el => {
            el.textContent = '';
        });
    }
    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return;
        modal.classList.remove('hidden');
        modal.classList.add('flex', 'animate-fadeIn');
        document.body.style.overflow = 'hidden';
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return;

        modal.classList.add('animate-fadeOut');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex', 'animate-fadeIn', 'animate-fadeOut');
            document.body.style.overflow = 'auto';
        }, 200); // fade-out duration
    }

    // Simple notification handler
    function showNotification(message, type = 'success') {
        const notif = document.createElement('div');
        notif.className = `fixed top-5 right-5 z-50 px-4 py-2 rounded-lg shadow-lg text-white text-sm transition-opacity duration-300 ${
            type === 'success' ? 'bg-green-600' : 'bg-red-600'
        }`;
        notif.textContent = message;
        document.body.appendChild(notif);
        setTimeout(() => notif.remove(), 2500);
    }

    // ===============================================
    // ADD ADMIN MODAL
    // ===============================================
    function openAddAdminModal() {
        openModal('addAdminModal');
    }

    function closeAddAdminModal() {
        closeModal('addAdminModal');
        document.getElementById('addAdminForm').reset();
        clearErrors?.();
    }

    // ===============================================
    // ADD ADMIN FORM SUBMISSION
    // ===============================================
    document.getElementById('addAdminForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Clear previous errors
        clearErrors();
        
        const formData = {
            firstname: document.getElementById('addFirstname').value,
            lastname: document.getElementById('addLastname').value,
            username: document.getElementById('addUsername').value,
            email: document.getElementById('addEmail').value,
            password: document.getElementById('addPassword').value,
        };

        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Adding...';
        
        fetch('/admin/management/admins', {
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
                closeAddAdminModal();
                setTimeout(() => window.location.reload(), 1000);
            } else {
                if (data.errors) {
                    displayErrors(data.errors, 'add');
                } else {
                    showNotification(data.message || 'Failed to add admin', 'error');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred while adding admin', 'error');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    });

    // ===============================================
    // EDIT ADMIN MODAL AUTO-FILL FUNCTIONALITY
    // ===============================================
    function openEditAdminModal(adminId) {
        openModal('editAdminModal');

        // Reset fields
        document.getElementById('editAdminForm').reset();
        clearErrors?.();

        fetch(`/admin/management/admins/${adminId}/edit`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const a = data.admin;
                document.getElementById('editAdminId').value = a.id;
                document.getElementById('editFirstname').value = a.firstname;
                document.getElementById('editLastname').value = a.lastname;
                document.getElementById('editUsername').value = a.username;
                document.getElementById('editEmail').value = a.email;
                document.getElementById('editStatus').value = a.status;
            } else {
                showNotification(data.message || 'Failed to load admin data', 'error');
                closeEditAdminModal();
            }
        })
        .catch(() => {
            showNotification('Error loading admin data', 'error');
            closeEditAdminModal();
        });
    }

    function closeEditAdminModal() {
        closeModal('editAdminModal');
        document.getElementById('editAdminForm').reset();
        clearErrors?.();
    }

    // ===============================================
    // EDIT ADMIN FORM SUBMISSION
    // ===============================================
    document.getElementById('editAdminForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        clearErrors?.();

        const adminId = document.getElementById('editAdminId').value;

        const formData = {
            firstname: document.getElementById('editFirstname').value,
            lastname: document.getElementById('editLastname').value,
            username: document.getElementById('editUsername').value,
            email: document.getElementById('editEmail').value,
            status: document.getElementById('editStatus').value,
            password: document.getElementById('editPassword').value,
        };


        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Updating...';

        fetch(`/admin/management/admins/${adminId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(formData)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showNotification('Admin updated successfully', 'success');
                setTimeout(() => {
                    closeEditAdminModal();
                    window.location.reload();
                }, 1000);
            } else {
                if (data.errors) displayErrors?.(data.errors, 'edit');
                else showNotification(data.message || 'Failed to update admin', 'error');
            }
        })
        .catch(() => showNotification('Error updating admin', 'error'))
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    });

    // ===============================================
    // ARCHIVE / ACTIVATE ADMIN MODAL
    // ===============================================
    function openArchiveAdminModal(adminId, adminName, currentStatus) {
        document.getElementById('archiveAdminId').value = adminId;
        document.getElementById('archiveAdminCurrentStatus').value = currentStatus;

        const isActive = currentStatus === 'active';
        const title = document.getElementById('archiveAdminTitle');
        const message = document.getElementById('archiveAdminMessage');
        const button = document.getElementById('archiveAdminButton');
        const icon = document.getElementById('archiveAdminIcon');
        const info = document.getElementById('archiveAdminInfo');
        const actions = document.getElementById('archiveAdminActions');

        if (isActive) {
            title.textContent = 'Archive Admin';
            message.innerHTML = `Archive <b>${adminName}</b>?`;
            button.innerHTML = '<i class="fas fa-archive mr-2"></i> Archive Admin';
            button.className = 'px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700';
            icon.innerHTML = '<i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>';
            info.innerHTML = `
                <li>Set ${adminName} to <b>Archived</b></li>
                <li>Prevent login access</li>
                <li>Can be reactivated anytime</li>
            `;
        } else {
            title.textContent = 'Activate Admin';
            message.innerHTML = `Activate <b>${adminName}</b>?`;
            button.innerHTML = '<i class="fas fa-check mr-2"></i> Activate Admin';
            button.className = 'px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700';
            icon.innerHTML = '<i class="fas fa-check-circle text-green-600 text-xl"></i>';
            info.innerHTML = `
                <li>Set ${adminName} to <b>Active</b></li>
                <li>Restores full access</li>
            `;
        }

        openModal('archiveAdminModal');
    }

    // ===============================================
    // CONFIRM ARCHIVE / ACTIVATE ADMIN SUBMISSION
    // ===============================================

    document.getElementById('archiveAdminForm')?.addEventListener('submit', function(e) {
        e.preventDefault();

        const adminId = document.getElementById('archiveAdminId').value;
        const currentStatus = document.getElementById('archiveAdminCurrentStatus').value;
        const newStatus = currentStatus === 'active' ? 'archived' : 'active';

        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Updating...';

        fetch(`/admin/management/admins/${adminId}/status`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ status: newStatus })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                setTimeout(() => {
                    closeModal('archiveAdminModal');
                    window.location.reload();
                }, 1000);
            } else {
                showNotification(data.message || 'Failed to update admin status', 'error');
            }
        })
        .catch(() => showNotification('Error updating admin status', 'error'))
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    });
    // ===============================================
    // OPEN DELETE ADMIN MODAL
    // ===============================================
    function openDeleteAdminModal(adminId, adminName) {
        document.getElementById('deleteAdminId').value = adminId;
        document.getElementById('deleteAdminName').textContent = adminName;
        openModal('deleteAdminModal');
    }

    // ===============================================
    // CLOSE DELETE ADMIN MODAL
    // ===============================================
    function closeDeleteAdminModal() {
        closeModal('deleteAdminModal');
        document.getElementById('deleteAdminForm').reset();
    }

    // ===============================================
    // DELETE ADMIN FORM SUBMISSION
    // ===============================================
    document.getElementById('deleteAdminForm')?.addEventListener('submit', function (e) {
        e.preventDefault();

        const adminId = document.getElementById('deleteAdminId').value;
        const submitBtn = document.getElementById('deleteAdminButton');
        const originalText = submitBtn.innerHTML;

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Deleting...';

        fetch(`/admin/management/admins/${adminId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    setTimeout(() => {
                        closeDeleteAdminModal();
                        window.location.reload();
                    }, 1000);
                } else {
                    showNotification(data.message || 'Failed to delete admin', 'error');
                }
            })
            .catch(err => {
                console.error('Delete error:', err);
                showNotification('Error deleting admin', 'error');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
    });
   </script>
@endpush