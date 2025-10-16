<!-- Archive Student Modal -->
<div id="archiveStudentModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-1/4 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
        <!-- Modal Header -->
        <div class="flex items-center justify-between pb-4 border-b border-gray-200">
            <div class="flex items-center gap-3">
                <div class="bg-yellow-100 rounded-full p-2">
                    <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900">Archive Student</h3>
            </div>
            <button onclick="closeArchiveStudentModal()" class="text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="mt-4">
            <p class="text-sm text-gray-600 mb-4">
                Are you sure you want to archive <span id="archiveStudentName"
                    class="font-semibold text-gray-900"></span>?
            </p>
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                <div class="flex items-start gap-3">
                    <i class="fas fa-info-circle text-yellow-600 mt-0.5"></i>
                    <div class="text-xs text-yellow-800">
                        <p class="font-medium mb-1">This action will:</p>
                        <ul class="list-disc list-inside space-y-1">
                            <li>Set the student status to "Archived"</li>
                            <li>Remove them from active class lists</li>
                            <li>Preserve all their data and records</li>
                            <li>Can be reversed by reactivating the student</li>
                        </ul>
                    </div>
                </div>
            </div>

            <form id="archiveStudentForm">
                <input type="hidden" id="archiveStudentId" name="student_id">

                <!-- Reason for archiving (optional) -->
                <div class="mb-4">
                    <label for="archive_reason" class="block text-sm font-medium text-gray-700 mb-2">
                        Reason for Archiving (Optional)
                    </label>
                    <textarea id="archive_reason" name="reason" rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                        placeholder="e.g., Transferred to another school, Graduated, etc."></textarea>
                </div>

                <!-- Modal Footer -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                    <button type="button" onclick="closeArchiveStudentModal()"
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-yellow-600 text-white rounded-lg text-sm font-medium hover:bg-yellow-700 transition">
                        <i class="fas fa-archive mr-2"></i> Archive Student
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>