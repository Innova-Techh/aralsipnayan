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
            <button type="button" onclick="closeAddAdminModal()" class="text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <form id="addAdminForm">
            @csrf

            <!-- First Name -->
            <div class="mb-4">
                <label for="addFirstname" class="block text-sm font-medium text-gray-700 mb-2">
                    First Name <span class="text-red-500">*</span>
                </label>
                <input type="text" id="addFirstname" name="firstname"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                    placeholder="Enter first name">
                <p id="firstname_error" class="error-message text-red-500 text-xs mt-1"></p>
            </div>

            <!-- Last Name -->
            <div class="mb-4">
                <label for="addLastname" class="block text-sm font-medium text-gray-700 mb-2">
                    Last Name <span class="text-red-500">*</span>
                </label>
                <input type="text" id="addLastname" name="lastname"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                    placeholder="Enter last name">
                <p id="lastname_error" class="error-message text-red-500 text-xs mt-1"></p>
            </div>

            <!-- Username -->
            <div class="mb-4">
                <label for="addUsername" class="block text-sm font-medium text-gray-700 mb-2">
                    Username <span class="text-red-500">*</span>
                </label>
                <input type="text" id="addUsername" name="username"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                    placeholder="Enter username">
                <p id="username_error" class="error-message text-red-500 text-xs mt-1"></p>
            </div>

            <!-- Email -->
            <div class="mb-4">
                <label for="addEmail" class="block text-sm font-medium text-gray-700 mb-2">
                    Email <span class="text-red-500">*</span>
                </label>
                <input type="email" id="addEmail" name="email"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                    placeholder="Enter email address">
                <p id="email_error" class="error-message text-red-500 text-xs mt-1"></p>
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label for="addPassword" class="block text-sm font-medium text-gray-700 mb-2">
                    Password <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="password" id="addPassword" name="password"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        placeholder="Enter password (min. 8 characters)">
                    <button type="button" onclick="togglePassword('addPassword')"
                        class="absolute right-3 top-2.5 text-gray-500 hover:text-gray-700">
                        <i class="fas fa-eye" id="addPassword-icon"></i>
                    </button>
                </div>
                <p id="password_error" class="error-message text-red-500 text-xs mt-1"></p>
            </div>

            <!-- Info Box -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-4">
                <div class="flex items-start gap-3">
                    <i class="fas fa-info-circle text-blue-600 mt-0.5"></i>
                    <div class="text-sm text-blue-800">
                        <p class="font-medium mb-1">Admin Account Information</p>
                        <ul class="list-disc list-inside space-y-1 text-xs">
                            <li>Admin accounts have full access to management features.</li>
                            <li>Email and username must be unique across all users.</li>
                            <li>Default password should meet a minimum of <strong>8 characters</strong>.</li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- Modal Footer -->
            <div class="flex items-center justify-end gap-3 mt-6">
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
