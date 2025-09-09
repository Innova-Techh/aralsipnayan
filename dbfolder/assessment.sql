-- =====================================================
-- ASSESSMENT AND DIAGNOSTIC TABLES
-- =====================================================

DROP TABLE IF EXISTS assessment_questions;
DROP TABLE IF EXISTS question_responses;
DROP TABLE IF EXISTS diagnostic_sessions;
DROP TABLE IF EXISTS assessments;


CREATE TABLE assessments (
    assessment_id VARCHAR(50) PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    competency ENUM('number_algebra', 'measurement_geometry', 'data_probability') NOT NULL,
    
    -- Assessment Type and Configuration
    assessment_type ENUM('diagnostic', 'regular') NOT NULL,
    difficulty_level ENUM('beginner', 'intermediate', 'advanced') NOT NULL,
    total_questions INT NOT NULL, -- 15/20/30 based on difficulty
    
    -- Assessment Status
    status ENUM('in_progress', 'completed', 'abandoned') NOT NULL DEFAULT 'in_progress',
    
    -- Diagnostic-specific fields
    is_diagnostic_phase BOOLEAN DEFAULT FALSE, -- true for diagnostic exams
    diagnostic_phase INT, -- 1=beginner, 2=intermediate, 3=advanced (for diagnostic)
    
    -- Scoring and Performance
    correct_answers INT NOT NULL DEFAULT 0,
    incorrect_answers INT NOT NULL DEFAULT 0,
    questions_answered INT NOT NULL DEFAULT 0,
    
    -- Calculated Scores (filled after completion)
    accuracy_percentage DECIMAL(5,2), -- (correct/total) * 100
    bkt_score_before DECIMAL(5,4), -- BKT score before this assessment
    bkt_score_after DECIMAL(5,4), -- BKT score after this assessment
    final_mastery_score DECIMAL(5,2), -- calculated mastery score
    
    -- Time Tracking
    total_time_spent INT DEFAULT 0, -- total seconds spent
    average_response_time DECIMAL(8,3), -- average response time
    time_performance_score DECIMAL(5,4), -- f_time calculated score
    
    -- Session Management
    started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign Keys
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    
    INDEX idx_user_assessments (user_id, started_at DESC),
    INDEX idx_competency_assessments (competency, assessment_type),
    INDEX idx_assessment_status (status, started_at),
    INDEX idx_diagnostic_tracking (user_id, assessment_type, competency)
);


CREATE TABLE diagnostic_sessions (
    session_id VARCHAR(50) PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    competency ENUM('number_algebra', 'measurement_geometry', 'data_probability') NOT NULL,
    
    -- Diagnostic Configuration
    total_phases INT NOT NULL DEFAULT 3, -- beginner, intermediate, advanced
    current_phase INT NOT NULL DEFAULT 1,
    
    -- Phase Results
    phase_1_score DECIMAL(5,4), -- MS1 (beginner phase mastery)
    phase_1_questions INT DEFAULT 15, -- Q1
    phase_2_score DECIMAL(5,4), -- MS2 (intermediate phase mastery) 
    phase_2_questions INT DEFAULT 10, -- Q2
    phase_3_score DECIMAL(5,4), -- MS3 (advanced phase mastery)
    phase_3_questions INT DEFAULT 10, -- Q3
    
    -- Final Diagnostic Results
    final_master_score DECIMAL(5,2), -- ((MS1×Q1)+(MS2×Q2)+(MS3×Q3))/(Q1+Q2+Q3) × 100
    recommended_difficulty ENUM('beginner', 'intermediate', 'advanced'),
    
    -- Session Status
    status ENUM('in_progress', 'completed') NOT NULL DEFAULT 'in_progress',
    started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    
    UNIQUE KEY unique_user_competency_diagnostic (user_id, competency),
    INDEX idx_diagnostic_status (status, started_at),
    INDEX idx_user_diagnostics (user_id, completed_at DESC)
);

CREATE TABLE assessment_questions (
    pool_id VARCHAR(50) PRIMARY KEY,
    assessment_id VARCHAR(50) NOT NULL,
    question_id VARCHAR(50) NOT NULL,
    
    -- Question Order (after Fisher-Yates shuffling)
    question_order INT NOT NULL,
    
    -- Question Status
    is_answered BOOLEAN NOT NULL DEFAULT FALSE,
    is_current BOOLEAN NOT NULL DEFAULT FALSE, -- currently active question
    
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (assessment_id) REFERENCES assessments(assessment_id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES questions(question_id),
    
    UNIQUE KEY unique_assessment_question (assessment_id, question_id),
    INDEX idx_assessment_order (assessment_id, question_order),
    INDEX idx_current_question (assessment_id, is_current)
);

CREATE TABLE question_responses (
    response_id VARCHAR(50) PRIMARY KEY,
    assessment_id VARCHAR(50) NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    question_id VARCHAR(50) NOT NULL,
    
    -- Response Details
    user_answer TEXT, -- user's actual answer
    is_correct BOOLEAN NOT NULL,
    response_time DECIMAL(8,3) NOT NULL, -- seconds with millisecond precision
    
    -- Timing Analysis
    max_allowed_time INT NOT NULL, -- from questions table
    normalized_time DECIMAL(5,4), -- response_time / max_allowed_time
    time_score DECIMAL(3,2), -- 0.0-1.0 based on correctness and speed
    
    -- BKT Calculation Components
    bkt_before DECIMAL(5,4) NOT NULL, -- P(Ln) before this question
    bkt_after DECIMAL(5,4) NOT NULL, -- P(Ln+1) after this question
    
    -- Difficulty and Time Factors
    difficulty_factor DECIMAL(3,2), -- 0.8/1.0/1.2 for beginner/intermediate/advanced
    time_factor DECIMAL(5,4), -- f_time for this specific question
    
    -- Points Earned (for gamification)
    base_points INT NOT NULL DEFAULT 0, -- 3/6/10 based on difficulty
    time_bonus_points INT NOT NULL DEFAULT 0, -- 5/10/15 based on speed (if correct)
    total_points INT NOT NULL DEFAULT 0, -- base + time bonus
    
    answered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (assessment_id) REFERENCES assessments(assessment_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES questions(question_id),
    
    INDEX idx_assessment_responses (assessment_id, answered_at),
    INDEX idx_user_responses (user_id, answered_at DESC),
    INDEX idx_question_performance (question_id, is_correct),
    INDEX idx_bkt_tracking (user_id, answered_at)
);