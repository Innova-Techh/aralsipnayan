<!-- Add Teacher Modal -->
<div id="addTeacherModal"
    class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
    <div class="relative p-5 border w-full max-w-md shadow-lg rounded-lg bg-white mx-4">
        <!-- Modal Header -->
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-semibold text-gray-900">
                <i class="fas fa-chalkboard-teacher text-blue-600 mr-2"></i>
                Add New Teacher
            </h3>
            <button onclick="closeAddTeacherModal()" class="text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <form id="addTeacherForm" action="{{ route('admin.management.teachers.store') }}" method="POST"
            onsubmit="return validateTeacherForm()">
            @csrf

            <!-- Full Name -->
            <div class="mb-4">
                <label for="teacher_fullname" class="block text-sm font-medium text-gray-700 mb-2">
                    Full Name <span class="text-red-500">*</span>
                </label>
                <input type="text" id="teacher_fullname" name="fullname"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                    placeholder="Enter full name">
                <p id="teacher_fullname_error" class="error-message text-red-500 text-xs mt-1"></p>
            </div>

            <!-- Username -->
            <div class="mb-4">
                <label for="teacher_username" class="block text-sm font-medium text-gray-700 mb-2">
                    Username <span class="text-red-500">*</span>
                </label>
                <input type="text" id="teacher_username" name="username"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                    placeholder="Enter username">
                <p id="teacher_username_error" class="error-message text-red-500 text-xs mt-1"></p>
            </div>

            <!-- Email -->
            <div class="mb-4">
                <label for="teacher_email" class="block text-sm font-medium text-gray-700 mb-2">
                    Email Address <span class="text-red-500">*</span>
                </label>
                <input type="email" id="teacher_email" name="email"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                    placeholder="Enter email address">
                <p id="teacher_email_error" class="error-message text-red-500 text-xs mt-1"></p>
            </div>

            <!-- Grade Level Focus -->
            <div class="mb-4">
                <label for="grade_level_focus" class="block text-sm font-medium text-gray-700 mb-2">
                    Grade Assigned <span class="text-red-500">*</span>
                </label>
                <select id="grade_level_focus" name="grade_level_focus"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    <option value="">Select grade level</option>
                    <option value="Grade 1">Grade 1</option>
                    <option value="Grade 2">Grade 2</option>
                    <option value="Grade 3">Grade 3</option>
                    <option value="Grade 4">Grade 4</option>
                    <option value="Grade 5">Grade 5</option>
                    <option value="Grade 6">Grade 6</option>
                    <option value="Grade 7">Grade 7</option>
                    <option value="Grade 8">Grade 8</option>
                </select>
                <p id="grade_level_focus_error" class="error-message text-red-500 text-xs mt-1"></p>
            </div>

            <!-- Sections (Multiple Select) -->
            <div class="mb-4">
                <label for="sections" class="block text-sm font-medium text-gray-700 mb-2">
                    Sections
                </label>
                <select id="sections" name="sections[]" multiple
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                    size="4">
                    <option value="A">Section A</option>
                    <option value="B">Section B</option>
                    <option value="C">Section C</option>
                    <option value="D">Section D</option>
                    <option value="E">Section E</option>
                </select>
                <p class="text-xs text-gray-500 mt-1">Hold Ctrl (Cmd on Mac) to select multiple sections</p>
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label for="teacher_password" class="block text-sm font-medium text-gray-700 mb-2">
                    Password <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="password" id="teacher_password" name="password"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        placeholder="Enter password (min.placeholder=" Enter password (min. 8 characters)">
                    <button type="button" onclick="togglePassword('teacher_password')"
                        class="absolute right-3 top-2.5 text-gray-500 hover:text-gray-700">
                        <i class="fas fa-eye" id="teacher_password-icon"></i>
                    </button>
                </div>
                <p id="teacher_password_error" class="error-message text-red-500 text-xs mt-1"></p>
            </div>

            <!-- Re-type Password -->
            <div class="mb-6">
                <label for="teacher_password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                    Re-type Password <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="password" id="teacher_password_confirmation" name="password_confirmation"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                        placeholder="Re-enter password">
                    <button type="button" onclick="togglePassword('teacher_password_confirmation')"
                        class="absolute right-3 top-2.5 text-gray-500 hover:text-gray-700">
                        <i class="fas fa-eye" id="teacher_password_confirmation-icon"></i>
                    </button>
                </div>
                <p id="teacher_password_confirmation_error" class="error-message text-red-500 text-xs mt-1"></p>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end gap-3">
                <button type="button" onclick="closeAddTeacherModal()"
                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition font-medium">
                    Cancel
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                    <i class="fas fa-save mr-2"></i>
                    Create Teacher
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