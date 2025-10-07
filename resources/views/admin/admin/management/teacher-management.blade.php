@extends('admin.admin.layouts.app')

@section('title', 'Teacher Management')

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

        <!-- Livewire Component -->
        @livewire('teacher-table')

        <!-- Add Teacher Modal -->
        @include('admin.components.add-teacher-modal')
    </div>
@endsection

@push('scripts')
    @livewireScripts
    <script>
        function openAddTeacherModal() {
            document.getElementById('addTeacherModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeAddTeacherModal() {
            document.getElementById('addTeacherModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
            // Reset form
            document.getElementById('addTeacherForm').reset();
            // Clear validation errors
            document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
            document.querySelectorAll('.border-red-500').forEach(el => {
                el.classList.remove('border-red-500');
                el.classList.add('border-gray-300');
            });
        }

        // Close modal when clicking outside
        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('addTeacherModal');
            if (modal) {
                modal.addEventListener('click', function (e) {
                    if (e.target === this) {
                        closeAddTeacherModal();
                    }
                });
            }
        });

        // Form validation
        function validateTeacherForm() {
            let isValid = true;
            const fullname = document.getElementById('teacher_fullname');
            const username = document.getElementById('teacher_username');
            const email = document.getElementById('teacher_email');
            const password = document.getElementById('teacher_password');
            const passwordConfirm = document.getElementById('teacher_password_confirmation');
            const gradeLevel = document.getElementById('grade_level_focus');

            // Clear previous errors
            document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
            document.querySelectorAll('.border-red-500').forEach(el => {
                el.classList.remove('border-red-500');
                el.classList.add('border-gray-300');
            });

            // Validate fullname
            if (!fullname.value.trim()) {
                showError('teacher_fullname', 'Full name is required');
                isValid = false;
            }

            // Validate username
            if (!username.value.trim()) {
                showError('teacher_username', 'Username is required');
                isValid = false;
            }

            // Validate email
            if (!email.value.trim()) {
                showError('teacher_email', 'Email is required');
                isValid = false;
            } else if (!isValidEmail(email.value)) {
                showError('teacher_email', 'Please enter a valid email address');
                isValid = false;
            }

            // Validate password
            if (!password.value) {
                showError('teacher_password', 'Password is required');
                isValid = false;
            } else if (password.value.length < 8) {
                showError('teacher_password', 'Password must be at least 8 characters');
                isValid = false;
            }

            // Validate password confirmation
            if (!passwordConfirm.value) {
                showError('teacher_password_confirmation', 'Please confirm your password');
                isValid = false;
            } else if (password.value !== passwordConfirm.value) {
                showError('teacher_password_confirmation', 'Passwords do not match');
                isValid = false;
            }

            // Validate grade level
            if (!gradeLevel.value) {
                showError('grade_level_focus', 'Grade level is required');
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
            const password = document.getElementById('teacher_password');
            const passwordConfirm = document.getElementById('teacher_password_confirmation');

            if (passwordConfirm) {
                passwordConfirm.addEventListener('input', function () {
                    if (password.value && passwordConfirm.value) {
                        if (password.value !== passwordConfirm.value) {
                            showError('teacher_password_confirmation', 'Passwords do not match');
                        } else {
                            const errorElement = document.getElementById('teacher_password_confirmation_error');
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