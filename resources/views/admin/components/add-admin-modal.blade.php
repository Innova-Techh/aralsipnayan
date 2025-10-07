<!-- Add Admin Modal -->
<div id="addAdminModal"
    class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
    <div class="relative p-5 border w-full max-w-md shadow-lg rounded-lg bg-white mx-4">
        <!-- Modal Header -->
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-semibold text-gray-900">
                <i class="fas fa-user-plus text-blue-600 mr-2"></i>
                Add New Admin
            </h3>
            <button onclick="closeAddAdminModal()" class="text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <form id="addAdminForm" action="{{ route('admin.management.admins.store') }}" method="POST"
            onsubmit="return validateForm()">
            @csrf

            <!-- Full Name -->
            <div class="mb-4">
                <label for="fullname" class="block text-sm font-medium text-gray-700 mb-2">
                    Full Name <span class="text-red-500">*</span>
                </label>
                <input type="text" id="fullname" name="fullname"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                    placeholder="Enter full name">
                <p id="fullname_error" class="error-message text-red-500 text-xs mt-1"></p>
            </div>

            <!-- Username -->
            <div class="mb-4">
                <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                    Username <span class="text-red-500">*</span>
                </label>
                <input type="text" id="username" name="username"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                    placeholder="Enter username">
                <p id="username_error" class="error-message text-red-500 text-xs mt-1"></p>
            </div>


            <!-- Password -->
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                    Password <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="password" id="password" name="password"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        placeholder="Enter password (min. 8 characters)">
                    <button type="button" onclick="togglePassword('password')"
                        class="absolute right-3 top-2.5 text-gray-500 hover:text-gray-700">
                        <i class="fas fa-eye" id="password-icon"></i>
                    </button>
                </div>
                <p id="password_error" class="error-message text-red-500 text-xs mt-1"></p>
            </div>

            <!-- Re-type Password -->
            <div class="mb-6">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                    Re-type Password <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="password" id="password_confirmation" name="password_confirmation"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        placeholder="Re-enter password">
                    <button type="button" onclick="togglePassword('password_confirmation')"
                        class="absolute right-3 top-2.5 text-gray-500 hover:text-gray-700">
                        <i class="fas fa-eye" id="password_confirmation-icon"></i>
                    </button>
                </div>
                <p id="password_confirmation_error" class="error-message text-red-500 text-xs mt-1"></p>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end gap-3">
                <button type="button" onclick="closeAddAdminModal()"
                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition font-medium">
                    Cancel
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-blue-900 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                    <i class="fas fa-save mr-2"></i>
                    Create Admin
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const icon = document.getElementById(fieldId + '-icon');

        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            field.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>