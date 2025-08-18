-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 10, 2025 at 10:17 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `aralsipnayandb`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('QrNRwfgnZ1BB8fnzKTRtBbwDz58Txs7eSuJlyQbe', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 OPR/119.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicjBFdElrRFZKUmRpb0ZSamdIUUh0ODJWMzVFNVNJVzBlZUJmdVFnSCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1749543401);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) UNIQUE NOT NULL,
    `email` VARCHAR(100) UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('Student', 'Teacher', 'Admin') NOT NULL,
    `last_login_date` DATETIME NULL,
    `status` ENUM('active', 'inactive', 'archive') DEFAULT 'active',
    `remember_token` VARCHAR(100),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);


--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;



CREATE TABLE student_profile (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL UNIQUE,
    firstname VARCHAR(100) NOT NULL,
    lastname VARCHAR(100) NOT NULL,
    section VARCHAR(50),
    grade_level VARCHAR(10) DEFAULT '6',
    school_name VARCHAR(150) DEFAULT 'Pembo Elementary School',
    avatar_url VARCHAR(255) DEFAULT '/avatars/default.png',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_student_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE teacher_profile (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL UNIQUE,
    firstname VARCHAR(100) NOT NULL,
    lastname VARCHAR(100) NOT NULL,
    grade_level_focus VARCHAR(10) DEFAULT '6',
    school_name VARCHAR(150) DEFAULT 'Pembo Elementary School',
    profile_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_teacher_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE admin_profile (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL UNIQUE,
    firstname VARCHAR(100) NOT NULL,
    lastname VARCHAR(100) NOT NULL,
    grade_level_focus VARCHAR(10) DEFAULT '6',
    school_name VARCHAR(150) DEFAULT 'Pembo Elementary School',
    profile_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_admin_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Main questions table with all attributes
CREATE TABLE questions (
    question_id VARCHAR(20) PRIMARY KEY,  -- Format: NA-B-001, MG-I-025, DP-A-010
    competency ENUM('Number_Algebra', 'Measurement_Geometry', 'Data_Probability') NOT NULL,
    difficulty_level ENUM('Beginner', 'Intermediate', 'Advanced') NOT NULL,
    question_type ENUM('Multiple_Choice', 'Fill_Blanks', 'True_False', 'Drag_Drop', 'Connect_Dots') DEFAULT 'Multiple_Choice',
    question_text TEXT NOT NULL,
    
    -- Multiple choice options (NULL for other question types)
    choice_a VARCHAR(255) NULL,
    choice_b VARCHAR(255) NULL,
    choice_c VARCHAR(255) NULL,
    choice_d VARCHAR(255) NULL,
    
    -- Flexible answer storage
    correct_answer TEXT NOT NULL,  -- For multiple choice: 'A', for fill blanks: actual answer, etc.
    
    -- Additional attributes
    hint_text TEXT NULL,
    explanation TEXT NULL,
    topic_tag VARCHAR(100),
    points INT DEFAULT NULL,  -- NULL means use default point system
    
    -- Question source
    created_by ENUM('System', 'Teacher') DEFAULT 'System',
    created_by_user_id BIGINT UNSIGNED NULL,  -- Teacher who created it
    is_active BOOLEAN DEFAULT TRUE,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indexes
    INDEX idx_competency_difficulty (competency, difficulty_level),
    INDEX idx_question_type (question_type),
    INDEX idx_active_questions (is_active, competency, difficulty_level),
    
    CONSTRAINT fk_question_creator FOREIGN KEY (created_by_user_id) REFERENCES users(id) ON DELETE SET NULL
);

- Individual competency mastery tracking
CREATE TABLE student_competency_mastery (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    competency ENUM('Number_Algebra', 'Measurement_Geometry', 'Data_Probability') NOT NULL,
    current_difficulty_level ENUM('Beginner', 'Intermediate', 'Advanced') DEFAULT 'Beginner',
    mastery_probability DECIMAL(4,3) DEFAULT 0.300,  -- BKT probability (0.000-1.000)
    
    -- Performance statistics
    total_questions_answered INT DEFAULT 0,
    correct_answers INT DEFAULT 0,
    accuracy_rate DECIMAL(4,3) GENERATED ALWAYS AS (
        CASE WHEN total_questions_answered > 0 
        THEN correct_answers / total_questions_answered 
        ELSE 0 END
    ) STORED,
    
    -- BKT parameters (can be adjusted per student)
    p_learn DECIMAL(4,3) DEFAULT 0.100,    -- Probability of learning
    p_guess DECIMAL(4,3) DEFAULT 0.250,    -- Probability of guessing correctly
    p_slip DECIMAL(4,3) DEFAULT 0.100,     -- Probability of slip
    
    -- Diagnostic status
    diagnostic_completed BOOLEAN DEFAULT FALSE,
    diagnostic_date DATETIME NULL,
    
    last_assessment_date DATETIME NULL,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_user_competency (user_id, competency),
    CONSTRAINT fk_mastery_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
-- Assessment templates (both built-in and custom)
CREATE TABLE assessments (
    assessment_id VARCHAR(30) PRIMARY KEY,  -- Format: NA-B-BUILTIN-001, MG-CUSTOM-001
    assessment_name VARCHAR(200) NOT NULL,
    assessment_type ENUM('Diagnostic', 'Adaptive_Builtin', 'Custom_Teacher') NOT NULL,
    competency ENUM('Number_Algebra', 'Measurement_Geometry', 'Data_Probability', 'Mixed') NOT NULL,
    difficulty_level ENUM('Beginner', 'Intermediate', 'Advanced', 'Mixed') DEFAULT 'Mixed',
    
    -- Assessment settings
    time_limit_minutes INT NULL,  -- NULL means no time limit
    total_questions INT NOT NULL,
    total_points INT DEFAULT 0,
    
    -- Custom assessment settings
    created_by_user_id BIGINT UNSIGNED NULL,  -- Teacher who created it
    deadline DATETIME NULL,
    is_active BOOLEAN DEFAULT TRUE,
    
    -- Question selection method for built-in assessments
    is_adaptive BOOLEAN DEFAULT TRUE,  -- Uses BKT + Fisher-Yates
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_assessment_type (assessment_type, competency),
    INDEX idx_active_assessments (is_active, competency),
    
    CONSTRAINT fk_assessment_creator FOREIGN KEY (created_by_user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ================================
-- ASSESSMENT SESSIONS & RESPONSES
-- ================================

-- Individual student assessment sessions
CREATE TABLE assessment_sessions (
    session_id VARCHAR(40) PRIMARY KEY,  -- Format: STU001-NA-B-20250814-001
    user_id BIGINT UNSIGNED NOT NULL,
    assessment_id VARCHAR(30) NULL,  -- NULL for adaptive assessments
    session_type ENUM('Diagnostic', 'Adaptive', 'Custom', 'Practice') NOT NULL,
    competency ENUM('Number_Algebra', 'Measurement_Geometry', 'Data_Probability', 'Mixed') NOT NULL,
    difficulty_level ENUM('Beginner', 'Intermediate', 'Advanced', 'Mixed') NOT NULL,
    
    -- Session data
    questions_json JSON NOT NULL,  -- Array of selected question IDs in order
    total_questions INT NOT NULL,
    
    -- Timing
    start_time DATETIME DEFAULT CURRENT_TIMESTAMP,
    end_time DATETIME NULL,
    time_limit_minutes INT NULL,
    
    -- Results
    questions_answered INT DEFAULT 0,
    correct_answers INT DEFAULT 0,
    total_points_earned INT DEFAULT 0,
    accuracy_rate DECIMAL(4,3) DEFAULT 0.000,
    
    -- Status
    status ENUM('in_progress', 'completed', 'time_expired', 'abandoned') DEFAULT 'in_progress',
    
    -- BKT tracking
    initial_mastery_probability DECIMAL(4,3) NULL,
    final_mastery_probability DECIMAL(4,3) NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_user_sessions (user_id, status),
    INDEX idx_session_competency (competency, session_type),
    
    CONSTRAINT fk_session_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_session_assessment FOREIGN KEY (assessment_id) REFERENCES assessments(assessment_id) ON DELETE SET NULL
);

-- Individual question responses within sessions
CREATE TABLE question_responses (
    response_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(40) NOT NULL,
    question_id VARCHAR(20) NOT NULL,
    question_order INT NOT NULL,
    
    -- Response data
    student_answer TEXT NOT NULL,  -- Student's actual answer
    is_correct BOOLEAN NOT NULL,
    points_earned INT DEFAULT 0,
    response_time_seconds INT NULL,
    
    -- BKT tracking (real-time updates)
    mastery_before DECIMAL(4,3) NULL,  -- Mastery probability before answering
    mastery_after DECIMAL(4,3) NULL,   -- Mastery probability after answering
    
    -- Hints used
    hint_used BOOLEAN DEFAULT FALSE,
    hint_penalty_applied BOOLEAN DEFAULT FALSE,
    
    answered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_session_order (session_id, question_order),
    INDEX idx_question_performance (question_id, is_correct),
    INDEX idx_user_responses (session_id, answered_at),
    
    CONSTRAINT fk_response_session FOREIGN KEY (session_id) REFERENCES assessment_sessions(session_id) ON DELETE CASCADE,
    CONSTRAINT fk_response_question FOREIGN KEY (question_id) REFERENCES questions(question_id) ON DELETE CASCADE
);

-- ================================
-- RULE-BASED ALGORITHM: QUESTION COOLDOWNS
-- ================================

CREATE TABLE question_cooldowns (
    cooldown_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    question_id VARCHAR(20) NOT NULL,
    competency ENUM('Number_Algebra', 'Measurement_Geometry', 'Data_Probability') NOT NULL,
    
    -- Cooldown details
    cooldown_until DATETIME NOT NULL,
    cooldown_reason ENUM('correct_answer', 'wrong_answer') NOT NULL,
    cooldown_hours INT NOT NULL,  -- 24 for correct, 4 for wrong
    
    -- Context
    session_id VARCHAR(40) NOT NULL,  -- Which session triggered this cooldown
    is_active BOOLEAN DEFAULT TRUE,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_user_question_active (user_id, question_id, is_active),
    INDEX idx_user_competency_cooldown (user_id, competency, cooldown_until),
    INDEX idx_active_cooldowns (is_active, cooldown_until),
    
    CONSTRAINT fk_cooldown_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_cooldown_question FOREIGN KEY (question_id) REFERENCES questions(question_id) ON DELETE CASCADE,
    CONSTRAINT fk_cooldown_session FOREIGN KEY (session_id) REFERENCES assessment_sessions(session_id) ON DELETE CASCADE
);

-- ================================
-- GAMIFICATION SYSTEM
-- ================================

-- Student points and leveling
CREATE TABLE student_gamification (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL UNIQUE,
    
    -- Points system
    total_points INT DEFAULT 0,
    current_level INT DEFAULT 1,
    points_to_next_level INT DEFAULT 35,
    
    -- Daily/Weekly tracking
    daily_points INT DEFAULT 0,
    weekly_points INT DEFAULT 0,
    monthly_points INT DEFAULT 0,
    
    -- Streaks
    current_streak INT DEFAULT 0,
    longest_streak INT DEFAULT 0,
    last_activity_date DATE NULL,
    
    -- Assessment tracking
    total_assessments_completed INT DEFAULT 0,
    perfect_assessments_count INT DEFAULT 0,
    
    -- Competency-specific stats
    na_assessments_completed INT DEFAULT 0,  -- Number & Algebra
    mg_assessments_completed INT DEFAULT 0,  -- Measurement & Geometry  
    dp_assessments_completed INT DEFAULT 0,  -- Data & Probability
    
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_gamification_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Point transactions log
CREATE TABLE point_transactions (
    transaction_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    session_id VARCHAR(40) NULL,  -- NULL for non-assessment bonuses
    
    -- Transaction details
    points_earned INT NOT NULL,
    transaction_type ENUM(
        'question_correct', 
        'assessment_completion', 
        'perfect_assessment', 
        'daily_bonus', 
        'competency_bonus',
        'streak_bonus'
    ) NOT NULL,
    
    -- Context
    competency ENUM('Number_Algebra', 'Measurement_Geometry', 'Data_Probability') NULL,
    difficulty_level ENUM('Beginner', 'Intermediate', 'Advanced') NULL,
    description TEXT NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_user_transactions (user_id, created_at),
    INDEX idx_transaction_type (transaction_type, competency),
    
    CONSTRAINT fk_transaction_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_transaction_session FOREIGN KEY (session_id) REFERENCES assessment_sessions(session_id) ON DELETE CASCADE
);

-- Badges and achievements
CREATE TABLE student_badges (
    badge_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    
    -- Badge details
    badge_name VARCHAR(100) NOT NULL,
    badge_type ENUM('Milestone', 'Competency', 'Achievement', 'Special') NOT NULL,
    badge_description TEXT NULL,
    badge_icon_url VARCHAR(255) NULL,
    
    -- Requirements met
    points_requirement INT NULL,
    competency ENUM('Number_Algebra', 'Measurement_Geometry', 'Data_Probability') NULL,
    special_condition TEXT NULL,
    
    earned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_user_badges (user_id, badge_type),
    INDEX idx_badge_earned (earned_at, badge_type),
    
    CONSTRAINT fk_badge_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Leaderboards (weekly/monthly)
CREATE TABLE leaderboards (
    leaderboard_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    leaderboard_type ENUM('Weekly_Points', 'Monthly_Points', 'Weekly_Streak') NOT NULL,
    period_start DATE NOT NULL,
    period_end DATE NOT NULL,
    
    -- Top performers JSON
    rankings_json JSON NOT NULL,  -- Array of {user_id, username, points/streak, rank}
    
    status ENUM('active', 'completed', 'archived') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_leaderboard_period (leaderboard_type, period_start, period_end),
    INDEX idx_active_leaderboards (status, leaderboard_type)
);

-- Insert into users table
INSERT INTO users (username, email, password, role, last_login_date, status, created_at, updated_at)
VALUES
('student1', 'student1@example.com', '$2y$10$CwTycUXWue0Thq9StjUM0uJ8I8jFZHh5h5zJgM4oL1N9oKeFQ5w2K', 'Student', NOW(), 'active', NOW(), NOW()),
('teacher1', 'teacher1@example.com', '$2y$10$CwTycUXWue0Thq9StjUM0uJ8I8jFZHh5h5zJgM4oL1N9oKeFQ5w2K', 'Teacher', NOW(), 'active', NOW(), NOW()),
('admin1', 'admin1@example.com', '$2y$10$CwTycUXWue0Thq9StjUM0uJ8I8jFZHh5h5zJgM4oL1N9oKeFQ5w2K', 'Admin', NOW(), 'active', NOW(), NOW());

-- Link student profile (assuming the inserted IDs are 1, 2, 3 in order)
INSERT INTO student_profile (user_id, firstname, lastname, section, grade_level, school_name, avatar_url, created_at, updated_at)
VALUES
(1, 'John', 'Doe', 'A', '6', 'Pembo Elementary School', '/avatars/default.png', NOW(), NOW());

-- Link teacher profile
INSERT INTO teacher_profile (user_id, firstname, lastname, grade_level_focus, school_name, profile_url, created_at, updated_at)
VALUES
(2, 'Jane', 'Smith', '6', 'Pembo Elementary School', '/profiles/teacher1.png', NOW(), NOW());

-- Link admin profile
INSERT INTO admin_profile (user_id, firstname, lastname, grade_level_focus, school_name, profile_url, created_at, updated_at)
VALUES
(3, 'Alice', 'Johnson', '6', 'Pembo Elementary School', '/profiles/admin1.png', NOW(), NOW());


-- Insert sample questions for each competency and difficulty
INSERT INTO questions (question_id, competency, difficulty_level, question_type, question_text, choice_a, choice_b, choice_c, choice_d, correct_answer, hint_text, explanation, topic_tag) VALUES

-- Number & Algebra - Beginner (30 questions total)
('NA-B-001', 'Number_Algebra', 'Beginner', 'Multiple_Choice', 'What is 15 + 28?', '43', '42', '41', '44', 'A', 'Add the ones place first, then the tens place', '15 + 28: First add 5 + 8 = 13, write 3 carry 1. Then 1 + 2 + 1 = 4. Answer is 43.', 'Addition'),
('NA-B-002', 'Number_Algebra', 'Beginner', 'Multiple_Choice', 'What is 56 - 23?', '33', '34', '32', '35', 'A', 'Subtract the ones place, then the tens place', '56 - 23: First 6 - 3 = 3, then 5 - 2 = 3. Answer is 33.', 'Subtraction'),
('NA-B-003', 'Number_Algebra', 'Beginner', 'Multiple_Choice', 'Which number is larger: 45 or 54?', '45', '54', 'They are equal', 'Cannot tell', 'B', 'Compare the tens place first', '54 has 5 tens while 45 has 4 tens, so 54 > 45.', 'Number_Comparison'),
('NA-B-004', 'Number_Algebra', 'Beginner', 'True_False', '7 × 8 = 56', 'True', 'False', '', '', 'A', 'Remember your multiplication tables', '7 × 8 = 56 is correct.', 'Multiplication'),
('NA-B-005', 'Number_Algebra', 'Beginner', 'Fill_Blanks', 'Complete the pattern: 2, 4, 6, 8, ___', '', '', '', '', '10', 'The pattern increases by 2 each time', 'This is counting by 2s: 2, 4, 6, 8, 10.', 'Patterns'),
('NA-B-006', 'Number_Algebra', 'Beginner', 'Multiple_Choice', 'What is 6 × 9?', '52', '54', '56', '58', 'B', 'Use the multiplication table', '6 × 9 = 54', 'Multiplication'),
('NA-B-007', 'Number_Algebra', 'Beginner', 'Multiple_Choice', 'What is 72 ÷ 8?', '8', '9', '7', '10', 'B', 'Think: what number times 8 equals 72?', '72 ÷ 8 = 9 because 9 × 8 = 72', 'Division'),
('NA-B-008', 'Number_Algebra', 'Beginner', 'Multiple_Choice', 'Round 67 to the nearest 10', '60', '70', '65', '80', 'B', 'Look at the ones digit to decide', '67 rounds to 70 because 7 ≥ 5', 'Rounding'),
('NA-B-009', 'Number_Algebra', 'Beginner', 'Fill_Blanks', 'What is the missing number? 5 + ___ = 12', '', '', '', '', '7', 'What do you add to 5 to get 12?', '5 + 7 = 12, so the answer is 7', 'Addition'),
('NA-B-010', 'Number_Algebra', 'Beginner', 'Multiple_Choice', 'Which is the smallest number?', '234', '243', '324', '432', 'A', 'Compare the hundreds place first', '234 has the smallest hundreds digit (2)', 'Number_Comparison'),
('NA-B-011', 'Number_Algebra', 'Beginner', 'True_False', '100 - 45 = 55', 'True', 'False', '', '', 'A', 'Check your subtraction', '100 - 45 = 55 is correct', 'Subtraction'),
('NA-B-012', 'Number_Algebra', 'Beginner', 'Multiple_Choice', 'What comes next? 5, 10, 15, 20, ___', '23', '25', '30', '35', 'B', 'The pattern increases by 5 each time', '5, 10, 15, 20, 25 - counting by 5s', 'Patterns'),
('NA-B-013', 'Number_Algebra', 'Beginner', 'Multiple_Choice', 'What is 4 × 7?', '24', '26', '28', '30', 'C', 'Use repeated addition: 7 + 7 + 7 + 7', '4 × 7 = 28', 'Multiplication'),
('NA-B-014', 'Number_Algebra', 'Beginner', 'Fill_Blanks', 'Complete: ___ - 18 = 32', '', '', '', '', '50', 'What number minus 18 equals 32?', '50 - 18 = 32', 'Subtraction'),
('NA-B-015', 'Number_Algebra', 'Beginner', 'Multiple_Choice', 'How many tens are in 340?', '3', '4', '34', '340', 'C', 'Count all the complete groups of 10', '340 has 34 tens (3 hundreds = 30 tens, plus 4 tens)', 'Place_Value'),
('NA-B-016', 'Number_Algebra', 'Beginner', 'True_False', 'An odd number ends in 1, 3, 5, 7, or 9', 'True', 'False', '', '', 'A', 'Think about what makes a number odd', 'Odd numbers cannot be divided evenly by 2, and they end in 1, 3, 5, 7, or 9', 'Number_Properties'),
('NA-B-017', 'Number_Algebra', 'Beginner', 'Multiple_Choice', 'What is 45 ÷ 5?', '8', '9', '10', '11', 'B', 'How many groups of 5 fit into 45?', '45 ÷ 5 = 9', 'Division'),
('NA-B-018', 'Number_Algebra', 'Beginner', 'Fill_Blanks', 'Write 307 in words: three hundred ___', '', '', '', '', 'seven', 'Break the number into parts', '307 = three hundred seven', 'Number_Words'),
('NA-B-019', 'Number_Algebra', 'Beginner', 'Multiple_Choice', 'Which number is even?', '23', '45', '67', '84', 'D', 'Even numbers end in 0, 2, 4, 6, or 8', '84 ends in 4, so it is even', 'Number_Properties'),
('NA-B-020', 'Number_Algebra', 'Beginner', 'Multiple_Choice', 'What is 19 + 26?', '44', '45', '46', '47', 'B', 'Add carefully, carrying when needed', '19 + 26: 9 + 6 = 15 (carry 1), 1 + 1 + 2 = 4, so 45', 'Addition'),
('NA-B-021', 'Number_Algebra', 'Beginner', 'True_False', '3 × 4 = 4 × 3', 'True', 'False', '', '', 'A', 'Multiplication is commutative', 'Both equal 12, so 3 × 4 = 4 × 3', 'Multiplication'),
('NA-B-022', 'Number_Algebra', 'Beginner', 'Fill_Blanks', 'Round 83 to the nearest 10: ___', '', '', '', '', '80', 'Look at the ones digit', '83 rounds to 80 because 3 < 5', 'Rounding'),
('NA-B-023', 'Number_Algebra', 'Beginner', 'Multiple_Choice', 'What is the place value of 6 in 4,628?', 'Ones', 'Tens', 'Hundreds', 'Thousands', 'C', 'Count the positions from right to left', 'In 4,628, the 6 is in the hundreds place', 'Place_Value'),
('NA-B-024', 'Number_Algebra', 'Beginner', 'Multiple_Choice', 'Which fraction is larger: 1/2 or 1/4?', '1/2', '1/4', 'They are equal', 'Cannot tell', 'A', 'Think of a pizza cut into pieces', '1/2 means half, 1/4 means quarter. Half is bigger than a quarter.', 'Fractions'),
('NA-B-025', 'Number_Algebra', 'Beginner', 'Fill_Blanks', 'Complete the fact family: 6 × 8 = 48, so 48 ÷ 8 = ___', '', '', '', '', '6', 'Division and multiplication are opposite operations', '48 ÷ 8 = 6', 'Division'),
('NA-B-026', 'Number_Algebra', 'Beginner', 'Multiple_Choice', 'What is 90 - 37?', '53', '54', '52', '57', 'A', 'You may need to borrow from the tens place', '90 - 37: Borrow to make 80 + 10 - 37 = 53', 'Subtraction'),
('NA-B-027', 'Number_Algebra', 'Beginner', 'True_False', 'Zero times any number equals zero', 'True', 'False', '', '', 'A', 'This is the zero property of multiplication', 'Any number multiplied by 0 equals 0', 'Multiplication'),
('NA-B-028', 'Number_Algebra', 'Beginner', 'Multiple_Choice', 'How many minutes are in 1 hour?', '50', '60', '70', '100', 'B', 'Remember the basic time conversion', '1 hour = 60 minutes', 'Time'),
('NA-B-029', 'Number_Algebra', 'Beginner', 'Fill_Blanks', 'Skip count by 3s: 3, 6, 9, ___, 15', '', '', '', '', '12', 'Add 3 each time', '3, 6, 9, 12, 15', 'Patterns'),
('NA-B-030', 'Number_Algebra', 'Beginner', 'Multiple_Choice', 'Which shows the numbers in order from least to greatest?', '156, 165, 561', '561, 165, 156', '165, 156, 561', '156, 561, 165', 'A', 'Compare the hundreds place first', '156 < 165 < 561 when ordered from least to greatest', 'Number_Comparison'),

-- Number & Algebra - Intermediate (20 questions total)
('NA-I-001', 'Number_Algebra', 'Intermediate', 'Multiple_Choice', 'Solve: 3x + 7 = 22', 'x = 5', 'x = 4', 'x = 6', 'x = 3', 'A', 'Subtract 7 from both sides first', '3x + 7 = 22 → 3x = 15 → x = 5', 'Linear_Equations'),
('NA-I-002', 'Number_Algebra', 'Intermediate', 'Multiple_Choice', 'What is 25% of 80?', '15', '20', '25', '30', 'B', '25% means 1/4 of the number', '25% of 80 = 0.25 × 80 = 20', 'Percentages'),
('NA-I-003', 'Number_Algebra', 'Intermediate', 'Fill_Blanks', 'Simplify: 3/6 = ___/2', '', '', '', '', '1', 'Find the common factor of 3 and 6', '3/6 = 1/2, so the answer is 1', 'Fractions'),
('NA-I-004', 'Number_Algebra', 'Intermediate', 'Multiple_Choice', 'What is 2.5 + 3.7?', '5.2', '6.2', '6.1', '5.12', 'B', 'Line up the decimal points', '2.5 + 3.7 = 6.2', 'Decimals'),
('NA-I-005', 'Number_Algebra', 'Intermediate', 'Multiple_Choice', 'Solve: 2(x + 3) = 14', 'x = 4', 'x = 5', 'x = 3', 'x = 7', 'A', 'Distribute first, then solve', '2(x + 3) = 14 → 2x + 6 = 14 → 2x = 8 → x = 4', 'Linear_Equations'),
('NA-I-006', 'Number_Algebra', 'Intermediate', 'Fill_Blanks', 'Convert to decimal: 3/4 = ___', '', '', '', '', '0.75', 'Divide 3 by 4', '3 ÷ 4 = 0.75', 'Decimals'),
('NA-I-007', 'Number_Algebra', 'Intermediate', 'Multiple_Choice', 'What is 15% of 60?', '6', '7', '8', '9', 'D', '15% = 15/100 = 0.15', '15% of 60 = 0.15 × 60 = 9', 'Percentages'),
('NA-I-008', 'Number_Algebra', 'Intermediate', 'True_False', '(-3) × (-4) = 12', 'True', 'False', '', '', 'A', 'Negative times negative equals positive', '(-3) × (-4) = +12', 'Integers'),
('NA-I-009', 'Number_Algebra', 'Intermediate', 'Multiple_Choice', 'Simplify: 2/8', '1/2', '1/3', '1/4', '2/4', 'C', 'Find the greatest common factor', '2/8 = 1/4 (divide both by 2)', 'Fractions'),
('NA-I-010', 'Number_Algebra', 'Intermediate', 'Fill_Blanks', 'What is the square root of 64? ___', '', '', '', '', '8', 'What number times itself equals 64?', '8 × 8 = 64, so √64 = 8', 'Square_Roots'),
('NA-I-011', 'Number_Algebra', 'Intermediate', 'Multiple_Choice', 'Which inequality is correct?', '-5 > -3', '-3 > -5', '-5 = -3', 'None', 'B', 'On a number line, which is further right?', '-3 is greater than -5 because it is closer to zero', 'Inequalities'),
('NA-I-012', 'Number_Algebra', 'Intermediate', 'Multiple_Choice', 'Solve: x/4 = 7', 'x = 28', 'x = 11', 'x = 3', 'x = 1.75', 'A', 'Multiply both sides by 4', 'x/4 = 7 → x = 7 × 4 = 28', 'Linear_Equations'),
('NA-I-013', 'Number_Algebra', 'Intermediate', 'Fill_Blanks', 'Express as a percentage: 0.35 = ___%', '', '', '', '', '35', 'Move the decimal point two places right', '0.35 = 35%', 'Percentages'),
('NA-I-014', 'Number_Algebra', 'Intermediate', 'Multiple_Choice', 'What is 3.2 × 1.5?', '4.8', '4.7', '5.8', '3.7', 'A', 'Multiply as whole numbers, then place decimal', '32 × 15 = 480, with 2 decimal places: 4.80', 'Decimals'),
('NA-I-015', 'Number_Algebra', 'Intermediate', 'True_False', 'The absolute value of -8 is 8', 'True', 'False', '', '', 'A', 'Absolute value is distance from zero', '|-8| = 8', 'Absolute_Value'),
('NA-I-016', 'Number_Algebra', 'Intermediate', 'Multiple_Choice', 'Which fraction equals 0.6?', '3/5', '2/3', '1/6', '6/10', 'A', '0.6 means 6 tenths', '3/5 = 6/10 = 0.6', 'Fractions'),
('NA-I-017', 'Number_Algebra', 'Intermediate', 'Fill_Blanks', 'Solve: 5x - 3 = 17, so x = ___', '', '', '', '', '4', 'Add 3 to both sides, then divide by 5', '5x - 3 = 17 → 5x = 20 → x = 4', 'Linear_Equations'),
('NA-I-018', 'Number_Algebra', 'Intermediate', 'Multiple_Choice', 'What is 20% of 150?', '25', '30', '35', '40', 'B', '20% = 1/5', '20% of 150 = 0.20 × 150 = 30', 'Percentages'),
('NA-I-019', 'Number_Algebra', 'Intermediate', 'Multiple_Choice', 'Order from least to greatest: -2, 0, -5, 3', '-5, -2, 0, 3', '-2, -5, 0, 3', '0, -2, -5, 3', '3, 0, -2, -5', 'A', 'Remember that negative numbers get smaller as they go further from zero', '-5 < -2 < 0 < 3', 'Number_Comparison'),
('NA-I-020', 'Number_Algebra', 'Intermediate', 'Fill_Blanks', 'What is 4²? ___', '', '', '', '', '16', '4² means 4 × 4', '4² = 4 × 4 = 16', 'Exponents'),

-- Number & Algebra - Advanced (15 questions total)
('NA-A-001', 'Number_Algebra', 'Advanced', 'Multiple_Choice', 'Factor: x² - 5x + 6', '(x-2)(x-3)', '(x-1)(x-6)', '(x+2)(x+3)', '(x-4)(x-2)', 'A', 'Find two numbers that multiply to 6 and add to -5', 'x² - 5x + 6 = (x-2)(x-3) because -2 + -3 = -5 and (-2)×(-3) = 6', 'Factoring'),
('NA-A-002', 'Number_Algebra', 'Advanced', 'Multiple_Choice', 'If f(x) = 2x + 3, what is f(4)?', '11', '10', '9', '8', 'A', 'Substitute x = 4 into the function', 'f(4) = 2(4) + 3 = 8 + 3 = 11', 'Functions'),
('NA-A-003', 'Number_Algebra', 'Advanced', 'Fill_Blanks', 'Solve: x² - 9 = 0, so x = ___ or x = ___', '', '', '', '', '3, -3', 'This is a difference of squares', 'x² - 9 = 0 → x² = 9 → x = ±3', 'Quadratic_Equations'),
('NA-A-004', 'Number_Algebra', 'Advanced', 'Multiple_Choice', 'Simplify: (3x²)(4x³)', '12x⁵', '12x⁶', '7x⁵', '7x⁶', 'A', 'Multiply coefficients and add exponents', '(3x²)(4x³) = 12x²⁺³ = 12x⁵', 'Polynomials'),
('NA-A-005', 'Number_Algebra', 'Advanced', 'Multiple_Choice', 'What is the slope of the line through (2,3) and (4,7)?', '2', '3', '4', '1', 'A', 'Use the slope formula: (y₂-y₁)/(x₂-x₁)', 'Slope = (7-3)/(4-2) = 4/2 = 2', 'Linear_Functions'),
('NA-A-006', 'Number_Algebra', 'Advanced', 'True_False', 'The equation y = 3x - 2 represents a linear function', 'True', 'False', '', '', 'A', 'Linear functions have the form y = mx + b', 'y = 3x - 2 is in slope-intercept form, so it is linear', 'Linear_Functions'),
('NA-A-007', 'Number_Algebra', 'Advanced', 'Fill_Blanks', 'Factor completely: 2x² + 8x = ___', '', '', '', '', '2x(x + 4)', 'Factor out the greatest common factor first', '2x² + 8x = 2x(x + 4)', 'Factoring'),
('NA-A-008', 'Number_Algebra', 'Advanced', 'Multiple_Choice', 'Solve the system: x + y = 5, x - y = 1', 'x=3, y=2', 'x=2, y=3', 'x=4, y=1', 'x=1, y=4', 'A', 'Try adding the equations together', 'Adding: 2x = 6, so x = 3. Substituting: 3 + y = 5, so y = 2', 'Systems_of_Equations'),
('NA-A-009', 'Number_Algebra', 'Advanced', 'Multiple_Choice', 'What is the vertex of y = x² - 4x + 3?', '(2, -1)', '(-2, 1)', '(2, 1)', '(-2, -1)', 'A', 'Complete the square or use x = -b/2a', 'x = 4/2 = 2, y = (2)² - 4(2) + 3 = -1, so vertex is (2, -1)', 'Quadratic_Functions'),
('NA-A-010', 'Number_Algebra', 'Advanced', 'Fill_Blanks', 'Rationalize: 1/√3 = ___', '', '', '', '', '√3/3', 'Multiply by √3/√3', '1/√3 × √3/√3 = √3/3', 'Radicals'),
('NA-A-011', 'Number_Algebra', 'Advanced', 'Multiple_Choice', 'If g(x) = x² + 2x, what is g(-1)?', '-1', '0', '1', '3', 'A', 'Substitute x = -1 carefully', 'g(-1) = (-1)² + 2(-1) = 1 - 2 = -1', 'Functions'),
('NA-A-012', 'Number_Algebra', 'Advanced', 'True_False', 'The graph of y = x² opens upward', 'True', 'False', '', '', 'A', 'The coefficient of x² is positive', 'When a > 0 in y = ax², the parabola opens upward', 'Quadratic_Functions'),
('NA-A-013', 'Number_Algebra', 'Advanced', 'Multiple_Choice', 'Solve: 2^x = 8', 'x = 3', 'x = 4', 'x = 2', 'x = 6', 'A', 'What power of 2 equals 8?', '2³ = 8, so x = 3', 'Exponential_Equations'),
('NA-A-014', 'Number_Algebra', 'Advanced', 'Fill_Blanks', 'The domain of f(x) = √(x-3) is x ≥ ___', '', '', '', '', '3', 'What values make the expression under the square root non-negative?', 'x - 3 ≥ 0, so x ≥ 3', 'Domain_Range'),
('NA-A-015', 'Number_Algebra', 'Advanced', 'Multiple_Choice', 'Expand: (x + 3)(x - 2)', 'x² + x - 6', 'x² - x - 6', 'x² + x + 6', 'x² - x + 6', 'A', 'Use FOIL method', '(x + 3)(x - 2) = x² - 2x + 3x - 6 = x² + x - 6', 'Polynomials');