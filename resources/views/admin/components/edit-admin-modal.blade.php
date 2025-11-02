<!-- Edit Admin Modal -->
<div id="editAdminModal"
    class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
    <div class="relative p-5 border w-full max-w-md shadow-lg rounded-lg bg-white mx-4">

        <!-- Modal Header -->
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-semibold text-gray-900">
                <i class="fas fa-user-edit text-green-600 mr-2"></i>
                Edit Admin Details
            </h3>
            <button type="button" onclick="closeEditAdminModal()" class="text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <form id="editAdminForm">
            @csrf
            @method('PUT')

            <!-- Hidden ID -->
            <input type="hidden" id="editAdminId" name="id">

            <!-- First Name -->
            <div class="mb-4">
                <label for="editFirstname" class="block text-sm font-medium text-gray-700 mb-2">
                    First Name <span class="text-red-500">*</span>
                </label>
                <input type="text" id="editFirstname" name="firstname"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                    placeholder="Enter first name">
                <p id="edit_firstname_error" class="error-message text-red-500 text-xs mt-1"></p>
            </div>

            <!-- Last Name -->
            <div class="mb-4">
                <label for="editLastname" class="block text-sm font-medium text-gray-700 mb-2">
                    Last Name <span class="text-red-500">*</span>
                </label>
                <input type="text" id="editLastname" name="lastname"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                    placeholder="Enter last name">
                <p id="edit_lastname_error" class="error-message text-red-500 text-xs mt-1"></p>
            </div>

            <!-- Username -->
            <div class="mb-4">
                <label for="editUsername" class="block text-sm font-medium text-gray-700 mb-2">
                    Username <span class="text-red-500">*</span>
                </label>
                <input type="text" id="editUsername" name="username"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                    placeholder="Enter username">
                <p id="edit_username_error" class="error-message text-red-500 text-xs mt-1"></p>
            </div>

            <!-- Email -->
            <div class="mb-4">
                <label for="editEmail" class="block text-sm font-medium text-gray-700 mb-2">
                    Email <span class="text-red-500">*</span>
                </label>
                <input type="email" id="editEmail" name="email"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                    placeholder="Enter email address">
                <p id="edit_email_error" class="error-message text-red-500 text-xs mt-1"></p>
            </div>

            <!-- Status -->
            <div class="mb-4">
                <label for="editStatus" class="block text-sm font-medium text-gray-700 mb-2">
                    Status
                </label>
                <select id="editStatus" name="status"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition">
                    <option value="active">Active</option>
                    <option value="archive">Archive</option>
                </select>
                <p id="edit_status_error" class="error-message text-red-500 text-xs mt-1"></p>
            </div>

            <!-- Password Fields -->
           
            <div class="mb-4">
                <label for="editPassword" class="block text-sm font-medium text-gray-700 mb-2">
                    New Password
                </label>
                <div class="relative">
                    <input type="password" id="editPassword" name="password"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                        placeholder="Enter new password">
                    <button type="button" onclick="togglePassword('editPassword')"
                        class="absolute right-3 top-2.5 text-gray-500 hover:text-gray-700">
                        <i class="fas fa-eye" id="editPassword-icon"></i>
                    </button>
                </div>
                <p id="edit_password_error" class="error-message text-red-500 text-xs mt-1"></p>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end gap-3 mt-6">
                <button type="button" onclick="closeEditAdminModal()"
                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition font-medium">
                    Cancel
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-green-700 text-white rounded-lg hover:bg-green-800 transition font-medium">
                    <i class="fas fa-save mr-2"></i>
                    Update Admin
                </button>
            </div>
        </form>
    </div>
</div>