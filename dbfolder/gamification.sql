-- =====================================================
-- GAMIFICATION TABLES
-- =====================================================
DROP TABLE IF EXISTS daily_assessments;
DROP TABLE IF EXISTS points_transactions;
DROP TABLE IF EXISTS user_progress;
DROP TABLE IF EXISTS rank_config;
DROP TABLE IF EXISTS level_config;


CREATE TABLE level_config (
    level INT PRIMARY KEY,
    points_required INT NOT NULL, -- cumulative points needed to reach this level
    points_for_this_level INT NOT NULL DEFAULT 60, -- points needed from previous level
    level_category ENUM('beginner', 'intermediate', 'advanced', 'master') NOT NULL,
    
    INDEX idx_points_required (points_required)
);

CREATE TABLE rank_config (
    rank_id INT PRIMARY KEY AUTO_INCREMENT,
    rank_name VARCHAR(50) NOT NULL UNIQUE,
    required_level INT NOT NULL,
    rank_description TEXT,
    rank_insignia_url VARCHAR(500), -- URL to rank insignia image
    
    INDEX idx_required_level (required_level)
);

CREATE TABLE user_progress (
    user_id BIGINT UNSIGNED PRIMARY KEY,
    
    -- Total Points and Level
    total_points INT NOT NULL DEFAULT 0,
    current_level INT NOT NULL DEFAULT 1,
    points_in_current_level INT NOT NULL DEFAULT 0, -- progress within current level (0-59)
    
    -- Current Rank
    current_rank VARCHAR(50) NOT NULL DEFAULT 'Math Explorer',
    rank_level_threshold INT NOT NULL DEFAULT 10, -- level when rank was achieved
    
    -- Daily Streak
    current_streak INT NOT NULL DEFAULT 0,
    longest_streak INT NOT NULL DEFAULT 0,
    last_assessment_date DATE,
    
    -- Competency Completion Tracking (for daily bonus)
    daily_competencies_completed SET('number_algebra', 'measurement_geometry', 'data_probability') DEFAULT '',
    last_daily_reset DATE,
    
    -- Metadata
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    
    INDEX idx_total_points (total_points DESC),
    INDEX idx_current_level (current_level DESC),
    INDEX idx_streak (current_streak DESC)
);

CREATE TABLE points_transactions (
    transaction_id VARCHAR(50) PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    
    -- Points Details
    points_earned INT NOT NULL,
    points_type ENUM(
        'base_question', 'time_bonus', 'competency_completion', 
        'perfect_assessment', 'daily_triple_completion', 'daily_streak'
    ) NOT NULL,
    
    -- Context Information
    question_id VARCHAR(50), -- for base_question and time_bonus
    assessment_id VARCHAR(50), -- for completion bonuses
    competency ENUM('number_algebra', 'measurement_geometry', 'data_probability'),
    difficulty_level ENUM('beginner', 'intermediate', 'advanced'),
    
    -- Additional Details
    description TEXT, -- human-readable description
    metadata JSON, -- flexible field for additional context
    
    earned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_points (user_id, earned_at DESC),
    INDEX idx_points_type (points_type),
    INDEX idx_competency_points (competency, earned_at DESC)
);

CREATE TABLE daily_assessments (
    record_id VARCHAR(50) PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    assessment_date DATE NOT NULL,
    
    -- Daily Progress
    assessments_completed INT NOT NULL DEFAULT 0,
    competencies_completed SET('number_algebra', 'measurement_geometry', 'data_probability') DEFAULT '',
    
    -- Points earned today
    points_earned_today INT NOT NULL DEFAULT 0,
    
    -- Streak tracking
    contributes_to_streak BOOLEAN NOT NULL DEFAULT FALSE, -- true if >= 1 assessment completed
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    
    UNIQUE KEY unique_user_date (user_id, assessment_date),
    INDEX idx_assessment_date (assessment_date),
    INDEX idx_streak_tracking (user_id, assessment_date DESC)
);


-- Populate level configuration (100 levels, 60 points each)
INSERT INTO level_config (level, points_required, points_for_this_level) VALUES
(1, 0, 0),
(2, 60, 60),
(3, 120, 60),
(4, 180, 60),
(5, 240, 60),
(6, 300, 60),
(7, 360, 60),
(8, 420, 60),
(9, 480, 60),
(10, 540, 60),
(11, 600, 60),
(12, 660, 60),
(13, 720, 60),
(14, 780, 60),
(15, 840, 60),
(16, 900, 60),
(17, 960, 60),
(18, 1020, 60),
(19, 1080, 60),
(20, 1140, 60),
(21, 1200, 60),
(22, 1260, 60),
(23, 1320, 60),
(24, 1380, 60),
(25, 1440, 60),
(26, 1500, 60),
(27, 1560, 60),
(28, 1620, 60),
(29, 1680, 60),
(30, 1740, 60),
(31, 1800, 60),
(32, 1860, 60),
(33, 1920, 60),
(34, 1980, 60),
(35, 2040, 60),
(36, 2100, 60),
(37, 2160, 60),
(38, 2220, 60),
(39, 2280, 60),
(40, 2340, 60),
(41, 2400, 60),
(42, 2460, 60),
(43, 2520, 60),
(44, 2580, 60),
(45, 2640, 60),
(46, 2700, 60),
(47, 2760, 60),
(48, 2820, 60),
(49, 2880, 60),
(50, 2940, 60),
(51, 3000, 60),
(52, 3060, 60),
(53, 3120, 60),
(54, 3180, 60),
(55, 3240, 60),
(56, 3300, 60),
(57, 3360, 60),
(58, 3420, 60),
(59, 3480, 60),
(60, 3540, 60),
(61, 3600, 60),
(62, 3660, 60),
(63, 3720, 60),
(64, 3780, 60),
(65, 3840, 60),
(66, 3900, 60),
(67, 3960, 60),
(68, 4020, 60),
(69, 4080, 60),
(70, 4140, 60),
(71, 4200, 60),
(72, 4260, 60),
(73, 4320, 60),
(74, 4380, 60),
(75, 4440, 60),
(76, 4500, 60),
(77, 4560, 60),
(78, 4620, 60),
(79, 4680, 60),
(80, 4740, 60),
(81, 4800, 60),
(82, 4860, 60),
(83, 4920, 60),
(84, 4980, 60),
(85, 5040, 60),
(86, 5100, 60),
(87, 5160, 60),
(88, 5220, 60),
(89, 5280, 60),
(90, 5340, 60),
(91, 5400, 60),
(92, 5460, 60),
(93, 5520, 60),
(94, 5580, 60),
(95, 5640, 60),
(96, 5700, 60),
(97, 5760, 60),
(98, 5820, 60),
(99, 5880, 60),
(100, 5940, 60);


-- Populate rank configuration
INSERT INTO rank_config (rank_name, required_level, rank_description) VALUES
('Math Explorer', 10, 'Starting the journey'),
('Math Adventurer', 20, 'Gaining confidence and exploring new challenges'),
('Math Seeker', 30, 'Developing problem-solving skills and curiosity'),
('Math Strategist', 40, 'Learning to think critically and apply strategies'),
('Math Innovator', 50, 'Solving problems creatively and independently'),
('Math Prodigy', 60, 'Recognized for impressive math mastery and speed'),
('Math Virtuoso', 70, 'Demonstrating exceptional mathematical skills'),
('Math Sage', 80, 'Reaching a higher understanding of concepts and patterns'),
('Math Champion', 90, 'Competing at an elite level and mastering challenges'),
('Math Grandmaster', 100, 'The ultimate achievement');