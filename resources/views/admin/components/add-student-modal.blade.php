<!-- Add Student Modal -->
<div id="addStudentModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-lg bg-white">
        <!-- Modal Header -->
        <div class="flex items-center justify-between pb-4 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-900">Add New Student</h3>
            <button onclick="closeAddStudentModal()" class="text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <form id="addStudentForm" class="mt-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Student ID -->
                <div>
                    <label for="student_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Student ID <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="student_id" name="student_id"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="e.g., 2024-0001">
                    <p id="student_id_error" class="error-message text-red-500 text-xs mt-1"></p>
                </div>

                <!-- Full Name -->
                <div>
                    <label for="full_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="full_name" name="full_name"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="e.g., Juan Dela Cruz">
                    <p id="full_name_error" class="error-message text-red-500 text-xs mt-1"></p>
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <input type="email" id="email" name="email"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="e.g., juan.delacruz@email.com">
                    <p id="email_error" class="error-message text-red-500 text-xs mt-1"></p>
                </div>

                <!-- Grade Level -->
                <div>
                    <label for="grade_level" class="block text-sm font-medium text-gray-700 mb-2">
                        Grade Level <span class="text-red-500">*</span>
                    </label>
                    <select id="grade_level" name="grade_level"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Select Grade Level</option>
                        <option value="7">Grade 7</option>
                        <option value="8">Grade 8</option>
                        <option value="9">Grade 9</option>
                        <option value="10">Grade 10</option>
                    </select>
                    <p id="grade_level_error" class="error-message text-red-500 text-xs mt-1"></p>
                </div>

                <!-- Date of Birth -->
                <div>
                    <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-2">
                        Date of Birth <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="date_of_birth" name="date_of_birth"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <p id="date_of_birth_error" class="error-message text-red-500 text-xs mt-1"></p>
                </div>

                <!-- Gender -->
                <div>
                    <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">
                        Gender <span class="text-red-500">*</span>
                    </label>
                    <select id="gender" name="gender"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Select Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                    <p id="gender_error" class="error-message text-red-500 text-xs mt-1"></p>
                </div>

                <!-- Contact Number -->
                <div>
                    <label for="contact_number" class="block text-sm font-medium text-gray-700 mb-2">
                        Contact Number
                    </label>
                    <input type="tel" id="contact_number" name="contact_number"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="e.g., 09123456789">
                    <p id="contact_number_error" class="error-message text-red-500 text-xs mt-1"></p>
                </div>

                <!-- Guardian Name -->
                <div>
                    <label for="guardian_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Guardian Name
                    </label>
                    <input type="text" id="guardian_name" name="guardian_name"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="e.g., Maria Dela Cruz">
                    <p id="guardian_name_error" class="error-message text-red-500 text-xs mt-1"></p>
                </div>

                <!-- Guardian Contact -->
                <div>
                    <label for="guardian_contact" class="block text-sm font-medium text-gray-700 mb-2">
                        Guardian Contact
                    </label>
                    <input type="tel" id="guardian_contact" name="guardian_contact"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="e.g., 09123456789">
                    <p id="guardian_contact_error" class="error-message text-red-500 text-xs mt-1"></p>
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select id="status" name="status"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="active" selected>Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                    <p id="status_error" class="error-message text-red-500 text-xs mt-1"></p>
                </div>
            </div>

            <!-- Address -->
            <div class="mt-6">
                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                    Address
                </label>
                <textarea id="address" name="address" rows="3"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Complete address"></textarea>
                <p id="address_error" class="error-message text-red-500 text-xs mt-1"></p>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                <button type="button" onclick="closeAddStudentModal()"
                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition">
                    Cancel
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-blue-900 text-white rounded-lg text-sm font-medium hover:bg-blue-800 transition">
                    <i class="fas fa-plus mr-2"></i> Add Student
                </button>
            </div>
        </form>
    </div>
</div>