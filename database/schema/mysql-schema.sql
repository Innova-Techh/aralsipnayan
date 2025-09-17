/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `admin_profile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_profile` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `grade_level_focus` varchar(10) NOT NULL DEFAULT '6',
  `school_name` varchar(150) NOT NULL DEFAULT 'Pembo Elementary School',
  `profile_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admin_profile_user_id_unique` (`user_id`),
  CONSTRAINT `admin_profile_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `assessment_questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assessment_questions` (
  `pool_id` varchar(50) NOT NULL,
  `assessment_id` varchar(50) NOT NULL,
  `question_id` varchar(50) NOT NULL,
  `question_order` int(11) NOT NULL,
  `is_answered` tinyint(1) NOT NULL DEFAULT 0,
  `is_current` tinyint(1) NOT NULL DEFAULT 0,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`pool_id`),
  UNIQUE KEY `unique_assessment_question` (`assessment_id`,`question_id`),
  KEY `assessment_questions_question_id_foreign` (`question_id`),
  KEY `idx_assessment_order` (`assessment_id`,`question_order`),
  KEY `idx_current_question` (`assessment_id`,`is_current`),
  CONSTRAINT `assessment_questions_assessment_id_foreign` FOREIGN KEY (`assessment_id`) REFERENCES `assessments` (`assessment_id`) ON DELETE CASCADE,
  CONSTRAINT `assessment_questions_question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `questions` (`question_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `assessment_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assessment_sessions` (
  `session_id` varchar(50) NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `assessment_id` varchar(50) DEFAULT NULL,
  `session_type` enum('diagnostic','adaptive','custom','practice') NOT NULL,
  `competency` enum('number_algebra','measurement_geometry','data_probability','mixed') NOT NULL,
  `difficulty_level` enum('beginner','intermediate','advanced','mixed') NOT NULL,
  `total_questions` int(11) NOT NULL,
  `questions_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`questions_json`)),
  `current_question_index` int(11) NOT NULL DEFAULT 0,
  `time_limit_minutes` int(11) DEFAULT NULL,
  `total_time_allowed_seconds` int(11) DEFAULT NULL,
  `countdown_started_at` timestamp NULL DEFAULT NULL,
  `countdown_expires_at` timestamp NULL DEFAULT NULL,
  `time_remaining_seconds` int(11) DEFAULT NULL,
  `auto_submit_on_timeout` tinyint(1) NOT NULL DEFAULT 1,
  `beginner_question_time_limit` int(11) NOT NULL DEFAULT 30,
  `intermediate_question_time_limit` int(11) NOT NULL DEFAULT 45,
  `advanced_question_time_limit` int(11) NOT NULL DEFAULT 60,
  `current_question_time_limit` int(11) DEFAULT NULL,
  `paused_at` timestamp NULL DEFAULT NULL,
  `total_paused_time_seconds` int(11) NOT NULL DEFAULT 0,
  `is_paused` tinyint(1) NOT NULL DEFAULT 0,
  `questions_answered` int(11) NOT NULL DEFAULT 0,
  `correct_answers` int(11) NOT NULL DEFAULT 0,
  `incorrect_answers` int(11) NOT NULL DEFAULT 0,
  `total_points_earned` int(11) NOT NULL DEFAULT 0,
  `accuracy_percentage` decimal(5,2) DEFAULT NULL,
  `accuracy_component` decimal(5,4) DEFAULT NULL,
  `average_response_time` decimal(8,3) DEFAULT NULL,
  `time_performance_score` decimal(5,4) DEFAULT NULL,
  `cumulative_time_score` decimal(8,4) NOT NULL DEFAULT 0.0000,
  `initial_bkt_probability` decimal(5,4) DEFAULT NULL,
  `final_bkt_probability` decimal(5,4) DEFAULT NULL,
  `bkt_component` decimal(5,4) DEFAULT NULL,
  `final_mastery_score` decimal(5,2) DEFAULT NULL,
  `is_adaptive` tinyint(1) NOT NULL DEFAULT 1,
  `difficulty_progression_factor` decimal(3,2) NOT NULL DEFAULT 1.00,
  `status` enum('in_progress','completed','time_expired','abandoned') NOT NULL DEFAULT 'in_progress',
  `started_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `completed_at` timestamp NULL DEFAULT NULL,
  `last_activity_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `session_notes` text DEFAULT NULL,
  `session_metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`session_metadata`)),
  PRIMARY KEY (`session_id`),
  KEY `assessment_sessions_assessment_id_foreign` (`assessment_id`),
  KEY `idx_user_session_type` (`user_id`,`session_type`),
  KEY `idx_competency_difficulty` (`competency`,`difficulty_level`),
  KEY `idx_status_timeline` (`status`,`started_at`),
  KEY `idx_user_competency_status` (`user_id`,`competency`,`status`),
  KEY `idx_countdown_expiration` (`countdown_expires_at`,`status`),
  KEY `idx_paused_sessions` (`is_paused`,`user_id`),
  KEY `idx_active_countdowns` (`countdown_started_at`,`status`),
  KEY `idx_mastery_competency` (`final_mastery_score`,`competency`),
  KEY `idx_accuracy_difficulty` (`accuracy_percentage`,`difficulty_level`),
  KEY `idx_session_completion` (`session_type`,`completed_at`),
  CONSTRAINT `assessment_sessions_assessment_id_foreign` FOREIGN KEY (`assessment_id`) REFERENCES `assessments` (`assessment_id`) ON DELETE SET NULL,
  CONSTRAINT `assessment_sessions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `assessments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assessments` (
  `assessment_id` varchar(50) NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `competency` enum('number_algebra','measurement_geometry','data_probability') NOT NULL,
  `assessment_type` enum('diagnostic','regular') NOT NULL,
  `difficulty_level` enum('beginner','intermediate','advanced') NOT NULL,
  `total_questions` int(11) NOT NULL,
  `time_limit` int(11) NOT NULL DEFAULT 30,
  `status` enum('in_progress','completed','abandoned') NOT NULL DEFAULT 'in_progress',
  `is_diagnostic_phase` tinyint(1) NOT NULL DEFAULT 0,
  `diagnostic_phase` int(11) DEFAULT NULL,
  `correct_answers` int(11) NOT NULL DEFAULT 0,
  `incorrect_answers` int(11) NOT NULL DEFAULT 0,
  `questions_answered` int(11) NOT NULL DEFAULT 0,
  `accuracy_percentage` decimal(5,2) DEFAULT NULL,
  `accuracy_component` decimal(5,4) DEFAULT NULL,
  `bkt_score_before` decimal(5,4) DEFAULT NULL,
  `bkt_score_after` decimal(5,4) DEFAULT NULL,
  `bkt_final_score` decimal(5,4) DEFAULT NULL,
  `bkt_component` decimal(5,4) DEFAULT NULL,
  `final_mastery_score` decimal(5,2) DEFAULT NULL,
  `total_time_spent` int(11) NOT NULL DEFAULT 0,
  `average_response_time` decimal(8,3) DEFAULT NULL,
  `time_performance_score` decimal(5,4) DEFAULT NULL,
  `cumulative_time_score` decimal(8,4) NOT NULL DEFAULT 0.0000,
  `average_time_factor` decimal(5,4) DEFAULT NULL,
  `started_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `completed_at` timestamp NULL DEFAULT NULL,
  `last_activity` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`assessment_id`),
  KEY `idx_user_assessments` (`user_id`,`started_at`),
  KEY `idx_competency_assessments` (`competency`,`assessment_type`),
  KEY `idx_assessment_status` (`status`,`started_at`),
  KEY `idx_diagnostic_tracking` (`user_id`,`assessment_type`,`competency`),
  KEY `idx_assessment_type_status` (`assessment_type`,`status`),
  KEY `idx_diagnostic_phase_status` (`is_diagnostic_phase`,`status`),
  CONSTRAINT `assessments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `daily_assessments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `daily_assessments` (
  `record_id` varchar(50) NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `assessment_date` date NOT NULL,
  `assessments_completed` int(11) NOT NULL DEFAULT 0,
  `competencies_completed` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`competencies_completed`)),
  `points_earned_today` int(11) NOT NULL DEFAULT 0,
  `contributes_to_streak` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`record_id`),
  UNIQUE KEY `unique_user_date` (`user_id`,`assessment_date`),
  KEY `idx_assessment_date` (`assessment_date`),
  KEY `idx_streak_tracking` (`user_id`,`assessment_date`),
  CONSTRAINT `daily_assessments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `diagnostic_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `diagnostic_sessions` (
  `session_id` varchar(50) NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `competency` enum('number_algebra','measurement_geometry','data_probability') NOT NULL,
  `total_phases` int(11) NOT NULL DEFAULT 3,
  `current_phase` int(11) NOT NULL DEFAULT 1,
  `phase_1_score` decimal(5,4) DEFAULT NULL,
  `phase_1_questions` int(11) NOT NULL DEFAULT 15,
  `phase_1_time_factor` decimal(5,4) DEFAULT NULL,
  `phase_2_score` decimal(5,4) DEFAULT NULL,
  `phase_2_questions` int(11) NOT NULL DEFAULT 15,
  `phase_2_time_factor` decimal(5,4) DEFAULT NULL,
  `phase_3_score` decimal(5,4) DEFAULT NULL,
  `phase_3_questions` int(11) NOT NULL DEFAULT 10,
  `phase_3_time_factor` decimal(5,4) DEFAULT NULL,
  `final_master_score` decimal(5,2) DEFAULT NULL,
  `accuracy_component` decimal(5,4) DEFAULT NULL,
  `bkt_component` decimal(5,4) DEFAULT NULL,
  `recommended_difficulty` enum('beginner','intermediate','advanced') DEFAULT NULL,
  `status` enum('in_progress','completed') NOT NULL DEFAULT 'in_progress',
  `started_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `completed_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`session_id`),
  UNIQUE KEY `unique_user_competency_diagnostic` (`user_id`,`competency`),
  KEY `idx_session_status` (`status`),
  KEY `idx_session_lookup` (`user_id`,`competency`,`status`),
  KEY `idx_status_time` (`status`,`started_at`),
  KEY `idx_user_diagnostics` (`user_id`,`completed_at`),
  CONSTRAINT `diagnostic_sessions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `points_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `points_transactions` (
  `transaction_id` varchar(50) NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `points_earned` int(11) NOT NULL,
  `points_type` enum('base_question','time_bonus','competency_completion','perfect_assessment','daily_triple_completion','daily_streak') NOT NULL,
  `question_id` varchar(50) DEFAULT NULL,
  `assessment_id` varchar(50) DEFAULT NULL,
  `competency` enum('number_algebra','measurement_geometry','data_probability') DEFAULT NULL,
  `difficulty_level` enum('beginner','intermediate','advanced') DEFAULT NULL,
  `description` text DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `earned_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`transaction_id`),
  KEY `idx_user_points` (`user_id`,`earned_at`),
  KEY `idx_points_type` (`points_type`),
  KEY `idx_competency_points` (`competency`,`earned_at`),
  CONSTRAINT `points_transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `question_cooldowns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `question_cooldowns` (
  `cooldown_id` varchar(50) NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `question_id` varchar(50) NOT NULL,
  `competency` enum('number_algebra','measurement_geometry','data_probability') NOT NULL,
  `was_correct` tinyint(1) NOT NULL,
  `answered_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `response_time` decimal(8,3) NOT NULL,
  `cooldown_duration` int(11) NOT NULL,
  `cooldown_until` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`cooldown_id`),
  UNIQUE KEY `unique_active_cooldown` (`user_id`,`question_id`,`is_active`),
  KEY `question_cooldowns_question_id_foreign` (`question_id`),
  KEY `idx_user_competency_cooldown` (`user_id`,`competency`,`is_active`,`cooldown_until`),
  KEY `idx_cooldown_expiry` (`cooldown_until`,`is_active`),
  CONSTRAINT `question_cooldowns_question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `questions` (`question_id`) ON DELETE CASCADE,
  CONSTRAINT `question_cooldowns_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `question_media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `question_media` (
  `media_id` varchar(50) NOT NULL,
  `question_id` varchar(50) NOT NULL,
  `media_type` enum('image','video','audio') NOT NULL DEFAULT 'image',
  `media_url` varchar(500) NOT NULL,
  `media_description` text DEFAULT NULL,
  `display_order` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`media_id`),
  KEY `idx_question_media` (`question_id`),
  CONSTRAINT `question_media_question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `questions` (`question_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `question_responses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `question_responses` (
  `response_id` varchar(50) NOT NULL,
  `assessment_id` varchar(50) NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `question_id` varchar(50) NOT NULL,
  `user_answer` text DEFAULT NULL,
  `is_correct` tinyint(1) NOT NULL,
  `response_time` decimal(8,3) NOT NULL,
  `max_allowed_time` int(11) NOT NULL,
  `normalized_time` decimal(5,4) DEFAULT NULL,
  `time_score` decimal(3,2) DEFAULT NULL,
  `bkt_before` decimal(5,4) NOT NULL,
  `bkt_after` decimal(5,4) NOT NULL,
  `difficulty_factor` decimal(3,2) DEFAULT NULL,
  `time_factor` decimal(5,4) DEFAULT NULL,
  `ftime_factor` decimal(5,4) DEFAULT NULL,
  `base_points` int(11) NOT NULL DEFAULT 0,
  `time_bonus_points` int(11) NOT NULL DEFAULT 0,
  `total_points` int(11) NOT NULL DEFAULT 0,
  `answered_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`response_id`),
  KEY `idx_assessment_responses` (`assessment_id`,`answered_at`),
  KEY `idx_user_responses` (`user_id`,`answered_at`),
  KEY `idx_question_performance` (`question_id`,`is_correct`),
  KEY `idx_bkt_tracking` (`user_id`,`answered_at`),
  CONSTRAINT `question_responses_assessment_id_foreign` FOREIGN KEY (`assessment_id`) REFERENCES `assessments` (`assessment_id`) ON DELETE CASCADE,
  CONSTRAINT `question_responses_question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `questions` (`question_id`),
  CONSTRAINT `question_responses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `questions` (
  `question_id` varchar(50) NOT NULL,
  `competency` enum('number_algebra','measurement_geometry','data_probability') NOT NULL,
  `difficulty_level` enum('beginner','intermediate','advanced') NOT NULL,
  `topic_tag` varchar(100) NOT NULL,
  `question_text` text NOT NULL,
  `question_type` enum('multiple_choice','fill_blanks','true_false','drag_drop','connect_dots') NOT NULL DEFAULT 'multiple_choice',
  `choice_a` text DEFAULT NULL,
  `choice_b` text DEFAULT NULL,
  `choice_c` text DEFAULT NULL,
  `choice_d` text DEFAULT NULL,
  `correct_answer` text NOT NULL,
  `hint_text` text DEFAULT NULL,
  `explanation` text DEFAULT NULL,
  `max_allowed_time` int(11) NOT NULL,
  `estimated_difficulty_weight` decimal(3,2) NOT NULL DEFAULT 1.00,
  `question_source` enum('built_in','custom') NOT NULL DEFAULT 'built_in',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `usage_count` int(11) NOT NULL DEFAULT 0,
  `success_rate` decimal(5,4) NOT NULL DEFAULT 0.0000,
  `base_points` int(11) NOT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`question_id`),
  KEY `questions_created_by_foreign` (`created_by`),
  KEY `idx_competency_difficulty` (`competency`,`difficulty_level`),
  KEY `idx_topic_active` (`topic_tag`,`is_active`),
  KEY `idx_difficulty_usage` (`difficulty_level`,`usage_count`),
  CONSTRAINT `questions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `student_mastery`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `student_mastery` (
  `mastery_id` varchar(50) NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `competency` enum('number_algebra','measurement_geometry','data_probability') NOT NULL,
  `current_difficulty` enum('beginner','intermediate','advanced') NOT NULL DEFAULT 'beginner',
  `accuracy_score` decimal(5,4) NOT NULL DEFAULT 0.0000,
  `accuracy_component` decimal(5,4) DEFAULT NULL,
  `bkt_score` decimal(5,4) NOT NULL DEFAULT 0.5000,
  `bkt_component` decimal(5,4) DEFAULT NULL,
  `final_mastery_score` decimal(5,2) NOT NULL DEFAULT 22.50,
  `prior_knowledge` decimal(5,4) NOT NULL DEFAULT 0.1000,
  `learn_rate` decimal(5,4) NOT NULL DEFAULT 0.3000,
  `slip_rate` decimal(5,4) NOT NULL DEFAULT 0.1000,
  `guess_rate` decimal(5,4) NOT NULL DEFAULT 0.2500,
  `has_taken_diagnostic` tinyint(1) NOT NULL DEFAULT 0,
  `diagnostic_completed_at` timestamp NULL DEFAULT NULL,
  `total_questions_answered` int(11) NOT NULL DEFAULT 0,
  `correct_answers` int(11) NOT NULL DEFAULT 0,
  `total_assessments_taken` int(11) NOT NULL DEFAULT 0,
  `cumulative_time_score` decimal(8,4) NOT NULL DEFAULT 0.0000,
  `current_ftime_factor` decimal(5,4) NOT NULL DEFAULT 1.0000,
  `average_time_factor` decimal(5,4) NOT NULL DEFAULT 1.0000,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`mastery_id`),
  UNIQUE KEY `unique_user_competency` (`user_id`,`competency`),
  KEY `idx_user_mastery` (`user_id`),
  KEY `idx_competency_difficulty` (`competency`,`current_difficulty`),
  KEY `idx_mastery_score` (`final_mastery_score`),
  KEY `idx_has_taken_diagnostic` (`has_taken_diagnostic`),
  KEY `idx_diagnostic_status` (`user_id`,`competency`,`has_taken_diagnostic`),
  CONSTRAINT `student_mastery_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `student_profile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `student_profile` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `student_id` varchar(50) DEFAULT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `middlename` varchar(100) DEFAULT NULL,
  `section` varchar(50) NOT NULL,
  `grade_level` varchar(10) NOT NULL DEFAULT '6',
  `school_name` varchar(150) NOT NULL DEFAULT 'Pembo Elementary School',
  `school_year` varchar(20) DEFAULT NULL,
  `avatar_url` varchar(255) NOT NULL DEFAULT '/images/profile/default.png',
  `has_completed_onboarding` tinyint(1) NOT NULL DEFAULT 0,
  `onboarding_completed_at` timestamp NULL DEFAULT NULL,
  `is_first_login` tinyint(1) NOT NULL DEFAULT 1,
  `has_viewed_assessments` tinyint(1) NOT NULL DEFAULT 0,
  `first_assessment_view_at` timestamp NULL DEFAULT NULL,
  `current_streak` int(11) NOT NULL DEFAULT 0,
  `longest_streak` int(11) NOT NULL DEFAULT 0,
  `last_activity_date` date DEFAULT NULL,
  `total_points` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `student_profile_user_id_unique` (`user_id`),
  UNIQUE KEY `student_profile_student_id_unique` (`student_id`),
  CONSTRAINT `student_profile_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `teacher_profile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `teacher_profile` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `grade_level_focus` varchar(10) NOT NULL DEFAULT '6',
  `school_name` varchar(150) NOT NULL DEFAULT 'Pembo Elementary School',
  `profile_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `teacher_profile_user_id_unique` (`user_id`),
  CONSTRAINT `teacher_profile_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `trophies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `trophies` (
  `trophy_id` varchar(50) NOT NULL,
  `trophy_name` varchar(100) NOT NULL,
  `trophy_type` enum('class_weekly','school_monthly') NOT NULL,
  `trophy_description` text DEFAULT NULL,
  `max_winners` int(11) NOT NULL,
  `reset_frequency` enum('weekly','monthly') NOT NULL,
  `trophy_icon_url` varchar(500) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`trophy_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `trophy_periods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `trophy_periods` (
  `period_id` varchar(50) NOT NULL,
  `trophy_id` varchar(50) NOT NULL,
  `period_start` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `period_end` timestamp NULL DEFAULT NULL,
  `period_type` enum('weekly','monthly') NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `winners_calculated` tinyint(1) NOT NULL DEFAULT 0,
  `class_id` varchar(50) DEFAULT NULL,
  `school_id` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`period_id`),
  KEY `idx_active_periods` (`is_active`,`period_end`),
  KEY `idx_trophy_periods` (`trophy_id`,`period_start`),
  CONSTRAINT `trophy_periods_trophy_id_foreign` FOREIGN KEY (`trophy_id`) REFERENCES `trophies` (`trophy_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `trophy_winners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `trophy_winners` (
  `winner_id` varchar(50) NOT NULL,
  `period_id` varchar(50) NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `final_position` int(11) NOT NULL,
  `points_earned_in_period` int(11) NOT NULL,
  `awarded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `notification_sent` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`winner_id`),
  UNIQUE KEY `unique_period_user` (`period_id`,`user_id`),
  KEY `idx_period_rankings` (`period_id`,`final_position`),
  KEY `idx_user_trophies` (`user_id`,`awarded_at`),
  CONSTRAINT `trophy_winners_period_id_foreign` FOREIGN KEY (`period_id`) REFERENCES `trophy_periods` (`period_id`),
  CONSTRAINT `trophy_winners_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_progress`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_progress` (
  `user_id` bigint(20) unsigned NOT NULL,
  `total_points` int(11) NOT NULL DEFAULT 0,
  `current_level` int(11) NOT NULL DEFAULT 1,
  `points_in_current_level` int(11) NOT NULL DEFAULT 0,
  `current_rank` varchar(50) NOT NULL DEFAULT 'Math Explorer',
  `rank_level_threshold` int(11) NOT NULL DEFAULT 10,
  `current_streak` int(11) NOT NULL DEFAULT 0,
  `longest_streak` int(11) NOT NULL DEFAULT 0,
  `last_assessment_date` date DEFAULT NULL,
  `daily_competencies_completed` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`daily_competencies_completed`)),
  `last_daily_reset` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  KEY `idx_total_points` (`total_points`),
  KEY `idx_current_level` (`current_level`),
  KEY `idx_streak` (`current_streak`),
  CONSTRAINT `user_progress_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Student','Teacher','Admin') NOT NULL,
  `last_login_date` datetime DEFAULT NULL,
  `status` enum('active','inactive','archive') NOT NULL DEFAULT 'active',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1,'0001_01_01_000000_create_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2,'0001_01_01_000001_create_cache_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3,'0001_01_01_000002_create_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4,'2025_08_14_154903_create_student_profiles_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5,'2025_08_14_154913_create_teacher_profiles_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6,'2025_08_14_154941_create_admin_profiles_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7,'2025_09_09_193303_create_questions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8,'2025_09_09_193335_create_question_media_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (9,'2025_09_09_193404_create_question_cooldowns_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (10,'2025_09_09_193624_create_student_mastery_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (11,'2025_09_09_194347_create_assessments_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (12,'2025_09_09_194421_create_diagnostic_sessions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (13,'2025_09_09_194517_create_assessment_questions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (14,'2025_09_09_194708_create_question_responses_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (15,'2025_09_09_195200_create_user_progress_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (16,'2025_09_09_195239_create_points_transactions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (17,'2025_09_09_195325_create_daily_assessments_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (18,'2025_09_09_195759_create_trophies_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (19,'2025_09_09_195841_create_trophy_periods_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (20,'2025_09_09_195936_create_trophy_winners_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (21,'2025_09_16_235036_create_assessment_sessions_table',1);
