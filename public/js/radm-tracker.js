/**
 * RADM (Random Answering Detection Model) Tracker
 * 
 * Tracks question response timing and coordinates with backend RADM service
 * to detect and intervene on random answering behavior.
 * 
 * Usage:
 * const radmTracker = new RadmTracker({
 *     sessionId: 'session-123',
 *     studentId: 123,
 *     difficultyLevel: 'intermediate'
 * });
 * 
 * // When question is displayed
 * radmTracker.startQuestion(questionId);
 * 
 * // When answer is submitted
 * radmTracker.endQuestion(questionId, isCorrect, bktProbability);
 */

class RadmTracker {
    constructor(config) {
        this.sessionId = config.sessionId;
        this.studentId = config.studentId;
        this.difficultyLevel = config.difficultyLevel || 'intermediate';
        this.apiEndpoint = config.apiEndpoint || '/api/radm/evaluate';
        
        this.currentQuestionId = null;
        this.startTime = null;
        
        // Storage key for persisting state across page loads
        this.storageKey = `radm_state_${this.sessionId}`;
        
        // Load persisted state from localStorage
        this.loadState();
        
        console.log('[RADM] Tracker initialized', {
            sessionId: this.sessionId,
            studentId: this.studentId,
            difficultyLevel: this.difficultyLevel,
            questionCount: this.questionCount,
            windowSize: this.responseWindow.length
        });
    }

    /**
     * Load state from localStorage
     */
    loadState() {
        try {
            const saved = localStorage.getItem(this.storageKey);
            if (saved) {
                const state = JSON.parse(saved);
                this.questionCount = state.questionCount || 0;
                this.responseWindow = state.responseWindow || [];
                console.log('[RADM] Loaded state from localStorage', {
                    questionCount: this.questionCount,
                    windowSize: this.responseWindow.length
                });
            } else {
                this.questionCount = 0;
                this.responseWindow = [];
            }
        } catch (error) {
            console.error('[RADM] Failed to load state:', error);
            this.questionCount = 0;
            this.responseWindow = [];
        }
    }

    /**
     * Save state to localStorage
     */
    saveState() {
        try {
            const state = {
                questionCount: this.questionCount,
                responseWindow: this.responseWindow
            };
            localStorage.setItem(this.storageKey, JSON.stringify(state));
            console.log('[RADM] Saved state to localStorage', state);
        } catch (error) {
            console.error('[RADM] Failed to save state:', error);
        }
    }

    /**
     * Clear persisted state (call when assessment is complete)
     */
    clearState() {
        try {
            localStorage.removeItem(this.storageKey);
            console.log('[RADM] Cleared state from localStorage');
        } catch (error) {
            console.error('[RADM] Failed to clear state:', error);
        }
    }

    /**
     * Start tracking time for a question
     */
    startQuestion(questionId) {
        this.currentQuestionId = questionId;
        this.startTime = Date.now();
        
        console.log('[RADM] Question started', {
            questionId: questionId,
            timestamp: new Date().toISOString()
        });
    }

    /**
     * End tracking and record response
     * 
     * @param {number|string} questionId - The question identifier
     * @param {boolean} isCorrect - Whether the answer was correct
     * @param {number} bktProbability - BKT probability after this response (0-1)
     * @returns {Promise} Resolves when evaluation is complete
     */
    async endQuestion(questionId, isCorrect, bktProbability = null) {
        if (!this.startTime) {
            console.warn('[RADM] No start time recorded for question', questionId);
            return;
        }

        const endTime = Date.now();
        const timeTaken = Math.round((endTime - this.startTime) / 1000); // Convert to seconds

        const response = {
            questionId: questionId,
            timeTaken: timeTaken,
            isCorrect: isCorrect,
            bktProbability: bktProbability,
            timestamp: new Date().toISOString()
        };

        // Add to window
        this.responseWindow.push(response);
        this.questionCount++;

        // Save state to localStorage
        this.saveState();

        console.log('[RADM] Question ended', {
            questionId: questionId,
            timeTaken: timeTaken,
            isCorrect: isCorrect,
            windowSize: this.responseWindow.length,
            totalQuestions: this.questionCount
        });

        // Keep only last 5 responses
        if (this.responseWindow.length > 5) {
            this.responseWindow.shift();
            this.saveState();
        }

        // Evaluate if we have exactly 5 responses and it's a multiple of 5
        console.log('[RADM] Checking evaluation condition:', {
            questionCount: this.questionCount,
            modulo5: this.questionCount % 5,
            windowLength: this.responseWindow.length,
            shouldEvaluate: this.questionCount % 5 === 0 && this.responseWindow.length === 5
        });
        
        if (this.questionCount % 5 === 0 && this.responseWindow.length === 5) {
            console.log('[RADM] Window complete - evaluating', this.responseWindow);
            await this.evaluate();
        } else {
            console.log('[RADM] Not evaluating yet - need', 5 - (this.questionCount % 5), 'more questions');
        }

        // Reset for next question
        this.currentQuestionId = null;
        this.startTime = null;
    }

    /**
     * Evaluate current window with backend RADM service
     */
    async evaluate() {
        try {
            const payload = {
                sessionId: this.sessionId,
                studentId: this.studentId,
                difficultyLevel: this.difficultyLevel,
                responses: this.responseWindow.map(r => ({
                    question_id: r.questionId,
                    time_taken: r.timeTaken,
                    is_correct: r.isCorrect,
                    bkt_probability: r.bktProbability
                }))
            };

            console.log('[RADM] Sending evaluation request', payload);
            console.log('[RADM] API Endpoint:', this.apiEndpoint);

            const response = await fetch(this.apiEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            if (!response.ok) {
                const errorText = await response.text();
                console.error('[RADM] API error response:', errorText);
                throw new Error(`RADM API error: ${response.status} - ${errorText}`);
            }

            const result = await response.json();

            console.log('[RADM] Evaluation result', result);

            // If intervention is triggered, show modal
            if (result.data && result.data.intervention_triggered) {
                console.log('[RADM] Intervention triggered! Showing modal...');
                this.showIntervention(result.data.detection_id);
            } else {
                console.log('[RADM] No intervention triggered', {
                    rai_score: result.data?.rai_score,
                    threshold: 0.60
                });
            }

            return result;

        } catch (error) {
            console.error('[RADM] Evaluation failed', error);
            console.error('[RADM] Error stack:', error.stack);
            // Don't throw - let assessment continue even if RADM fails
        }
    }

    /**
     * Show intervention modal using Livewire
     */
    showIntervention(detectionId) {
        console.log('[RADM] Triggering intervention modal', detectionId);
        console.log('[RADM] Livewire available:', typeof window.Livewire !== 'undefined');
        
        // Dispatch Livewire event to show modal
        if (window.Livewire) {
            console.log('[RADM] Dispatching Livewire event: showRadmIntervention');
            window.Livewire.dispatch('showRadmIntervention', { detectionId: detectionId });
            console.log('[RADM] Livewire event dispatched');
        } else {
            console.error('[RADM] Livewire not available - cannot show intervention modal');
            console.log('[RADM] Available window properties:', Object.keys(window));
        }
    }

    /**
     * Reset tracker (e.g., when starting a new session)
     */
    reset() {
        this.currentQuestionId = null;
        this.startTime = null;
        this.questionCount = 0;
        this.responseWindow = [];
        
        console.log('[RADM] Tracker reset');
    }

    /**
     * Get current statistics
     */
    getStats() {
        return {
            sessionId: this.sessionId,
            studentId: this.studentId,
            questionCount: this.questionCount,
            windowSize: this.responseWindow.length,
            currentQuestion: this.currentQuestionId
        };
    }
}

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = RadmTracker;
}

// Also make available globally
if (typeof window !== 'undefined') {
    window.RadmTracker = RadmTracker;
}
