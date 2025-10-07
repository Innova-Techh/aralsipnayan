<!-- Retry Modal (initially hidden) -->
<div id="retry-modal" class="hidden fixed inset-0 bg-black bg-opacity-75 items-center justify-center z-50">
    <div class="bg-white rounded-2xl p-8 max-w-md mx-4 text-center">
        <div class="mb-6">
            <div class="text-6xl mb-4">⚠️</div>
            <h3 class="text-2xl font-bold text-gray-800 mb-2">Submission Failed</h3>
            <p class="text-gray-600 mb-4">
                Your answer couldn't be submitted due to a connection error. Don't worry, your progress has been saved locally.
            </p>
            <p class="text-sm text-gray-500">
                Click the button below to reload the page and try again.
            </p>
        </div>
        <button id="reload-page-btn"
                class="bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white px-8 py-3 rounded-full font-semibold transition-all duration-200 transform hover:scale-105 border-b-4 border-[#cc4713] shadow-lg">
            🔄 Reload Page
        </button>
    </div>
</div>
