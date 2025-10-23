<!-- Edit Student Modal -->
<div id="editStudentModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-lg bg-white">
        <!-- Modal Header -->
        <div class="flex items-center justify-between pb-4 border-b border-gray-200">
            <div class="flex items-center gap-3">
                <div class="bg-blue-100 rounded-full p-2">
                    <i class="fas fa-user-edit text-blue-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-semibold text-gray-900">Edit Student</h3>
                    <p class="text-sm text-gray-600 mt-1">Update student information</p>
                </div>
            </div>
            <button onclick="closeEditStudentModal()" class="text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <form id="editStudentForm" class="mt-6">
            <input type="hidden" id="editStudentId" name="student_id">
            
            <div class="space-y-4">
                <!-- Student LRN -->
                <div>
                    <label for="editStudentLRN" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-id-card text-gray-400 mr-2"></i>Student LRN
                    </label>
                    <input type="text" id="editStudentLRN" name="student_id" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="e.g., LRN123456">
                </div>

                <!-- Name Fields in Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- First Name -->
                    <div>
                        <label for="editFirstname" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user text-gray-400 mr-2"></i>First Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="editFirstname" name="firstname" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Juan">
                    </div>

                    <!-- Middle Name -->
                    <div>
                        <label for="editMiddlename" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user text-gray-400 mr-2"></i>Middle Name
                        </label>
                        <input type="text" id="editMiddlename" name="middlename"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Santos">
                    </div>

                    <!-- Last Name -->
                    <div>
                        <label for="editLastname" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user text-gray-400 mr-2"></i>Last Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="editLastname" name="lastname" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Dela Cruz">
                    </div>
                </div>

                <!-- Email -->
                <div>
                    <label for="editEmail" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-envelope text-gray-400 mr-2"></i>Email Address <span class="text-red-500">*</span>
                    </label>
                    <input type="email" id="editEmail" name="email" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="student@example.com">
                </div>

                <!-- Grade Level and Status -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                   <!-- Gender -->
                   <div>
                        <label for="editStudentGender" class="block text-sm font-medium text-gray-700 mb-2">
                            Gender <span class="text-red-500">*</span>
                        </label>
                        <select id="editStudentGender" name="gender"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Select Gender</option>
                            <option value="male">male</option>
                            <option value="female">female</option>
                            <option value="other">other</option>
                        </select>
                        <p id="editGender_error" class="error-message text-red-500 text-xs mt-1"></p>
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="editStatus" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-toggle-on text-gray-400 mr-2"></i>Status <span class="text-red-500">*</span>
                        </label>
                        <select id="editStatus" name="status" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        <p id="editStatus_error" class="error-message text-red-500 text-xs mt-1"></p>
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <label for="editPassword" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-key text-gray-400 mr-2"></i>Password <span class="text-red-500"></span>(Leave blank to keep current)
                    </label>
                    <input type="password" id="editPassword" name="password"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="*******">
                        <p id="editPassword_error" class="error-message text-red-500 text-xs mt-1"></p>
                </div>

                <!-- Information Note -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-info-circle text-blue-600 mt-1"></i>
                        <div class="text-sm text-blue-800">
                            <p class="font-medium mb-1">Note:</p>
                            <ul class="list-disc list-inside space-y-1 text-xs">
                                <li>Changing the email and password will update the student's login credentials</li>
                                <li>Setting status to "Inactive" will prevent the student from logging in</li>
                                <li>Setting status to "Archive" will remove the student from their section</li>
                                <li>Can all be reverted by setting status to "Active"</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                <button type="button" onclick="closeEditStudentModal()"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-300 transition">
                    <i class="fas fa-times mr-2"></i>Cancel
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-blue-900 text-white rounded-lg text-sm font-medium hover:bg-blue-800 transition">
                    <i class="fas fa-save mr-2"></i>Update Student
                </button>
            </div>
        </form>
    </div>
</div>