@extends('admin.teacher.layouts.app')

@section('title', 'AralSipnayan')

@section('content')
<div>
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Profile Settings</h1>
            <p class="text-gray-600 mt-1">Manage your teacher account settings and preferences</p>
        </div>

        <!-- Profile Information -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Profile Information</h2>
            <form id="profileForm">
                @csrf
                <div class="flex items-center space-x-6 mb-6">
                    <img id="profileImage" class="h-20 w-20 rounded-full object-cover" 
                         src="{{ $profile->profile_url ? asset('storage/' . $profile->profile_url) : asset('images/profile/avatar1.png') }}" 
                         alt="Profile Picture">
                    <div>
                        <input type="file" id="photoInput" name="photo" accept="image/*" class="hidden">
                        <button type="button" onclick="document.getElementById('photoInput').click()" 
                                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm">
                            Change Photo
                        </button>
                        <p class="text-xs text-gray-500 mt-1">JPG, GIF or PNG. 1MB max.</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                        <input type="text" name="firstname" value="{{ $profile->firstname }}" 
                               required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                        <input type="text" name="lastname" value="{{ $profile->lastname }}" 
                               required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" value="{{ Auth::guard('admin')->user()->email }}" 
                               required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">School Name</label>
                        <input type="text" name="school_name" value="{{ $profile->school_name }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>

        <!-- Teaching Statistics -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Teaching Statistics</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-600">{{ $stats['total_students'] ?? 0 }}</div>
                    <div class="text-sm text-gray-600">Total Students</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-green-600">{{ $stats['total_assessments'] ?? 0 }}</div>
                    <div class="text-sm text-gray-600">Assessments Created</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-purple-600">{{ $stats['active_sections'] ?? 0 }}</div>
                    <div class="text-sm text-gray-600">Active Sections</div>
                </div>
            </div>
        </div>

        <!-- Security Settings -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Security Settings</h2>
            <form id="passwordForm">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                        <input type="password" name="current_password" placeholder="Enter current password" 
                               required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                        <input type="password" name="new_password" placeholder="Enter new password" 
                               required minlength="8"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                        <input type="password" name="confirm_password" placeholder="Confirm new password" 
                               required minlength="8"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Photo upload handler
    document.getElementById('photoInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;

        // Validate file size (1MB = 1048576 bytes)
        if (file.size > 1048576) {
            Swal.fire({
                icon: 'error',
                title: 'File Too Large',
                text: 'Please select an image under 1MB',
                confirmButtonColor: '#2563eb'
            });
            return;
        }

        // Validate file type
        if (!file.type.match('image.*')) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid File Type',
                text: 'Please select an image file (JPG, PNG, GIF)',
                confirmButtonColor: '#2563eb'
            });
            return;
        }

        // Show confirmation
        Swal.fire({
            title: 'Update Profile Photo?',
            text: 'Are you sure you want to update your profile photo?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#2563eb',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, update it!'
        }).then((result) => {
            if (result.isConfirmed) {
                uploadPhoto(file);
            } else {
                // Reset file input
                e.target.value = '';
            }
        });
    });

    function uploadPhoto(file) {
        const formData = new FormData();
        formData.append('photo', file);
        formData.append('_token', '{{ csrf_token() }}');

        // Show loading
        Swal.fire({
            title: 'Uploading...',
            text: 'Please wait while we update your photo',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch('{{ route('teacher.profile.update-photo') }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update image
                document.getElementById('profileImage').src = data.photo_url;
                
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: data.message,
                    confirmButtonColor: '#2563eb'
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: data.message,
                    confirmButtonColor: '#2563eb'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'An error occurred while uploading the photo',
                confirmButtonColor: '#2563eb'
            });
        })
        .finally(() => {
            // Reset file input
            document.getElementById('photoInput').value = '';
        });
    }

    // Profile form submit handler
    document.getElementById('profileForm').addEventListener('submit', function(e) {
        e.preventDefault();

        Swal.fire({
            title: 'Save Changes?',
            text: 'Are you sure you want to update your profile information?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#2563eb',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, save it!'
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData(this);

                // Show loading
                Swal.fire({
                    title: 'Saving...',
                    text: 'Please wait while we update your profile',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch('{{ route('teacher.profile.update') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: data.message,
                            confirmButtonColor: '#2563eb'
                        }).then(() => {
                            // Reload page to reflect changes
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: data.message,
                            confirmButtonColor: '#2563eb'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'An error occurred while updating your profile',
                        confirmButtonColor: '#2563eb'
                    });
                });
            }
        });
    });

    // Password form submit handler
    document.getElementById('passwordForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const newPassword = this.querySelector('[name="new_password"]').value;
        const confirmPassword = this.querySelector('[name="confirm_password"]').value;

        // Validate passwords match
        if (newPassword !== confirmPassword) {
            Swal.fire({
                icon: 'error',
                title: 'Passwords Do Not Match',
                text: 'Please make sure your new password and confirmation password match',
                confirmButtonColor: '#2563eb'
            });
            return;
        }

        Swal.fire({
            title: 'Update Password?',
            text: 'Are you sure you want to change your password?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#2563eb',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, update it!'
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData(this);

                // Show loading
                Swal.fire({
                    title: 'Updating...',
                    text: 'Please wait while we update your password',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch('{{ route('teacher.profile.update-password') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: data.message,
                            confirmButtonColor: '#2563eb'
                        }).then(() => {
                            // Reset form
                            document.getElementById('passwordForm').reset();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: data.message,
                            confirmButtonColor: '#2563eb'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'An error occurred while updating your password',
                        confirmButtonColor: '#2563eb'
                    });
                });
            }
        });
    });
</script>

@endsection