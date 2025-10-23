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
                    <div class="mb-4">
                        <label for="addStudentFirstName" class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                        <input type="text" id="addStudentFirstName" name="firstname" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div class="mb-4">
                        <label for="addStudentMiddleName" class="block text-sm font-medium text-gray-700 mb-2">Middle Name (Optional)</label>
                        <input type="text" id="addStudentMiddleName" name="middlename" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="mb-4">
                        <label for="addStudentLastName" class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                        <input type="text" id="addStudentLastName" name="lastname" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div class="mb-4">
                        <label for="addStudentEmail" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" id="addStudentEmail" name="email" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>

                    <!-- Gender -->
                    <div>
                        <label for="addStudentGender" class="block text-sm font-medium text-gray-700 mb-2">
                            Gender <span class="text-red-500">*</span>
                        </label>
                        <select id="addStudentGender" name="gender"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Select Gender</option>
                            <option value="male">male</option>
                            <option value="female">female</option>
                            <option value="other">other</option>
                        </select>
                        <p id="addGender_error" class="error-message text-red-500 text-xs mt-1"></p>
                    </div>
            </div>

            <!-- Info Box -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-4">
                <div class="flex items-start gap-3">
                    <i class="fas fa-info-circle text-blue-600 mt-0.5"></i>
                    <div class="text-sm text-blue-800">
                        <p class="font-medium mb-1">Default Credentials</p>
                        <ul class="list-disc list-inside space-y-1 text-xs">
                            <li>Username will be auto-generated based on section</li>
                            <li>Default password: <strong>123</strong></li>
                            <li>Student ID (LRN) will be auto-generated</li>
                        </ul>
                    </div>
                </div>
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