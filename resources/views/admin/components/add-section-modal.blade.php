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
        <form id="addSectionForm" action="{{ route('admin.management.sections.store') }}" method="POST">
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

            <!-- Assigned Teacher -->
            <div class="mb-6">
                <label for="assigned_teacher" class="block text-sm font-medium text-gray-700 mb-2">
                    Assign Teacher <span class="text-red-500">*</span>
                </label>
                <select id="assigned_teacher" name="assigned_teacher"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                    <option value="">Select teacher</option>
                    <!-- Teachers will be loaded dynamically via JavaScript -->
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

<script>
// Load available teachers when modal opens
function loadAvailableTeachers() {
    fetch('{{ route("admin.management.sections.teachers") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const select = document.getElementById('assigned_teacher');
                // Clear existing options except the first one
                select.innerHTML = '<option value="">Select teacher</option>';
                
                // Add teachers to select
                data.teachers.forEach(teacher => {
                    const option = document.createElement('option');
                    option.value = teacher.id;
                    option.textContent = `${teacher.firstname} ${teacher.lastname}`;
                    select.appendChild(option);
                });
            }
        })
        .catch(error => {
            console.error('Error loading teachers:', error);
        });
}

// Load teachers when modal is opened
document.addEventListener('DOMContentLoaded', function() {
    // Load teachers when the modal is shown
    const modal = document.getElementById('addSectionModal');
    if (modal) {
        // Use MutationObserver to detect when modal becomes visible
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                    const target = mutation.target;
                    if (!target.classList.contains('hidden')) {
                        loadAvailableTeachers();
                    }
                }
            });
        });
        
        observer.observe(modal, { attributes: true });
    }
});
</script>