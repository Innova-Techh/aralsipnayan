<!-- Edit Teacher Modal -->
<div id="editTeacherModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-6 border w-full max-w-2xl shadow-lg rounded-lg bg-white">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-xl font-semibold text-gray-900">Edit Teacher</h3>
            <button onclick="closeEditTeacherModal()" class="text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <form id="editTeacherForm" class="space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" id="editTeacherId" name="teacher_id">

            <!-- Name Fields -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="editFirstname" class="block text-sm font-medium text-gray-700 mb-1">
                        First Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="editFirstname" name="firstname" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p id="editFirstname_error" class="error-message text-red-500 text-xs mt-1"></p>
                </div>

                <div>
                    <label for="editLastname" class="block text-sm font-medium text-gray-700 mb-1">
                        Last Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="editLastname" name="lastname" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p id="editLastname_error" class="error-message text-red-500 text-xs mt-1"></p>
                </div>
            </div>

            <!-- Username and Email -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="editUsername" class="block text-sm font-medium text-gray-700 mb-1">
                        Username <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="editUsername" name="username" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p id="editUsername_error" class="error-message text-red-500 text-xs mt-1"></p>
                </div>

                <div>
                    <label for="editEmail" class="block text-sm font-medium text-gray-700 mb-1">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" id="editEmail" name="email" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p id="editEmail_error" class="error-message text-red-500 text-xs mt-1"></p>
                </div>
            </div>

            <!-- Password -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="editPassword" class="block text-sm font-medium text-gray-700 mb-1">
                        Password (Leave blank to keep current)
                    </label>
                    <input type="password" id="editPassword" name="password"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p id="editPassword_error" class="error-message text-red-500 text-xs mt-1"></p>
                </div>
            </div>

            <!-- Sections - Improved Structure -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Assign Sections
                    <span class="text-xs text-gray-500 font-normal ml-2">(Select sections this teacher will handle)</span>
                </label>
                
                <!-- Container for sections with better layout -->
                <div class="border border-gray-300 rounded-lg p-4 max-h-48 overflow-y-auto bg-gray-50">
                    <div id="editTeacherSections" class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        <!-- Loading state -->
                        <div class="col-span-full text-center py-4 text-gray-500 text-sm" id="editSectionsLoading">
                            <i class="fas fa-spinner fa-spin mr-2"></i>
                            Loading sections...
                        </div>
                    </div>
                    
                    <!-- Empty state -->
                    <div id="editSectionsEmpty" class="hidden text-center py-4 text-gray-500 text-sm">
                        <i class="fas fa-info-circle mr-2"></i>
                        No sections available
                    </div>
                </div>
                
                <!-- Selected sections display -->
                <div id="editSelectedSectionsDisplay" class="mt-2 hidden">
                    <p class="text-xs text-gray-600 mb-1">Selected sections:</p>
                    <div id="editSelectedSectionsList" class="flex flex-wrap gap-1"></div>
                </div>
            </div>

             <!-- Information Note -->
             <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-info-circle text-blue-600 mt-1"></i>
                        <div class="text-sm text-blue-800">
                            <p class="font-medium mb-1">Note:</p>
                            <ul class="list-disc list-inside space-y-1 text-xs">
                                <li>Changing the email, username, and password will update the teacher's login credentials</li>
                            </ul>
                        </div>
                    </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end gap-3 mt-6 pt-4 border-t">
                <button type="button" onclick="closeEditTeacherModal()"
                    class="px-5 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                    Cancel
                </button>
                <button type="submit"
                    class="px-5 py-2 bg-blue-900 text-white rounded-lg hover:bg-blue-800 transition font-medium">
                    <i class="fas fa-save mr-2"></i>
                    Update Teacher
                </button>
            </div>
        </form>
    </div>
</div>