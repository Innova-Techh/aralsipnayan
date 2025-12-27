<!-- Active Quiz Blocker Modal -->
<div id="activeQuizBlockerModal"
    class="fixed inset-0 bg-black bg-opacity-60 justify-center items-center z-[60] hidden px-4 sm:px-0">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-[0_20px_40px_rgba(0,0,0,0.4)] overflow-hidden transform scale-95 transition-all duration-300"
        id="activeQuizBlockerModalContent">
        <!-- Header with 3D effects -->
        <div
            class="bg-gradient-to-r from-[#FF6B6B] to-[#FF8E8E] p-4 sm:p-6 relative shadow-[0_8px_16px_rgba(0,0,0,0.3)] border-b-4 border-[#e74c3c]">
            <div class="absolute inset-0 bg-gradient-to-b from-white/30 to-transparent pointer-events-none"></div>
            <div class="flex items-center justify-center gap-2 sm:gap-3 relative z-10">
                <div
                    class="w-10 h-10 sm:w-12 sm:h-12 bg-white/20 rounded-full flex items-center justify-center shadow-[0_4px_8px_rgba(0,0,0,0.2)]">
                    <svg class="w-6 h-6 sm:w-8 sm:h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <h2 class="text-lg sm:text-xl font-bold text-white drop-shadow-lg">Diagnostic Quiz In Progress</h2>
            </div>
        </div>

        <!-- Content -->
        <div class="p-4 sm:p-6 text-center">
            <div class="mb-4 sm:mb-6">
                <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-2 sm:mb-3">Cannot Start New Assessment</h3>
                <p class="text-sm sm:text-base text-gray-600 leading-relaxed">
                    You already have an active quiz in progress. Please complete the ongoing diagnostic
                    quiz before starting a new one.
                </p>
            </div>

            <!-- Active Quiz Info -->
            <div
                class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-3 sm:p-4 mb-4 sm:mb-6 border-l-4 border-blue-400 shadow-inner">
                <div class="text-xs sm:text-sm text-gray-700">
                    <div class="font-semibold text-blue-800 mb-2">Active Quiz:</div>
                    <div id="activeQuizInfo" class="space-y-1 text-left">Loading...</div>
                </div>
            </div>

            <!-- Action Buttons with 3D effects -->
            <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                <!-- Resume Button -->
                {{-- <button onclick="resumeActiveQuiz()"
                    class="flex-1 bg-gradient-to-b from-[#4CAF50] to-[#45a049] text-white font-semibold py-2.5 sm:py-3 px-3 sm:px-4 rounded-xl border-b-4 border-[#3d8b40] shadow-[0_6px_12px_rgba(0,0,0,0.2)] hover:scale-[1.02] hover:shadow-[0_8px_16px_rgba(0,0,0,0.3)] active:scale-[0.98] active:shadow-[0_4px_8px_rgba(0,0,0,0.2)] transition-all duration-200 relative overflow-hidden text-sm sm:text-base">
                    <div class="absolute inset-0 bg-gradient-to-b from-white/20 to-transparent pointer-events-none">
                    </div>
                    <span class="relative z-10">Resume Quiz</span>
                </button> --}}

                <!-- Cancel Button -->
                <button onclick="closeActiveQuizBlockerModal()"
                    class="flex-1 bg-gradient-to-b from-[#6C757D] to-[#5a6268] text-white font-semibold py-2.5 sm:py-3 px-3 sm:px-4 rounded-xl border-b-4 border-[#4e555b] shadow-[0_6px_12px_rgba(0,0,0,0.2)] hover:scale-[1.02] hover:shadow-[0_8px_16px_rgba(0,0,0,0.3)] active:scale-[0.98] active:shadow-[0_4px_8px_rgba(0,0,0,0.2)] transition-all duration-200 relative overflow-hidden text-sm sm:text-base">
                    <div class="absolute inset-0 bg-gradient-to-b from-white/20 to-transparent pointer-events-none">
                    </div>
                    <span class="relative z-10">Okay</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let activeQuizData = null;

    function showActiveQuizBlockerModal(quizData) {
        const modal = document.getElementById('activeQuizBlockerModal');
        const infoDiv = document.getElementById('activeQuizInfo');
        
        activeQuizData = quizData;

        if (quizData && infoDiv) {
            let quizTypeLabel = quizData.type === 'diagnostic' ? 'Diagnostic Test' : 'Regular Assessment';
            let progressText = `${quizData.progress}/${quizData.total_questions} questions`;
            
            // Calculate remaining time
            const startTime = new Date(quizData.started_at);
            const timeLimitMinutes = quizData.time_limit || 30;
            const timeLimitMs = timeLimitMinutes * 60 * 1000;
            const elapsedMs = Date.now() - startTime.getTime();
            const remainingMs = Math.max(0, timeLimitMs - elapsedMs);
            const remainingMinutes = Math.floor(remainingMs / 60000);
            const remainingSeconds = Math.floor((remainingMs % 60000) / 1000);

            let timeDisplay;
            if (remainingMs <= 0) {
                timeDisplay = '<span class="text-red-600 font-bold">⏰ Time Expired</span>';
            } else if (remainingMinutes > 0) {
                timeDisplay = `<span class="text-orange-600 font-semibold">⏱️ ${remainingMinutes}m ${remainingSeconds}s remaining</span>`;
            } else {
                timeDisplay = `<span class="text-red-600 font-semibold">⏱️ ${remainingSeconds}s remaining</span>`;
            }

            infoDiv.innerHTML = `
                <div><strong>Type:</strong> ${quizTypeLabel}</div>
                <div><strong>Quiz:</strong> ${quizData.title || quizData.competency || 'Assessment'}</div>
                ${quizData.phase_name ? `<div><strong>Phase:</strong> ${quizData.phase_name}</div>` : ''}
                <div><strong>Progress:</strong> ${progressText}</div>
                {{-- <div>${timeDisplay}</div> --}}
                <div class="text-xs text-gray-500 mt-1">Started: ${new Date(quizData.started_at).toLocaleString()}</div>
            `;
        }

        // Show modal with animation
        modal.classList.remove('hidden');
        modal.style.display = 'flex';
        setTimeout(() => {
            const content = document.getElementById('activeQuizBlockerModalContent');
            if (content) {
                content.style.transform = 'scale(1)';
            }
        }, 10);
    }

    function closeActiveQuizBlockerModal() {
        const modal = document.getElementById('activeQuizBlockerModal');
        const content = document.getElementById('activeQuizBlockerModalContent');
        
        if (content) {
            content.style.transform = 'scale(0.95)';
        }
        
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.style.display = 'none';
            activeQuizData = null;
        }, 300);
    }

    function resumeActiveQuiz() {
        if (!activeQuizData) return;

        if (activeQuizData.type === 'diagnostic') {
            // Resume diagnostic - redirect to diagnostic quiz page
            const competency = activeQuizData.competency;
            window.location.href = `/student/quiz/${competency}`;
        } else {
            // Resume regular assessment - use existing resume functionality
            const assessmentId = activeQuizData.assessment_id;
            const category = '{{ $category ?? "" }}';
            
            // Show loading state
            const resumeBtn = event.target;
            const originalText = resumeBtn.innerHTML;
            resumeBtn.disabled = true;
            resumeBtn.innerHTML = '<span class="relative z-10">Resuming...</span>';

            fetch('{{ route("student.quiz.resume-assessment") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    assessment_id: assessmentId,
                    category: category
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = data.redirect;
                } else {
                    alert(data.message || 'Failed to resume assessment. Please try again.');
                    resumeBtn.disabled = false;
                    resumeBtn.innerHTML = originalText;
                }
            })
            .catch(error => {
                console.error('Error resuming assessment:', error);
                alert('Failed to resume assessment. Please try again.');
                resumeBtn.disabled = false;
                resumeBtn.innerHTML = originalText;
            });
        }
    }

    // Close modal when clicking outside
    document.getElementById('activeQuizBlockerModal')?.addEventListener('click', function (e) {
        if (e.target === this) {
            closeActiveQuizBlockerModal();
        }
    });
</script>
