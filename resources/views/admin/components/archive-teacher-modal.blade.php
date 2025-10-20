<!-- Archive/Activate Teacher Modal -->
<div id="archiveTeacherModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-1/4 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
        <!-- Modal Header -->
        <div class="flex items-center justify-between pb-4 border-b border-gray-200">
            <div class="flex items-center gap-3">
                <div id="archiveTeacherIcon" class="bg-yellow-100 rounded-full p-2">
                    <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
                </div>
                <h3 id="archiveTeacherTitle" class="text-xl font-semibold text-gray-900">Inactivate Teacher</h3>
            </div>
            <button onclick="closeArchiveTeacherModal()" class="text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="mt-4">
            <p id="archiveTeacherMessage" class="text-sm text-gray-600 mb-4">
                Are you sure you want to Inactivate <span id="archiveTeacherName"
                    class="font-semibold text-gray-900"></span>?
            </p>
            <div id="archiveTeacherInfo" class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                <div class="flex items-start gap-3">
                    <i class="fas fa-info-circle text-yellow-600 mt-0.5"></i>
                    <div class="text-xs text-yellow-800">
                        <p class="font-medium mb-1">This action will:</p>
                        <ul id="archiveTeacherActions" class="list-disc list-inside space-y-1">
                            <li>Set the teacher status to "Inactive"</li>
                            <li>Remove them from active teacher lists</li>
                            <li>Preserve all their data and records</li>
                            <li>Can be reversed by reactivating the teacher</li>
                        </ul>
                    </div>
                </div>
            </div>

            <form id="archiveTeacherForm">
                <input type="hidden" id="archiveTeacherId" name="teacher_id">
                <input type="hidden" id="archiveTeacherCurrentStatus" name="current_status">

                <!-- Modal Footer -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                    <button type="button" onclick="closeArchiveTeacherModal()"
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition">
                        Cancel
                    </button>
                    <button type="submit" id="archiveTeacherButton"
                        class="px-4 py-2 bg-yellow-600 text-white rounded-lg text-sm font-medium hover:bg-yellow-700 transition">
                        <i class="fas fa-archive mr-2"></i> Archive Teacher
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>