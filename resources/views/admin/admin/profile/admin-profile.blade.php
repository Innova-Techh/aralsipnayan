@extends('admin.admin.layouts.app')

@section('content')
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Profile Settings</h1>
                <p class="mt-2 text-gray-600">Manage your admin account settings and preferences</p>
            </div>

            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Profile Information Card -->
            <div class="bg-white rounded-lg shadow-sm mb-6">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Profile Information</h2>

                    <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Profile Photo -->
                        <div class="mb-6 flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <img src="{{ auth()->guard('admin')->user()->adminProfile->profile_photo ?? asset('images/default-avatar.png') }}"
                                    alt="Profile Photo" class="w-24 h-24 rounded-full object-cover border-4 border-gray-100"
                                    id="profilePhotoPreview">
                            </div>
                            <div class="flex-grow">
                                <label for="profile_photo"
                                    class="inline-block bg-blue-600 text-white px-4 py-2 rounded-lg cursor-pointer hover:bg-blue-700 transition">
                                    Change Photo
                                </label>
                                <input type="file" id="profile_photo" name="profile_photo"
                                    accept="image/jpeg,image/png,image/gif" class="hidden" onchange="previewImage(this)">
                                <p class="mt-2 text-sm text-gray-500">JPG, GIF or PNG. 1MB max.</p>
                                @error('profile_photo')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Form Fields Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Full Name -->
                            <div>
                                <label for="fullname" class="block text-sm font-medium text-gray-700 mb-2">
                                    Full Name
                                </label>
                                <input type="text" id="fullname" name="fullname"
                                    value="{{ old('fullname', auth()->guard('admin')->user()->adminProfile->firstname . ' ' . auth()->guard('admin')->user()->adminProfile->lastname) }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    required>
                                @error('fullname')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                    Email
                                </label>
                                <input type="email" id="email" name="email"
                                    value="{{ old('email', auth()->guard('admin')->user()->email) }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    required>
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Username -->
                            <div>
                                <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                                    Username
                                </label>
                                <input type="text" id="username" name="username"
                                    value="{{ old('username', auth()->guard('admin')->user()->username) }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    required>
                                @error('username')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- School Name -->
                            <div>
                                <label for="school_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    School Name
                                </label>
                                <input type="text" id="school_name" name="school_name"
                                    value="{{ old('school_name', auth()->guard('admin')->user()->adminProfile->school_name) }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('school_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Grade Level Focus -->
                            <div>
                                <label for="grade_level_focus" class="block text-sm font-medium text-gray-700 mb-2">
                                    Grade Level Focus
                                </label>
                                <input type="text" id="grade_level_focus" name="grade_level_focus"
                                    value="{{ old('grade_level_focus', auth()->guard('admin')->user()->adminProfile->grade_level_focus) }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="e.g., Grade 7-12">
                                @error('grade_level_focus')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Save Button -->
                        <div class="mt-6 flex justify-end">
                            <button type="submit"
                                class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition font-medium">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Admin Statistics Card -->
            <div class="bg-white rounded-lg shadow-sm mb-6">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Admin Statistics</h2>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Total Teachers -->
                        <div class="text-center">
                            <div class="text-4xl font-bold text-blue-600">
                                {{ \App\Models\User::where('role', 'Teacher')->where('status', 'active')->count() }}
                            </div>
                            <div class="text-gray-600 mt-2">Total Teachers</div>
                        </div>

                        <!-- Total Students -->
                        <div class="text-center">
                            <div class="text-4xl font-bold text-green-600">
                                {{ \App\Models\User::where('role', 'Student')->where('status', 'active')->count() }}
                            </div>
                            <div class="text-gray-600 mt-2">Total Students</div>
                        </div>

                        <!-- Total Admins -->
                        <div class="text-center">
                            <div class="text-4xl font-bold text-purple-600">
                                {{ \App\Models\User::where('role', 'Admin')->where('status', 'active')->count() }}
                            </div>
                            <div class="text-gray-600 mt-2">Total Admins</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Security Settings Card -->
            <div class="bg-white rounded-lg shadow-sm mb-6">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Security Settings</h2>

                    <form action="{{ route('admin.profile.update-password') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Current Password -->
                        <div class="mb-6">
                            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                                Current Password
                            </label>
                            <input type="password" id="current_password" name="current_password"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Enter current password" required>
                            @error('current_password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- New Password -->
                        <div class="mb-6">
                            <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">
                                New Password
                            </label>
                            <input type="password" id="new_password" name="new_password"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Enter new password" required>
                            @error('new_password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">Must be at least 8 characters long</p>
                        </div>

                        <!-- Confirm New Password -->
                        <div class="mb-6">
                            <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                Confirm New Password
                            </label>
                            <input type="password" id="new_password_confirmation" name="new_password_confirmation"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Confirm new password" required>
                        </div>

                        <!-- Update Password Button -->
                        <div class="flex justify-end">
                            <button type="submit"
                                class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition font-medium">
                                Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Account Activity Card -->
            <div class="bg-white rounded-lg shadow-sm mb-6">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Account Activity</h2>

                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-3 border-b border-gray-200">
                            <div>
                                <p class="font-medium text-gray-900">Account Created</p>
                                <p class="text-sm text-gray-500">
                                    {{ auth()->guard('admin')->user()->created_at->format('F d, Y') }}
                                </p>
                            </div>
                        </div>

                        <div class="flex justify-between items-center py-3 border-b border-gray-200">
                            <div>
                                <p class="font-medium text-gray-900">Last Login</p>
                                <p class="text-sm text-gray-500">
                                    {{ auth()->guard('admin')->user()->last_login_at ? auth()->guard('admin')->user()->last_login_at->diffForHumans() : 'Never' }}
                                </p>
                            </div>
                        </div>

                        <div class="flex justify-between items-center py-3 border-b border-gray-200">
                            <div>
                                <p class="font-medium text-gray-900">Account Status</p>
                                <p class="text-sm text-gray-500">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ auth()->guard('admin')->user()->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ucfirst(auth()->guard('admin')->user()->status) }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <div class="flex justify-between items-center py-3">
                            <div>
                                <p class="font-medium text-gray-900">Role</p>
                                <p class="text-sm text-gray-500">{{ auth()->guard('admin')->user()->role }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Danger Zone Card -->
            <div class="bg-white rounded-lg shadow-sm border-2 border-red-200">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-red-600 mb-4">Danger Zone</h2>
                    <p class="text-gray-600 mb-4">Once you delete your account, there is no going back. Please be certain.
                    </p>

                    <button type="button" onclick="confirmDeleteAccount()"
                        class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition font-medium">
                        Delete Account
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Account Confirmation Modal -->
    <div id="deleteAccountModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg max-w-md w-full mx-4 p-6">
            <h3 class="text-xl font-bold text-gray-900 mb-4">Delete Account</h3>
            <p class="text-gray-600 mb-6">Are you sure you want to delete your account? This action cannot be undone.</p>

            <form action="{{ route('admin.profile.delete') }}" method="POST">
                @csrf
                @method('DELETE')

                <div class="mb-4">
                    <label for="delete_password" class="block text-sm font-medium text-gray-700 mb-2">
                        Confirm your password
                    </label>
                    <input type="password" id="delete_password" name="password"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                        required>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeDeleteModal()"
                        class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        Cancel
                    </button>
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                        Delete Account
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById('profilePhotoPreview').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function confirmDeleteAccount() {
            document.getElementById('deleteAccountModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteAccountModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('deleteAccountModal').addEventListener('click', function (e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });
    </script>
@endsection