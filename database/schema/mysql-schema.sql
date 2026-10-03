-- ============================================================================
-- DTCS Hospital Management System — Complete Database Schema (MySQL Schema Dump)
-- ============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'STRICT_TRANS_TABLES,NO_ENGINE_SUBSTITUTION';

-- ════════════════════════════════════════════════════════════════════════════
-- AUTHENTICATION & FRAMEWORK TABLES
-- ════════════════════════════════════════════════════════════════════════════

CREATE TABLE IF NOT EXISTS `users` (
    `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`              VARCHAR(255)    NOT NULL,
    `employee_id`       VARCHAR(255)    NULL DEFAULT NULL,
    `department`        VARCHAR(255)    NULL DEFAULT NULL,
    `phone`             VARCHAR(20)     NULL DEFAULT NULL,
    `avatar`            VARCHAR(255)    NULL DEFAULT NULL,
    `is_active`         TINYINT(1)      NOT NULL DEFAULT 1,
    `email`             VARCHAR(255)    NOT NULL,
    `email_verified_at` TIMESTAMP       NULL DEFAULT NULL,
    `password`          VARCHAR(255)    NOT NULL,
    `remember_token`    VARCHAR(100)    NULL DEFAULT NULL,
    `login_token`       VARCHAR(512)    NULL DEFAULT NULL,
    `active_session_id` VARCHAR(255)    NULL DEFAULT NULL,
    `last_activity_at`  TIMESTAMP       NULL DEFAULT NULL,
    `failed_attempts`   TINYINT UNSIGNED NOT NULL DEFAULT 0,
    `locked_at`         TIMESTAMP       NULL DEFAULT NULL,
    `lockout_until`     TIMESTAMP       NULL DEFAULT NULL,
    `created_at`        TIMESTAMP       NULL DEFAULT NULL,
    `updated_at`        TIMESTAMP       NULL DEFAULT NULL,
    `deleted_at`        TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `users_email_unique` (`email`),
    UNIQUE KEY `users_employee_id_unique` (`employee_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
    `email`      VARCHAR(255) NOT NULL,
    `token`      VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP    NULL DEFAULT NULL,
    PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `sessions` (
    `id`            VARCHAR(255) NOT NULL,
    `user_id`       BIGINT UNSIGNED NULL DEFAULT NULL,
    `ip_address`    VARCHAR(45)  NULL DEFAULT NULL,
    `user_agent`    TEXT         NULL DEFAULT NULL,
    `payload`       LONGTEXT     NOT NULL,
    `last_activity` INT          NOT NULL,
    PRIMARY KEY (`id`),
    KEY `sessions_user_id_index` (`user_id`),
    KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ════════════════════════════════════════════════════════════════════════════
-- ROLE-BASED ACCESS CONTROL (RBAC) TABLES
-- ════════════════════════════════════════════════════════════════════════════

CREATE TABLE IF NOT EXISTS `roles` (
    `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`        VARCHAR(255)    NOT NULL,
    `slug`        VARCHAR(255)    NOT NULL,
    `description` VARCHAR(255)    NULL DEFAULT NULL,
    `created_at`  TIMESTAMP       NULL DEFAULT NULL,
    `updated_at`  TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `roles_name_unique` (`name`),
    UNIQUE KEY `roles_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `permissions` (
    `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`        VARCHAR(255)    NOT NULL,
    `slug`        VARCHAR(255)    NOT NULL,
    `module`      VARCHAR(255)    NOT NULL,
    `description` VARCHAR(255)    NULL DEFAULT NULL,
    `created_at`  TIMESTAMP       NULL DEFAULT NULL,
    `updated_at`  TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `permissions_name_unique` (`name`),
    UNIQUE KEY `permissions_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `role_user` (
    `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`    BIGINT UNSIGNED NOT NULL,
    `role_id`    BIGINT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP       NULL DEFAULT NULL,
    `updated_at` TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `role_user_user_id_role_id_unique` (`user_id`, `role_id`),
    KEY `role_user_role_id_foreign` (`role_id`),
    CONSTRAINT `role_user_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `role_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `permission_role` (
    `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `permission_id` BIGINT UNSIGNED NOT NULL,
    `role_id`       BIGINT UNSIGNED NOT NULL,
    `created_at`    TIMESTAMP       NULL DEFAULT NULL,
    `updated_at`    TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `permission_role_permission_id_role_id_unique` (`permission_id`, `role_id`),
    KEY `permission_role_role_id_foreign` (`role_id`),
    CONSTRAINT `permission_role_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `permission_role_role_id_foreign`       FOREIGN KEY (`role_id`)       REFERENCES `roles`       (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ════════════════════════════════════════════════════════════════════════════
-- PATIENT INFORMATION MODULE TABLES
-- ════════════════════════════════════════════════════════════════════════════

CREATE TABLE IF NOT EXISTS `patients` (
    `id`             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `patient_no`     VARCHAR(50)     NOT NULL,
    `first_name`     VARCHAR(100)    NOT NULL,
    `last_name`      VARCHAR(100)    NOT NULL,
    `middle_name`    VARCHAR(100)    NULL DEFAULT NULL,
    `date_of_birth`  DATE            NOT NULL,
    `gender`         ENUM('Male', 'Female', 'Other') NOT NULL,
    `blood_type`     VARCHAR(5)      NULL DEFAULT NULL,
    `patient_type`   ENUM('Inpatient', 'Outpatient') NOT NULL DEFAULT 'Outpatient',
    `room_number`    VARCHAR(50)     NULL DEFAULT NULL,
    `phone`          VARCHAR(20)     NULL DEFAULT NULL,
    `email`          VARCHAR(255)    NULL DEFAULT NULL,
    `address`        TEXT            NULL DEFAULT NULL,
    `allergies`      TEXT            NULL DEFAULT NULL,
    `created_at`     TIMESTAMP       NULL DEFAULT NULL,
    `updated_at`     TIMESTAMP       NULL DEFAULT NULL,
    `deleted_at`     TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `patients_patient_no_unique` (`patient_no`),
    KEY `idx_patients_name` (`last_name`, `first_name`),
    KEY `idx_patients_type` (`patient_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ════════════════════════════════════════════════════════════════════════════
-- CLINICAL SERVICES MODULE TABLES
-- ════════════════════════════════════════════════════════════════════════════

CREATE TABLE IF NOT EXISTS `lab_requests` (
    `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `request_no`    VARCHAR(50)     NOT NULL,
    `patient_id`    BIGINT UNSIGNED NOT NULL,
    `doctor_id`     BIGINT UNSIGNED NOT NULL,
    `test_name`     VARCHAR(255)    NOT NULL,
    `specimen_type` VARCHAR(100)    NULL DEFAULT NULL,
    `priority`      ENUM('Routine', 'Urgent', 'STAT') NOT NULL DEFAULT 'Routine',
    `status`        ENUM('Pending', 'In Progress', 'Completed', 'Cancelled') NOT NULL DEFAULT 'Pending',
    `remarks`       TEXT            NULL DEFAULT NULL,
    `created_at`    TIMESTAMP       NULL DEFAULT NULL,
    `updated_at`    TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `lab_requests_request_no_unique` (`request_no`),
    KEY `lab_requests_patient_id_foreign` (`patient_id`),
    KEY `lab_requests_doctor_id_foreign`  (`doctor_id`),
    KEY `idx_lab_requests_status`   (`status`),
    KEY `idx_lab_requests_priority` (`priority`),
    CONSTRAINT `lab_requests_doctor_id_foreign`  FOREIGN KEY (`doctor_id`)  REFERENCES `users` (`id`)    ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `lab_requests_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE  ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `lab_results` (
    `id`              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `lab_request_id`  BIGINT UNSIGNED NOT NULL,
    `technologist_id` BIGINT UNSIGNED NULL DEFAULT NULL,
    `result_value`    TEXT            NOT NULL,
    `unit`            VARCHAR(50)     NULL DEFAULT NULL,
    `reference_range` VARCHAR(100)    NULL DEFAULT NULL,
    `status`          ENUM('Normal', 'Abnormal', 'Critical') NOT NULL DEFAULT 'Normal',
    `is_released`     TINYINT(1)      NOT NULL DEFAULT 0,
    `validated_by`    BIGINT UNSIGNED NULL DEFAULT NULL,
    `released_by`     BIGINT UNSIGNED NULL DEFAULT NULL,
    `released_at`     TIMESTAMP       NULL DEFAULT NULL,
    `created_at`      TIMESTAMP       NULL DEFAULT NULL,
    `updated_at`      TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `lab_results_lab_request_id_foreign`  (`lab_request_id`),
    KEY `lab_results_technologist_id_foreign` (`technologist_id`),
    KEY `lab_results_validated_by_foreign`    (`validated_by`),
    KEY `lab_results_released_by_foreign`     (`released_by`),
    CONSTRAINT `lab_results_lab_request_id_foreign`  FOREIGN KEY (`lab_request_id`)  REFERENCES `lab_requests` (`id`) ON DELETE CASCADE  ON UPDATE CASCADE,
    CONSTRAINT `lab_results_released_by_foreign`     FOREIGN KEY (`released_by`)     REFERENCES `users` (`id`)        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `lab_results_technologist_id_foreign` FOREIGN KEY (`technologist_id`) REFERENCES `users` (`id`)        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `lab_results_validated_by_foreign`    FOREIGN KEY (`validated_by`)    REFERENCES `users` (`id`)        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `radiology_requests` (
    `id`             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `request_no`     VARCHAR(50)     NOT NULL,
    `patient_id`     BIGINT UNSIGNED NOT NULL,
    `doctor_id`      BIGINT UNSIGNED NOT NULL,
    `modality`       ENUM('X-Ray', 'CT Scan', 'MRI', 'Ultrasound') NOT NULL,
    `procedure_name` VARCHAR(255)    NOT NULL,
    `priority`       ENUM('Routine', 'Urgent', 'STAT') NOT NULL DEFAULT 'Routine',
    `status`         ENUM('Pending', 'In Progress', 'Completed', 'Cancelled') NOT NULL DEFAULT 'Pending',
    `clinical_notes` TEXT            NULL DEFAULT NULL,
    `created_at`     TIMESTAMP       NULL DEFAULT NULL,
    `updated_at`     TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `radiology_requests_request_no_unique` (`request_no`),
    KEY `radiology_requests_patient_id_foreign` (`patient_id`),
    KEY `radiology_requests_doctor_id_foreign`  (`doctor_id`),
    CONSTRAINT `radiology_requests_doctor_id_foreign`  FOREIGN KEY (`doctor_id`)  REFERENCES `users` (`id`)    ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `radiology_requests_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE  ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `radiology_images` (
    `id`                   BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `radiology_request_id` BIGINT UNSIGNED NOT NULL,
    `file_path`            VARCHAR(512)    NOT NULL,
    `file_name`            VARCHAR(255)    NOT NULL,
    `file_type`            VARCHAR(50)     NOT NULL DEFAULT 'dicom',
    `uploaded_by`          BIGINT UNSIGNED NULL DEFAULT NULL,
    `created_at`           TIMESTAMP       NULL DEFAULT NULL,
    `updated_at`           TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `radiology_images_radiology_request_id_foreign` (`radiology_request_id`),
    KEY `radiology_images_uploaded_by_foreign`           (`uploaded_by`),
    CONSTRAINT `radiology_images_radiology_request_id_foreign` FOREIGN KEY (`radiology_request_id`) REFERENCES `radiology_requests` (`id`) ON DELETE CASCADE  ON UPDATE CASCADE,
    CONSTRAINT `radiology_images_uploaded_by_foreign`           FOREIGN KEY (`uploaded_by`)           REFERENCES `users` (`id`)              ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `radiology_reports` (
    `id`                   BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `radiology_request_id` BIGINT UNSIGNED NOT NULL,
    `radiologist_id`       BIGINT UNSIGNED NOT NULL,
    `findings`             TEXT            NOT NULL,
    `impression`           TEXT            NOT NULL,
    `status`               ENUM('Draft', 'Final', 'Released') NOT NULL DEFAULT 'Draft',
    `approved_by`          BIGINT UNSIGNED NULL DEFAULT NULL,
    `released_by`          BIGINT UNSIGNED NULL DEFAULT NULL,
    `released_at`          TIMESTAMP       NULL DEFAULT NULL,
    `created_at`           TIMESTAMP       NULL DEFAULT NULL,
    `updated_at`           TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `radiology_reports_radiology_request_id_foreign` (`radiology_request_id`),
    KEY `radiology_reports_radiologist_id_foreign`       (`radiologist_id`),
    KEY `radiology_reports_approved_by_foreign`          (`approved_by`),
    KEY `radiology_reports_released_by_foreign`          (`released_by`),
    CONSTRAINT `radiology_reports_approved_by_foreign`           FOREIGN KEY (`approved_by`)           REFERENCES `users` (`id`)              ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `radiology_reports_radiologist_id_foreign`        FOREIGN KEY (`radiologist_id`)        REFERENCES `users` (`id`)              ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `radiology_reports_radiology_request_id_foreign` FOREIGN KEY (`radiology_request_id`) REFERENCES `radiology_requests` (`id`) ON DELETE CASCADE  ON UPDATE CASCADE,
    CONSTRAINT `radiology_reports_released_by_foreign`           FOREIGN KEY (`released_by`)           REFERENCES `users` (`id`)              ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `prescriptions` (
    `id`             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `rx_no`          VARCHAR(50)     NOT NULL,
    `patient_id`     BIGINT UNSIGNED NOT NULL,
    `doctor_id`      BIGINT UNSIGNED NOT NULL,
    `medication`     VARCHAR(255)    NOT NULL,
    `dosage`         VARCHAR(100)    NOT NULL,
    `frequency`      VARCHAR(100)    NOT NULL,
    `duration_days`  INT UNSIGNED    NOT NULL DEFAULT 1,
    `notes`          TEXT            NULL DEFAULT NULL,
    `status`         ENUM('Pending', 'Verified', 'Dispensed', 'Cancelled') NOT NULL DEFAULT 'Pending',
    `verified_by`    BIGINT UNSIGNED NULL DEFAULT NULL,
    `created_at`     TIMESTAMP       NULL DEFAULT NULL,
    `updated_at`     TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `prescriptions_rx_no_unique` (`rx_no`),
    KEY `prescriptions_patient_id_foreign`  (`patient_id`),
    KEY `prescriptions_doctor_id_foreign`   (`doctor_id`),
    KEY `prescriptions_verified_by_foreign` (`verified_by`),
    CONSTRAINT `prescriptions_doctor_id_foreign`   FOREIGN KEY (`doctor_id`)   REFERENCES `users`    (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `prescriptions_patient_id_foreign`  FOREIGN KEY (`patient_id`)  REFERENCES `patients` (`id`) ON DELETE CASCADE  ON UPDATE CASCADE,
    CONSTRAINT `prescriptions_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users`    (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `dispensing_records` (
    `id`              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `prescription_id` BIGINT UNSIGNED NOT NULL,
    `pharmacist_id`   BIGINT UNSIGNED NOT NULL,
    `quantity`        INT UNSIGNED    NOT NULL,
    `lot_number`      VARCHAR(100)    NULL DEFAULT NULL,
    `notes`           TEXT            NULL DEFAULT NULL,
    `dispensed_at`    TIMESTAMP       NOT NULL,
    `created_at`      TIMESTAMP       NULL DEFAULT NULL,
    `updated_at`      TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `dispensing_records_prescription_id_foreign` (`prescription_id`),
    KEY `dispensing_records_pharmacist_id_foreign`   (`pharmacist_id`),
    CONSTRAINT `dispensing_records_pharmacist_id_foreign`         FOREIGN KEY (`pharmacist_id`)         REFERENCES `users`         (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `dispensing_records_prescription_id_foreign`       FOREIGN KEY (`prescription_id`)       REFERENCES `prescriptions` (`id`) ON DELETE CASCADE  ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `surgery_requests` (
    `id`               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `request_no`       VARCHAR(50)     NOT NULL,
    `patient_id`       BIGINT UNSIGNED NOT NULL,
    `doctor_id`        BIGINT UNSIGNED NOT NULL,
    `procedure_name`   VARCHAR(255)    NOT NULL,
    `operating_room`   VARCHAR(50)     NULL DEFAULT NULL,
    `preop_diagnosis`  TEXT            NULL DEFAULT NULL,
    `priority`         ENUM('Elective', 'Urgent', 'Emergency') NOT NULL DEFAULT 'Elective',
    `status`           ENUM('Requested', 'Scheduled', 'In Surgery', 'Completed', 'Cancelled') NOT NULL DEFAULT 'Requested',
    `scheduled_start`  DATETIME        NULL DEFAULT NULL,
    `scheduled_end`    DATETIME        NULL DEFAULT NULL,
    `created_at`       TIMESTAMP       NULL DEFAULT NULL,
    `updated_at`       TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `surgery_requests_request_no_unique` (`request_no`),
    KEY `surgery_requests_patient_id_foreign` (`patient_id`),
    KEY `surgery_requests_doctor_id_foreign`  (`doctor_id`),
    CONSTRAINT `surgery_requests_doctor_id_foreign`  FOREIGN KEY (`doctor_id`)  REFERENCES `users`    (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `surgery_requests_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE  ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `surgical_teams` (
    `id`                 BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `surgery_request_id` BIGINT UNSIGNED NOT NULL,
    `surgeon_id`         BIGINT UNSIGNED NOT NULL,
    `anesthesiologist`   VARCHAR(255)    NULL DEFAULT NULL,
    `scrub_nurse`        VARCHAR(255)    NULL DEFAULT NULL,
    `circulating_nurse`  VARCHAR(255)    NULL DEFAULT NULL,
    `created_at`         TIMESTAMP       NULL DEFAULT NULL,
    `updated_at`         TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `surgical_teams_surgery_request_id_foreign` (`surgery_request_id`),
    KEY `surgical_teams_surgeon_id_foreign`         (`surgeon_id`),
    CONSTRAINT `surgical_teams_surgeon_id_foreign`         FOREIGN KEY (`surgeon_id`)         REFERENCES `users`            (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `surgical_teams_surgery_request_id_foreign` FOREIGN KEY (`surgery_request_id`) REFERENCES `surgery_requests` (`id`) ON DELETE CASCADE  ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `surgical_team_members` (
    `id`               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `surgical_team_id` BIGINT UNSIGNED NOT NULL,
    `user_id`          BIGINT UNSIGNED NOT NULL,
    `role_in_surgery`  VARCHAR(100)    NOT NULL,
    `created_at`       TIMESTAMP       NULL DEFAULT NULL,
    `updated_at`       TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `surgical_team_members_surgical_team_id_foreign` (`surgical_team_id`),
    KEY `surgical_team_members_user_id_foreign`          (`user_id`),
    CONSTRAINT `surgical_team_members_surgical_team_id_foreign` FOREIGN KEY (`surgical_team_id`) REFERENCES `surgical_teams` (`id`) ON DELETE CASCADE  ON UPDATE CASCADE,
    CONSTRAINT `surgical_team_members_user_id_foreign`          FOREIGN KEY (`user_id`)          REFERENCES `users`          (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `surgery_schedules` (
    `id`                 BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `surgery_request_id` BIGINT UNSIGNED NOT NULL,
    `operating_room`     VARCHAR(50)     NOT NULL,
    `scheduled_start`    DATETIME        NOT NULL,
    `scheduled_end`      DATETIME        NOT NULL,
    `status`             ENUM('Scheduled', 'In Progress', 'Completed', 'Cancelled') NOT NULL DEFAULT 'Scheduled',
    `scheduled_by`       BIGINT UNSIGNED NOT NULL,
    `created_at`         TIMESTAMP       NULL DEFAULT NULL,
    `updated_at`         TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `surgery_schedules_surgery_request_id_foreign` (`surgery_request_id`),
    KEY `surgery_schedules_scheduled_by_foreign`       (`scheduled_by`),
    CONSTRAINT `surgery_schedules_scheduled_by_foreign`       FOREIGN KEY (`scheduled_by`)       REFERENCES `users`            (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `surgery_schedules_surgery_request_id_foreign` FOREIGN KEY (`surgery_request_id`) REFERENCES `surgery_requests` (`id`) ON DELETE CASCADE  ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `diet_requests` (
    `id`           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `request_no`   VARCHAR(50)     NOT NULL,
    `patient_id`   BIGINT UNSIGNED NOT NULL,
    `doctor_id`    BIGINT UNSIGNED NOT NULL,
    `diet_type`    VARCHAR(100)    NOT NULL,
    `caloric_target` INT UNSIGNED   NULL DEFAULT NULL,
    `restrictions` TEXT            NULL DEFAULT NULL,
    `status`       ENUM('Pending', 'Active', 'Discontinued') NOT NULL DEFAULT 'Pending',
    `created_at`   TIMESTAMP       NULL DEFAULT NULL,
    `updated_at`   TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `diet_requests_request_no_unique` (`request_no`),
    KEY `diet_requests_patient_id_foreign` (`patient_id`),
    KEY `diet_requests_doctor_id_foreign`  (`doctor_id`),
    CONSTRAINT `diet_requests_doctor_id_foreign`  FOREIGN KEY (`doctor_id`)  REFERENCES `users`    (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `diet_requests_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE  ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `diet_plans` (
    `id`              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `diet_request_id` BIGINT UNSIGNED NOT NULL,
    `dietitian_id`    BIGINT UNSIGNED NOT NULL,
    `meal_period`     ENUM('Breakfast', 'Lunch', 'Dinner', 'Snack', 'Full Day') NOT NULL DEFAULT 'Full Day',
    `menu_details`    TEXT            NOT NULL,
    `instructions`    TEXT            NULL DEFAULT NULL,
    `created_at`      TIMESTAMP       NULL DEFAULT NULL,
    `updated_at`      TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `diet_plans_diet_request_id_foreign` (`diet_request_id`),
    KEY `diet_plans_dietitian_id_foreign`    (`dietitian_id`),
    CONSTRAINT `diet_plans_diet_request_id_foreign` FOREIGN KEY (`diet_request_id`) REFERENCES `diet_requests` (`id`) ON DELETE CASCADE  ON UPDATE CASCADE,
    CONSTRAINT `diet_plans_dietitian_id_foreign`    FOREIGN KEY (`dietitian_id`)    REFERENCES `users`         (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `notifications` (
    `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`    BIGINT UNSIGNED NOT NULL,
    `type`       VARCHAR(100)    NOT NULL,
    `title`      VARCHAR(255)    NOT NULL,
    `message`    TEXT            NOT NULL,
    `is_read`    TINYINT(1)      NOT NULL DEFAULT 0,
    `link`       VARCHAR(512)    NULL DEFAULT NULL,
    `created_at` TIMESTAMP       NULL DEFAULT NULL,
    `updated_at` TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `notifications_user_id_foreign` (`user_id`),
    CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `conversations` (
    `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title`      VARCHAR(255)    NULL DEFAULT NULL,
    `is_group`   TINYINT(1)      NOT NULL DEFAULT 0,
    `created_by` BIGINT UNSIGNED NULL DEFAULT NULL,
    `created_at` TIMESTAMP       NULL DEFAULT NULL,
    `updated_at` TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `conversations_created_by_foreign` (`created_by`),
    CONSTRAINT `conversations_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `conversation_participants` (
    `id`              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `conversation_id` BIGINT UNSIGNED NOT NULL,
    `user_id`         BIGINT UNSIGNED NOT NULL,
    `joined_at`       TIMESTAMP       NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `conversation_participants_unique` (`conversation_id`, `user_id`),
    KEY `conversation_participants_user_id_foreign` (`user_id`),
    CONSTRAINT `conversation_participants_conversation_id_foreign` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `conversation_participants_user_id_foreign`         FOREIGN KEY (`user_id`)         REFERENCES `users`         (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `messages` (
    `id`              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `conversation_id` BIGINT UNSIGNED NOT NULL,
    `sender_id`       BIGINT UNSIGNED NOT NULL,
    `body`            TEXT            NOT NULL,
    `is_read`         TINYINT(1)      NOT NULL DEFAULT 0,
    `created_at`      TIMESTAMP       NULL DEFAULT NULL,
    `updated_at`      TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `messages_conversation_id_foreign` (`conversation_id`),
    KEY `messages_sender_id_foreign`       (`sender_id`),
    CONSTRAINT `messages_conversation_id_foreign` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `messages_sender_id_foreign`       FOREIGN KEY (`sender_id`)       REFERENCES `users`         (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `activity_logs` (
    `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`       BIGINT UNSIGNED NULL DEFAULT NULL,
    `action`        VARCHAR(100)    NOT NULL,
    `module`        VARCHAR(100)    NOT NULL,
    `severity`      ENUM('info', 'warning', 'critical') NOT NULL DEFAULT 'info',
    `result`        ENUM('success', 'failure')          NOT NULL DEFAULT 'success',
    `description`   TEXT            NOT NULL,
    `ip_address`    VARCHAR(45)     NULL DEFAULT NULL,
    `loggable_type` VARCHAR(255)    NULL DEFAULT NULL,
    `loggable_id`   BIGINT UNSIGNED NULL DEFAULT NULL,
    `logged_at`     TIMESTAMP       NOT NULL,
    PRIMARY KEY (`id`),
    KEY `activity_logs_user_id_foreign` (`user_id`),
    KEY `idx_activity_logs_action`      (`action`),
    KEY `idx_activity_logs_module`      (`module`),
    KEY `idx_activity_logs_logged_at`   (`logged_at`),
    CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `medisense_interactions` (
    `id`                 BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`            BIGINT UNSIGNED NULL DEFAULT NULL,
    `patient_id`         BIGINT UNSIGNED NULL DEFAULT NULL,
    `prompt`             TEXT            NOT NULL,
    `response`           LONGTEXT        NOT NULL,
    `clinical_context`   JSON            NULL DEFAULT NULL,
    `model_version`      VARCHAR(50)     NOT NULL DEFAULT 'medisense-v1',
    `processing_time_ms` INT UNSIGNED    NULL DEFAULT NULL,
    `disclaimer_accepted` TINYINT(1)     NOT NULL DEFAULT 1,
    `created_at`         TIMESTAMP       NULL DEFAULT NULL,
    `updated_at`         TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `medisense_interactions_user_id_foreign`    (`user_id`),
    KEY `medisense_interactions_patient_id_foreign` (`patient_id`),
    CONSTRAINT `medisense_interactions_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE SET NULL,
    CONSTRAINT `medisense_interactions_user_id_foreign`    FOREIGN KEY (`user_id`)    REFERENCES `users`    (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
