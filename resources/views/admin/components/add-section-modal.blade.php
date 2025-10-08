<!-- Add Section Modal -->
<div id="addSectionModal"
    class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
    <div class="relative p-5 border w-full max-w-md shadow-lg rounded-lg bg-white mx-4">
        <!-- Modal Header -->
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-semibold text-gray-900">
                <i class="fas fa-book text-blue-600 mr-2"></i>
                Add New Section
            </h3>
            <button onclick="closeAddSectionModal()" class="text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <form id="addSectionForm" action="{{ route('admin.management.sections.store') }}" method="POST"
            onsubmit="return validateSectionForm()">
            @csrf

            <!-- Section Name -->
            <div class="mb-4">
                <label for="section_name" class="block text-sm font-medium text-gray-700 mb-2">
                    Section Name <span class="text-red-500">*</span>
                </label>
                <input type="text" id="section_name" name="section_name"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                    placeholder="e.g., Section A, Section B">
                <p id="section_name_error" class="error-message text-red-500 text-xs mt-1"></p>
            </div>

            <!-- Grade Level -->
            <div class="mb-4">
                <label for="grade_level" class="block text-sm font-medium text-gray-700 mb-2">
                    Grade Level <span class="text-red-500">*</span>
                </label>
                <select id="grade_level" name="grade_level"
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
                    <option value="Grade 9">Grade 9</option>
                    <option value="Grade 10">Grade 10</option>
                </select>
                <p id="grade_level_error" class="error-message text-red-500 text-xs mt-1"></p>
            </div>

            <!-- Enrolled Students -->
            <div class="mb-4">
                <label for="enrolled_students" class="block text-sm font-medium text-gray-700 mb-2">
                    Number of Enrolled Students <span class="text-red-500">*</span>
                </label>
                <input type="number" id="enrolled_students" name="enrolled_students" min="0" max="100"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                    placeholder="Enter number of students">
                <p id="enrolled_students_error" class="error-message text-red-500 text-xs mt-1"></p>
            </div>

            <!-- Assigned Teacher -->
            <div class="mb-6">
                <label for="assigned_teacher" class="block text-sm font-medium text-gray-700 mb-2">
                    Assigned Teacher <span class="text-red-500">*</span>
                </label>
                <select id="assigned_teacher" name="assigned_teacher"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    <option value="">Select teacher</option>
                    <option value="1">Ms. Maria Santos</option>
                    <option value="2">Mr. John Cruz</option>
                    <option value="3">Ms. Ana Reyes</option>
                    <option value="4">Mr. Carlos Lopez</option>
                    <option value="5">Ms. Linda Garcia</option>
                    <option value="6">Mr. Robert Santos</option>
                    <option value="7">Ms. Jennifer Dela Cruz</option>
                    <option value="8">Mr. Michael Tan</option>
                </select>
                <p id="assigned_teacher_error" class="error-message text-red-500 text-xs mt-1"></p>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end gap-3">
                <button type="button" onclick="closeAddSectionModal()"
                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition font-medium">
                    Cancel
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-blue-900 text-white rounded-lg hover:bg-blue-800 transition font-medium">
                    <i class="fas fa-save mr-2"></i>
                    Create Section
                </button>
            </div>
        </form>
    </div>
</div>