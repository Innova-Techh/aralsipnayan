-- =====================================================
-- BADGE AND TROPHY TABLES
-- =====================================================
DROP TABLE IF EXISTS trophy_winners;
DROP TABLE IF EXISTS trophy_periods;
DROP TABLE IF EXISTS trophies;
DROP TABLE IF EXISTS user_badges;
DROP TABLE IF EXISTS badge_config;

CREATE TABLE badge_config (
    badge_id VARCHAR(50) PRIMARY KEY,
    badge_name VARCHAR(100) NOT NULL,
    badge_description TEXT,
    badge_type ENUM('milestone', 'achievement', 'special') NOT NULL,
    
    -- Badge Requirements
    points_required INT, -- for point-based badges
    assessments_required INT, -- for assessment completion badges
    special_condition VARCHAR(200), -- for complex requirements
    
    badge_icon_url VARCHAR(500),
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE user_badges (
    user_badge_id VARCHAR(50) PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    badge_id VARCHAR(50) NOT NULL,
    
    earned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    notification_sent BOOLEAN DEFAULT FALSE,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (badge_id) REFERENCES badge_config(badge_id),
    
    UNIQUE KEY unique_user_badge (user_id, badge_id),
    INDEX idx_user_badges (user_id, earned_at DESC),
    INDEX idx_badge_earnings (badge_id, earned_at DESC)
);

CREATE TABLE trophies (
    trophy_id VARCHAR(50) PRIMARY KEY,
    trophy_name VARCHAR(100) NOT NULL,
    trophy_type ENUM('class_weekly', 'school_monthly') NOT NULL,
    trophy_description TEXT,
    
    -- Trophy Configuration
    max_winners INT NOT NULL, -- 10 for class, 20 for school
    reset_frequency ENUM('weekly', 'monthly') NOT NULL,
    
    trophy_icon_url VARCHAR(500),
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE trophy_periods (
    period_id VARCHAR(50) PRIMARY KEY,
    trophy_id VARCHAR(50) NOT NULL,
    
    -- Period Definition
    period_start TIMESTAMP NOT NULL,
    period_end TIMESTAMP DEFAULT NULL,
    period_type ENUM('weekly', 'monthly') NOT NULL,
    
    -- Status
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    winners_calculated BOOLEAN NOT NULL DEFAULT FALSE,
    
    -- Reference Information
    class_id VARCHAR(50), -- for class trophies
    school_id VARCHAR(50), -- for school trophies
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (trophy_id) REFERENCES trophies(trophy_id),
    INDEX idx_active_periods (is_active, period_end),
    INDEX idx_trophy_periods (trophy_id, period_start DESC)
);

CREATE TABLE trophy_winners (
    winner_id VARCHAR(50) PRIMARY KEY,
    period_id VARCHAR(50) NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    
    -- Winner Details
    final_position INT NOT NULL, -- 1st, 2nd, 3rd, etc.
    points_earned_in_period INT NOT NULL,
    
    -- Trophy Award Details
    awarded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    notification_sent BOOLEAN DEFAULT FALSE,
    
    FOREIGN KEY (period_id) REFERENCES trophy_periods(period_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    
    UNIQUE KEY unique_period_user (period_id, user_id),
    INDEX idx_period_rankings (period_id, final_position),
    INDEX idx_user_trophies (user_id, awarded_at DESC)
);

-- Populate milestone badges
INSERT INTO badge_config VALUES
('first_steps', 'First Steps', 'Complete your first assessment (any competency)', 'milestone', 0, 1, 'first_assessment', NULL, TRUE, NOW()),
('quick_learner', 'Quick Learner', 'Earn 50 total points', 'milestone', 50, NULL, NULL, NULL, TRUE, NOW()),
('on_fire', 'On Fire', 'Earn 150 total points', 'milestone', 150, NULL, NULL, NULL, TRUE, NOW()),
('math_explorer_badge', 'Math Explorer', 'Earn 300 total points', 'milestone', 300, NULL, NULL, NULL, TRUE, NOW()),
('math_whiz', 'Math Whiz', 'Earn 500 total points', 'milestone', 500, NULL, NULL, NULL, TRUE, NOW()),
('grade_champion', 'Grade Champion', 'Earn 750 total points', 'milestone', 750, NULL, NULL, NULL, TRUE, NOW());

-- Populate trophy configuration
INSERT INTO trophies VALUES
('class_weekly_leader', 'Class Leader Board Trophy', 'class_weekly', 'Top 10 students in class by total points earned each week', 10, 'weekly', NULL, TRUE, NOW()),
('school_monthly_leader', 'School Leaderboard Trophy', 'school_monthly', 'Top 20 students in school by total points earned each month', 20, 'monthly', NULL, TRUE, NOW());
