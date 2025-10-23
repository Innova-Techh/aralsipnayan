<!-- Archive/Activate Admin Modal -->
<div id="archiveAdminModal"
    class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6 border border-gray-200">
        <!-- Header -->
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <div id="archiveAdminIcon" class="bg-yellow-100 rounded-full p-2">
                    <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
                </div>
                <h3 id="archiveAdminTitle" class="text-lg font-semibold text-gray-900">Archive Admin</h3>
            </div>
            <button onclick="closeModal('archiveAdminModal')" 
                class="text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <!-- Body -->
        <div>
            <p id="archiveAdminMessage" class="text-sm text-gray-700 mb-4">
                Are you sure you want to archive 
                <span id="archiveAdminName" class="font-semibold text-gray-900"></span>?
            </p>

            <div id="archiveAdminInfo"
                class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4 text-xs text-yellow-800">
                <div class="flex items-start gap-3">
                    <i class="fas fa-info-circle text-yellow-600 mt-0.5"></i>
                    <div>
                        <p class="font-medium mb-1">This action will:</p>
                        <ul id="archiveAdminActions" class="list-disc list-inside space-y-1">
                            <li>Set the admin status to <b>Archived</b></li>
                            <li>Prevent login access</li>
                            <li>Can be reactivated anytime</li>
                        </ul>
                    </div>
                </div>
            </div>

            <form id="archiveAdminForm">
                <input type="hidden" id="archiveAdminId" name="admin_id">
                <input type="hidden" id="archiveAdminCurrentStatus" name="current_status">

                <!-- Footer -->
                <div class="flex justify-end gap-3 pt-3 border-t border-gray-200">
                    <button type="button" onclick="closeModal('archiveAdminModal')"
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition">
                        Cancel
                    </button>
                    <button type="submit" id="archiveAdminButton"
                        class="px-4 py-2 bg-yellow-600 text-white rounded-lg text-sm font-medium hover:bg-yellow-700 transition">
                        <i class="fas fa-archive mr-2"></i> Archive Admin
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
