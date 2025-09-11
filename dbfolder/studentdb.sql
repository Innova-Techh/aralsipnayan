-- =====================================================
-- ARALSIPNAYAN COMPLETE DATABASE SCHEMA
-- Gamified Mathematics Assessment System
-- =====================================================

-- Drop existing tables in reverse dependency order
DROP TABLE IF EXISTS question_cooldowns;
DROP TABLE IF EXISTS question_media;
DROP TABLE IF EXISTS questions;



-- =====================================================
-- 1. QUESTION BANK TABLES
-- =====================================================

CREATE TABLE IF NOT EXISTS questions (
    question_id VARCHAR(50) PRIMARY KEY,
    competency ENUM('number_algebra', 'measurement_geometry', 'data_probability') NOT NULL,
    difficulty_level ENUM('beginner', 'intermediate', 'advanced') NOT NULL,
    topic_tag VARCHAR(100) NOT NULL, -- subcategory like 'polygons', 'fractions', etc.

    -- Question Content
    question_text TEXT NOT NULL,
    question_type ENUM('multiple_choice', 'fill_blanks', 'true_false', 'drag_drop', 'connect_dots') DEFAULT 'multiple_choice',
    choice_a TEXT,
    choice_b TEXT,
    choice_c TEXT,
    choice_d TEXT,
    correct_answer TEXT NOT NULL,
    hint_text TEXT,
    explanation TEXT,
    max_allowed_time INT NOT NULL, -- in seconds (30/45/60 based on difficulty)

    -- Timing and Difficulty
    estimated_difficulty_weight DECIMAL(3,2) DEFAULT 1.0,

    -- Question Metadata
    question_source ENUM('built_in', 'custom') NOT NULL DEFAULT 'built_in',
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    usage_count INT DEFAULT 0,
    success_rate DECIMAL(5,4) DEFAULT 0.0000,
    base_points INT NOT NULL,
    created_by BIGINT UNSIGNED NULL,

    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    -- Foreign Keys
    CONSTRAINT fk_questions_created_by FOREIGN KEY (created_by)
        REFERENCES users(id) ON DELETE SET NULL,

    -- Indexes
    INDEX idx_competency_difficulty (competency, difficulty_level),
    INDEX idx_topic_active (topic_tag, is_active),
    INDEX idx_difficulty_usage (difficulty_level, usage_count)
);



CREATE TABLE question_media (
    media_id VARCHAR(50) PRIMARY KEY,
    question_id VARCHAR(50) NOT NULL,
    media_type ENUM('image', 'video', 'audio') NOT NULL DEFAULT 'image',
    media_url VARCHAR(500) NOT NULL,
    media_description TEXT,
    display_order INT DEFAULT 1, -- for multiple images per question
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (question_id) REFERENCES questions(question_id) ON DELETE CASCADE,
    INDEX idx_question_media (question_id)
);


CREATE TABLE question_cooldowns (
    cooldown_id VARCHAR(50) PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    question_id VARCHAR(50) NOT NULL,
    competency ENUM('number_algebra', 'measurement_geometry', 'data_probability') NOT NULL,
    
    -- Answer details that triggered cooldown
    was_correct BOOLEAN NOT NULL,
    answered_at TIMESTAMP NOT NULL,
    response_time DECIMAL(8,3) NOT NULL, -- seconds with millisecond precision
    
    -- Cooldown settings
    cooldown_duration INT NOT NULL, -- minutes (60 for correct, 30 for incorrect)
    cooldown_until TIMESTAMP NULL, -- calculated: answered_at + cooldown_duration
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES questions(question_id) ON DELETE CASCADE,
    
    -- Composite index for efficient cooldown queries
    INDEX idx_user_competency_cooldown (user_id, competency, is_active, cooldown_until),
    INDEX idx_cooldown_expiry (cooldown_until, is_active),
    
    -- Prevent duplicate active cooldowns for same user-question
    UNIQUE KEY unique_active_cooldown (user_id, question_id, is_active)
);

