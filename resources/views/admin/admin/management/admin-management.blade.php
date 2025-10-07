@extends('admin.admin.layouts.app')

@section('title', 'Admin Management')

@section('content')
    <div class="p-6">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Admin Management</h1>
                <p class="text-gray-600 text-sm">Manage system administrators</p>
            </div>

            <div class="flex space-x-3 mt-4 sm:mt-0">
                <button onclick="openAddAdminModal()"
                    class="flex items-center gap-2 px-4 py-2 bg-blue-900 text-white rounded-lg text-sm font-medium hover:bg-blue-800 transition">
                    <i class="fas fa-plus"></i> Add Admin
                </button>
            </div>
        </div>

        <!-- Livewire Component -->
        @livewire('admin-table')

        <!-- Add Admin Modal -->
        @include('admin.components.add-admin-modal')
    </div>
@endsection

@push('scripts')
    @livewireScripts
    <script>
        function openAddAdminModal() {
            document.getElementById('addAdminModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeAddAdminModal() {
            document.getElementById('addAdminModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
            // Reset form
            document.getElementById('addAdminForm').reset();
            // Clear validation errors
            document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
            document.querySelectorAll('.border-red-500').forEach(el => {
                el.classList.remove('border-red-500');
                el.classList.add('border-gray-300');
            });
        }

        // Close modal when clicking outside
        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('addAdminModal');
            if (modal) {
                modal.addEventListener('click', function (e) {
                    if (e.target === this) {
                        closeAddAdminModal();
                    }
                });
            }
        });

        // Form validation
        function validateForm() {
            let isValid = true;
            const fullname = document.getElementById('fullname');
            const username = document.getElementById('username');
            const email = document.getElementById('email');
            const password = document.getElementById('password');
            const passwordConfirm = document.getElementById('password_confirmation');

            // Clear previous errors
            document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
            document.querySelectorAll('.border-red-500').forEach(el => {
                el.classList.remove('border-red-500');
                el.classList.add('border-gray-300');
            });

            // Validate fullname
            if (!fullname.value.trim()) {
                showError('fullname', 'Full name is required');
                isValid = false;
            }

            // Validate username
            if (!username.value.trim()) {
                showError('username', 'Username is required');
                isValid = false;
            }

            // Validate email
            if (!email.value.trim()) {
                showError('email', 'Email is required');
                isValid = false;
            } else if (!isValidEmail(email.value)) {
                showError('email', 'Please enter a valid email address');
                isValid = false;
            }

            // Validate password
            if (!password.value) {
                showError('password', 'Password is required');
                isValid = false;
            } else if (password.value.length < 8) {
                showError('password', 'Password must be at least 8 characters');
                isValid = false;
            }

            // Validate password confirmation
            if (!passwordConfirm.value) {
                showError('password_confirmation', 'Please confirm your password');
                isValid = false;
            } else if (password.value !== passwordConfirm.value) {
                showError('password_confirmation', 'Passwords do not match');
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

        function isValidEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }

        // Real-time password match validation
        document.addEventListener('DOMContentLoaded', function () {
            const password = document.getElementById('password');
            const passwordConfirm = document.getElementById('password_confirmation');

            if (passwordConfirm) {
                passwordConfirm.addEventListener('input', function () {
                    if (password.value && passwordConfirm.value) {
                        if (password.value !== passwordConfirm.value) {
                            showError('password_confirmation', 'Passwords do not match');
                        } else {
                            const errorElement = document.getElementById('password_confirmation_error');
                            if (errorElement) {
                                errorElement.textContent = '';
                            }
                            passwordConfirm.classList.remove('border-red-500');
                            passwordConfirm.classList.add('border-green-500');
                        }
                    }
                });
            }
        });
    </script>
@endpush

@push('styles')
    @livewireStyles
@endpush