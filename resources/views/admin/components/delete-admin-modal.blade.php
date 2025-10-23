<!-- Delete Admin Modal -->
<div id="deleteAdminModal"
    class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
    <div class="relative p-6 w-full max-w-md bg-white rounded-lg shadow-lg">
        <!-- Header -->
        <div class="flex items-center justify-between border-b pb-3">
            <div class="flex items-center gap-3">
                <div id="deleteAdminIcon" class="bg-red-100 rounded-full p-2">
                    <i class="fas fa-trash text-red-600 text-xl"></i>
                </div>
                <h3 id="deleteAdminTitle" class="text-lg font-semibold text-gray-900">Delete Admin</h3>
            </div>
            <button type="button" onclick="closeDeleteAdminModal()" class="text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Body -->
        <div class="mt-4">
            <p id="deleteAdminMessage" class="text-sm text-gray-600 mb-4">
                Are you sure you want to permanently delete <b id="deleteAdminName" class="text-gray-900"></b>?
            </p>

            <div id="deleteAdminInfo" class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                <div class="flex items-start gap-3">
                    <i class="fas fa-info-circle text-red-600 mt-0.5"></i>
                    <div class="text-xs text-red-800">
                        <p class="font-medium mb-1">This action will:</p>
                        <ul id="deleteAdminActions" class="list-disc list-inside space-y-1">
                            <li>Completely remove this admin from the system</li>
                            <li>Delete their profile and login credentials</li>
                            <li><strong>This action cannot be undone</strong></li>
                        </ul>
                    </div>
                </div>
            </div>

            <form id="deleteAdminForm">
                <input type="hidden" id="deleteAdminId" name="admin_id">

                <!-- Footer -->
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                    <button type="button" onclick="closeDeleteAdminModal()"
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition text-sm font-medium">
                        Cancel
                    </button>
                    <button type="submit" id="deleteAdminButton"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm font-medium">
                        <i class="fas fa-trash mr-2"></i> Delete Admin
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>