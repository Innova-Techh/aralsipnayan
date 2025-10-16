<!-- Edit Student Modal -->
<div id="editStudentModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-lg bg-white">
        <!-- Modal Header -->
        <div class="flex items-center justify-between pb-4 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-900">Edit Student Information</h3>
            <button onclick="closeEditStudentModal()" class="text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <form id="editStudentForm" class="mt-6">
            <input type="hidden" id="edit_student_id" name="student_id">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Student ID -->
                <div>
                    <label for="edit_student_id_field" class="block text-sm font-medium text-gray-700 mb-2">
                        Student ID <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="edit_student_id_field" name="student_id_field"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="e.g., 2024-0001">
                    <p id="edit_student_id_field_error" class="error-message text-red-500 text-xs mt-1"></p>
                </div>

                <!-- Full Name -->
                <div>
                    <label for="edit_full_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="edit_full_name" name="full_name"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="e.g., Juan Dela Cruz">
                    <p id="edit_full_name_error" class="error-message text-red-500 text-xs mt-1"></p>
                </div>

                <!-- Email -->
                <div>
                    <label for="edit_email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <input type="email" id="edit_email" name="email"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="e.g., juan.delacruz@email.com">
                    <p id="edit_email_error" class="error-message text-red-500 text-xs mt-1"></p>
                </div>

                <!-- Grade Level -->
                <div>
                    <label for="edit_grade_level" class="block text-sm font-medium text-gray-700 mb-2">
                        Grade Level <span class="text-red-500">*</span>
                    </label>
                    <select id="edit_grade_level" name="grade_level"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Select Grade Level</option>
                        <option value="7">Grade 7</option>
                        <option value="8">Grade 8</option>
                        <option value="9">Grade 9</option>
                        <option value="10">Grade 10</option>
                    </select>
                    <p id="edit_grade_level_error" class="error-message text-red-500 text-xs mt-1"></p>
                </div>

                <!-- Date of Birth -->
                <div>
                    <label for="edit_date_of_birth" class="block text-sm font-medium text-gray-700 mb-2">
                        Date of Birth <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="edit_date_of_birth" name="date_of_birth"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <p id="edit_date_of_birth_error" class="error-message text-red-500 text-xs mt-1"></p>
                </div>

                <!-- Gender -->
                <div>
                    <label for="edit_gender" class="block text-sm font-medium text-gray-700 mb-2">
                        Gender <span class="text-red-500">*</span>
                    </label>
                    <select id="edit_gender" name="gender"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Select Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                    <p id="edit_gender_error" class="error-message text-red-500 text-xs mt-1"></p>
                </div>

                <!-- Contact Number -->
                <div>
                    <label for="edit_contact_number" class="block text-sm font-medium text-gray-700 mb-2">
                        Contact Number
                    </label>
                    <input type="tel" id="edit_contact_number" name="contact_number"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="e.g., 09123456789">
                    <p id="edit_contact_number_error" class="error-message text-red-500 text-xs mt-1"></p>
                </div>

                <!-- Guardian Name -->
                <div>
                    <label for="edit_guardian_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Guardian Name
                    </label>
                    <input type="text" id="edit_guardian_name" name="guardian_name"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="e.g., Maria Dela Cruz">
                    <p id="edit_guardian_name_error" class="error-message text-red-500 text-xs mt-1"></p>
                </div>

                <!-- Guardian Contact -->
                <div>
                    <label for="edit_guardian_contact" class="block text-sm font-medium text-gray-700 mb-2">
                        Guardian Contact
                    </label>
                    <input type="tel" id="edit_guardian_contact" name="guardian_contact"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="e.g., 09123456789">
                    <p id="edit_guardian_contact_error" class="error-message text-red-500 text-xs mt-1"></p>
                </div>

                <!-- Status -->
                <div>
                    <label for="edit_status" class="block text-sm font-medium text-gray-700 mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select id="edit_status" name="status"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="archived">Archived</option>
                    </select>
                    <p id="edit_status_error" class="error-message text-red-500 text-xs mt-1"></p>
                </div>
            </div>

            <!-- Address -->
            <div class="mt-6">
                <label for="edit_address" class="block text-sm font-medium text-gray-700 mb-2">
                    Address
                </label>
                <textarea id="edit_address" name="address" rows="3"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Complete address"></textarea>
                <p id="edit_address_error" class="error-message text-red-500 text-xs mt-1"></p>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                <button type="button" onclick="closeEditStudentModal()"
                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition">
                    Cancel
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-blue-900 text-white rounded-lg text-sm font-medium hover:bg-blue-800 transition">
                    <i class="fas fa-save mr-2"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>