-- =====================================================
-- MASTERY AND BKT TRACKING TABLES
-- =====================================================

DROP TABLE IF EXISTS bkt_parameters;
DROP TABLE IF EXISTS mastery_thresholds;
DROP TABLE IF EXISTS student_mastery;



CREATE TABLE IF NOT EXISTS student_mastery (
    mastery_id VARCHAR(50) PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    competency ENUM('number_algebra', 'measurement_geometry', 'data_probability') NOT NULL,
    
    -- Current Difficulty Level (determined by mastery score)
    current_difficulty ENUM('beginner', 'intermediate', 'advanced') NOT NULL DEFAULT 'beginner',
    
    -- Mastery Score Components (0.0 to 1.0 scale)
    accuracy_score DECIMAL(5,4) NOT NULL DEFAULT 0.0000, -- ACC component (55% weight)
    bkt_score DECIMAL(5,4) NOT NULL DEFAULT 0.5000, -- BKT component (45% weight) - starts at 0.5
    
    -- Final Mastery Score (0-100 scale)
    final_mastery_score DECIMAL(5,2) NOT NULL DEFAULT 22.50, -- (0.55 × 0) + (0.45 × 0.5) × 100
    
    -- BKT Parameters
    prior_knowledge DECIMAL(5,4) DEFAULT 0.1000, -- P(L0) - initial knowledge probability
    learn_rate DECIMAL(5,4) DEFAULT 0.3000, -- P(T) - transition probability  
    slip_rate DECIMAL(5,4) DEFAULT 0.1000, -- P(S) - slip probability
    guess_rate DECIMAL(5,4) DEFAULT 0.2500, -- P(G) - guess probability
    
    -- Diagnostic Status
    has_taken_diagnostic BOOLEAN NOT NULL DEFAULT FALSE,
    diagnostic_completed_at TIMESTAMP NULL,
    
    -- Performance Tracking
    total_questions_answered INT NOT NULL DEFAULT 0,
    correct_answers INT NOT NULL DEFAULT 0,
    total_assessments_taken INT NOT NULL DEFAULT 0,
    
    -- Time Performance (for f_time calculation)
    cumulative_time_score DECIMAL(8,4) NOT NULL DEFAULT 0.0000, -- sum of all time scores
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    
    UNIQUE KEY unique_user_competency (user_id, competency),
    INDEX idx_user_mastery (user_id),
    INDEX idx_competency_difficulty (competency, current_difficulty),
    INDEX idx_mastery_score (final_mastery_score DESC)
);


CREATE TABLE IF NOT EXISTS mastery_thresholds (
    threshold_id INT PRIMARY KEY AUTO_INCREMENT,
    difficulty_level ENUM('beginner', 'intermediate', 'advanced') NOT NULL,
    
    -- Threshold Ranges (based on proficiency levels)
    min_score DECIMAL(5,2) NOT NULL, -- minimum mastery score for this level
    max_score DECIMAL(5,2) NOT NULL, -- maximum mastery score for this level
    
    -- Progression Rules
    promotion_threshold DECIMAL(5,2), -- score needed to move up
    demotion_threshold DECIMAL(5,2), -- score that triggers move down
    
    -- Assessment Configuration
    questions_per_assessment INT NOT NULL, -- 15/20/30
    
    -- Proficiency Description
    proficiency_description VARCHAR(100),
    
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    
    UNIQUE KEY unique_difficulty (difficulty_level)
);

-- 8. BKT Parameters Configuration Table
CREATE TABLE IF NOT EXISTS bkt_parameters (
    config_id INT PRIMARY KEY AUTO_INCREMENT,
    competency ENUM('number_algebra', 'measurement_geometry', 'data_probability') NOT NULL,
    difficulty_level ENUM('beginner', 'intermediate', 'advanced') NOT NULL,
    
    -- Standard BKT Parameters
    initial_knowledge DECIMAL(5,4) DEFAULT 0.1000, -- P(L0)
    learn_rate DECIMAL(5,4) DEFAULT 0.3000, -- P(T)
    slip_rate DECIMAL(5,4) DEFAULT 0.1000, -- P(S)
    guess_rate DECIMAL(5,4) DEFAULT 0.2500, -- P(G)
    
    -- Difficulty Factors
    difficulty_weight DECIMAL(3,2) NOT NULL, -- 0.8/1.0/1.2
    
    -- Time Scoring Configuration
    fast_threshold DECIMAL(3,2) DEFAULT 0.50, -- ≤0.5 normalized time = fast
    medium_threshold DECIMAL(3,2) DEFAULT 0.80, -- ≤0.8 normalized time = medium
    
    -- Time Score Values
    fast_correct_score DECIMAL(3,2) DEFAULT 1.00,
    medium_correct_score DECIMAL(3,2) DEFAULT 0.80,
    slow_correct_score DECIMAL(3,2) DEFAULT 0.60,
    fast_incorrect_score DECIMAL(3,2) DEFAULT 0.20,
    slow_incorrect_score DECIMAL(3,2) DEFAULT 0.10,
    timeout_score DECIMAL(3,2) DEFAULT 0.00,
    
    -- Mastery Score Weights
    accuracy_weight DECIMAL(3,2) DEFAULT 0.55, -- 55%
    bkt_weight DECIMAL(3,2) DEFAULT 0.45, -- 45%
    
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_competency_difficulty (competency, difficulty_level)
);

-- Populate mastery thresholds
INSERT INTO mastery_thresholds VALUES
(1, 'beginner', 0.00, 49.99, 50.00, 0.00, 15, 'Not Proficient + Low Proficient', TRUE),
(2, 'intermediate', 50.00, 74.99, 75.00, 49.99, 20, 'Nearly Proficient', TRUE),
(3, 'advanced', 75.00, 100.00, 100.00, 74.99, 30, 'Proficient + Highly Proficient', TRUE);

-- Populate BKT parameters for all competencies and difficulties
INSERT INTO bkt_parameters (competency, difficulty_level, difficulty_weight) VALUES
('number_algebra', 'beginner', 0.8),
('number_algebra', 'intermediate', 1.0),
('number_algebra', 'advanced', 1.2),
('measurement_geometry', 'beginner', 0.8),
('measurement_geometry', 'intermediate', 1.0),
('measurement_geometry', 'advanced', 1.2),
('data_probability', 'beginner', 0.8),
('data_probability', 'intermediate', 1.0),
('data_probability', 'advanced', 1.2);