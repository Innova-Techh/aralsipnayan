<div>
    @if($show)
    <div class="fixed inset-0 z-50 overflow-y-auto font-baloo" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Background overlay -->
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-black bg-opacity-75" aria-hidden="true"
                 wire:click="closeModal"></div>

            <!-- Modal panel -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <!-- Header -->
                <div class="bg-gradient-to-r from-orange-500 to-orange-600 p-6 text-center shadow-lg border-b-4 border-[#cc4713] relative">
                    <div class="absolute inset-0 bg-gradient-to-b from-white/20 to-transparent pointer-events-none"></div>
                    {{-- <div class="text-6xl mb-3 relative z-10">⏸️</div> --}}
                    <h3 class="text-2xl sm:text-3xl font-extrabold text-white relative z-10" id="modal-title" 
                        style="text-shadow: -1px -1px 0 #7A4305, 1px -1px 0 #7A4305, -1px 1px 0 #7A4305, 1px 1px 0 #7A4305, 0 0 1px #7A4305;">
                        Take a Moment! ⏸️
                    </h3>
                </div>

                <!-- Content -->
                <div class="px-6 py-6 bg-[#FFF7E6]">
                    <div class="space-y-4">
                        {{-- <p class="text-base sm:text-lg text-gray-800 font-medium text-center">
                            We've noticed a pattern in your recent responses. Let's ensure you're taking the time to engage thoughtfully with each question! ✨
                        </p> --}}

                        @if(!empty($this->getMessages()))
                        <div class="bg-gradient-to-r from-blue-400 to-blue-500 rounded-xl p-4 mb-4 shadow-lg border-b-4 border-blue-700">
                            <div class="flex items-start gap-3">
                                <div class="text-3xl flex-shrink-0">👀</div>
                                <div class="flex-1">
                                    <p class="text-white font-bold mb-2 text-base sm:text-lg">
                                        What we noticed:
                                    </p>
                                    <ul class="text-white space-y-2 text-sm sm:text-base">
                                        @foreach($this->getMessages() as $message)
                                        <li class="flex items-start gap-2">
                                            <span class="flex-shrink-0">•</span>
                                            <span>{{ $message }}</span>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="bg-gradient-to-r from-green-400 to-green-500 rounded-xl p-4 shadow-lg border-b-4 border-green-700">
                            <div class="flex items-start gap-3">
                                <div class="text-3xl flex-shrink-0">💡</div>
                                <div class="flex-1">
                                    <p class="text-white font-bold mb-2 text-base sm:text-lg">
                                        Tips for Success:
                                    </p>
                                    <ul class="text-white space-y-2 text-sm sm:text-base">
                                        <li class="flex items-start gap-2">
                                            <span class="flex-shrink-0">📖</span>
                                            <span>Read each question carefully before answering</span>
                                        </li>
                                        <li class="flex items-start gap-2">
                                            <span class="flex-shrink-0">⏰</span>
                                            <span>Take your time to think through your response</span>
                                        </li>
                                        <li class="flex items-start gap-2">
                                            <span class="flex-shrink-0">🔍</span>
                                            <span>Review all answer options before selecting</span>
                                        </li>
                                        <li class="flex items-start gap-2">
                                            <span class="flex-shrink-0">🎯</span>
                                            <span>Stay focused and avoid distractions</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-xl p-4 shadow-lg border-b-4 border-red-800">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 9v4"></path><path d="M12 17h.01"></path><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path></svg></div>
                                <div class="flex-1">
                                    <p class="text-white font-bold text-base sm:text-lg">
                                        Because of this, you are deducted with 15 points.
                                    </p>
                                </div>
                            </div>
                        </div>{{-- 
                        <div class="bg-gradient-to-r from-purple-400 to-purple-500 rounded-xl p-4 text-center shadow-lg border-b-4 border-purple-700">
                            <p class="text-white font-bold text-base sm:text-lg">
                                🌟 Remember: This assessment is designed to help you learn. Take your time and do your best! 🌟
                            </p>
                        </div> --}}
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-6 py-4 bg-[#FFF7E6] flex justify-center">
                    <button type="button" wire:click="closeModal"
                            class="bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white px-8 py-3 sm:px-10 sm:py-4 rounded-full font-bold text-base sm:text-lg transition-all duration-200 transform hover:scale-105 border-b-4 border-[#cc4713] shadow-lg">
                        I Understand - Let's Continue!
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>



