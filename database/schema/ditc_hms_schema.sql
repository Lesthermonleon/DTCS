-- ============================================================================
-- DITC Hospital Management System — Complete Database Schema
-- MySQL 8.x Compatible · Laravel Synchronized
-- Generated: 2026-08-04
--
-- Import into MySQL Workbench via: File → Run SQL Script
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
    `id`            VARCHAR(255)    NOT NULL,
    `user_id`       BIGINT UNSIGNED NULL DEFAULT NULL,
    `ip_address`    VARCHAR(45)     NULL DEFAULT NULL,
    `user_agent`    TEXT            NULL,
    `payload`       LONGTEXT        NOT NULL,
    `last_activity` INT             NOT NULL,
    PRIMARY KEY (`id`),
    KEY `sessions_user_id_index` (`user_id`),
    KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `cache` (
    `key`        VARCHAR(255) NOT NULL,
    `value`      MEDIUMTEXT   NOT NULL,
    `expiration` INT          NOT NULL,
    PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `cache_locks` (
    `key`        VARCHAR(255) NOT NULL,
    `owner`      VARCHAR(255) NOT NULL,
    `expiration` INT          NOT NULL,
    PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `jobs` (
    `id`           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `queue`        VARCHAR(255)    NOT NULL,
    `payload`      LONGTEXT        NOT NULL,
    `attempts`     TINYINT UNSIGNED NOT NULL,
    `reserved_at`  INT UNSIGNED    NULL DEFAULT NULL,
    `available_at` INT UNSIGNED    NOT NULL,
    `created_at`   INT UNSIGNED    NOT NULL,
    PRIMARY KEY (`id`),
    KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `job_batches` (
    `id`             VARCHAR(255) NOT NULL,
    `name`           VARCHAR(255) NOT NULL,
    `total_jobs`     INT          NOT NULL,
    `pending_jobs`   INT          NOT NULL,
    `failed_jobs`    INT          NOT NULL,
    `failed_job_ids` LONGTEXT     NOT NULL,
    `options`        MEDIUMTEXT   NULL,
    `cancelled_at`   INT          NULL DEFAULT NULL,
    `created_at`     INT          NOT NULL,
    `finished_at`    INT          NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `failed_jobs` (
    `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `uuid`       VARCHAR(255)    NOT NULL,
    `connection` TEXT            NOT NULL,
    `queue`      TEXT            NOT NULL,
    `payload`    LONGTEXT        NOT NULL,
    `exception`  LONGTEXT        NOT NULL,
    `failed_at`  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ════════════════════════════════════════════════════════════════════════════
-- RBAC TABLES
-- ════════════════════════════════════════════════════════════════════════════

CREATE TABLE IF NOT EXISTS `roles` (
    `id`              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`            VARCHAR(255)    NOT NULL,
    `slug`            VARCHAR(255)    NOT NULL,
    `dashboard_route` VARCHAR(100)    NULL DEFAULT NULL COMMENT 'Named route to redirect after login',
    `description`     VARCHAR(255)    NULL DEFAULT NULL,
    `created_at`      TIMESTAMP       NULL DEFAULT NULL,
    `updated_at`      TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `roles_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `permissions` (
    `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`       VARCHAR(255)    NOT NULL,
    `slug`       VARCHAR(255)    NOT NULL,
    `module`     VARCHAR(255)    NOT NULL,
    `created_at` TIMESTAMP       NULL DEFAULT NULL,
    `updated_at` TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `permissions_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `role_user` (
    `user_id` BIGINT UNSIGNED NOT NULL,
    `role_id` BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (`user_id`, `role_id`),
    KEY `role_user_role_id_foreign` (`role_id`),
    CONSTRAINT `role_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `role_user_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `permission_role` (
    `permission_id` BIGINT UNSIGNED NOT NULL,
    `role_id`       BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (`permission_id`, `role_id`),
    KEY `permission_role_role_id_foreign` (`role_id`),
    CONSTRAINT `permission_role_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `permission_role_role_id_foreign`       FOREIGN KEY (`role_id`)       REFERENCES `roles` (`id`)       ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ════════════════════════════════════════════════════════════════════════════
-- PATIENT HUB
-- ════════════════════════════════════════════════════════════════════════════

CREATE TABLE IF NOT EXISTS `patients` (
    `id`                      BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `patient_no`              VARCHAR(255)    NOT NULL,
    `first_name`              VARCHAR(255)    NOT NULL,
    `last_name`               VARCHAR(255)    NOT NULL,
    `middle_name`             VARCHAR(255)    NULL DEFAULT NULL,
    `date_of_birth`           DATE            NOT NULL,
    `gender`                  ENUM('Male','Female','Other') NOT NULL,
    `blood_type`              VARCHAR(5)      NULL DEFAULT NULL,
    `address`                 TEXT            NULL,
    `phone`                   VARCHAR(20)     NULL DEFAULT NULL,
    `email`                   VARCHAR(255)    NULL DEFAULT NULL,
    `emergency_contact_name`  VARCHAR(255)    NULL DEFAULT NULL,
    `emergency_contact_phone` VARCHAR(20)     NULL DEFAULT NULL,
    `patient_type`            ENUM('Inpatient','Outpatient') NOT NULL DEFAULT 'Outpatient',
    `ward`                    VARCHAR(255)    NULL DEFAULT NULL,
    `bed_number`              VARCHAR(255)    NULL DEFAULT NULL,
    `created_at`              TIMESTAMP       NULL DEFAULT NULL,
    `updated_at`              TIMESTAMP       NULL DEFAULT NULL,
    `deleted_at`              TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `patients_patient_no_unique` (`patient_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ════════════════════════════════════════════════════════════════════════════
-- LIS — LABORATORY INFORMATION SYSTEM
-- ════════════════════════════════════════════════════════════════════════════

CREATE TABLE IF NOT EXISTS `lab_test_categories` (
    `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`        VARCHAR(255)    NOT NULL,
    `code`        VARCHAR(255)    NOT NULL,
    `description` VARCHAR(255)    NULL DEFAULT NULL,
    `is_active`   TINYINT(1)      NOT NULL DEFAULT 1,
    `created_at`  TIMESTAMP       NULL DEFAULT NULL,
    `updated_at`  TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `lab_test_categories_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `lab_tests` (
    `id`                    BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `lab_test_category_id`  BIGINT UNSIGNED NOT NULL,
    `name`                  VARCHAR(255)    NOT NULL,
    `code`                  VARCHAR(255)    NOT NULL,
    `normal_range`          VARCHAR(255)    NULL DEFAULT NULL,
    `unit`                  VARCHAR(255)    NULL DEFAULT NULL,
    `method`                VARCHAR(255)    NULL DEFAULT NULL,
    `price`                 DECIMAL(8,2)    NULL DEFAULT NULL,
    `is_active`             TINYINT(1)      NOT NULL DEFAULT 1,
    `created_at`            TIMESTAMP       NULL DEFAULT NULL,
    `updated_at`            TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `lab_tests_code_unique` (`code`),
    KEY `lab_tests_lab_test_category_id_foreign` (`lab_test_category_id`),
    CONSTRAINT `lab_tests_lab_test_category_id_foreign` FOREIGN KEY (`lab_test_category_id`) REFERENCES `lab_test_categories` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `lab_requests` (
    `id`             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `request_no`     VARCHAR(255)    NOT NULL,
    `patient_id`     BIGINT UNSIGNED NOT NULL,
    `doctor_id`      BIGINT UNSIGNED NOT NULL,
    `priority`       ENUM('Routine','Urgent','STAT') NOT NULL DEFAULT 'Routine',
    `status`         ENUM('Pending','In Progress','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
    `clinical_notes` TEXT            NULL,
    `specimen_type`  VARCHAR(255)    NULL DEFAULT NULL,
    `requested_at`   TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `received_at`    TIMESTAMP       NULL DEFAULT NULL,
    `completed_at`   TIMESTAMP       NULL DEFAULT NULL,
    `created_at`     TIMESTAMP       NULL DEFAULT NULL,
    `updated_at`     TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `lab_requests_request_no_unique` (`request_no`),
    KEY `lab_requests_patient_id_foreign` (`patient_id`),
    KEY `lab_requests_doctor_id_foreign` (`doctor_id`),
    KEY `lab_requests_status_index` (`status`),
    KEY `idx_lab_req_doctor_status` (`doctor_id`, `status`),
    CONSTRAINT `lab_requests_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `lab_requests_doctor_id_foreign`  FOREIGN KEY (`doctor_id`)  REFERENCES `users` (`id`)    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `lab_request_items` (
    `id`              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `lab_request_id`  BIGINT UNSIGNED NOT NULL,
    `lab_test_id`     BIGINT UNSIGNED NOT NULL,
    `status`          ENUM('Pending','In Progress','Completed') NOT NULL DEFAULT 'Pending',
    `created_at`      TIMESTAMP       NULL DEFAULT NULL,
    `updated_at`      TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `lab_request_items_lab_request_id_foreign` (`lab_request_id`),
    KEY `lab_request_items_lab_test_id_foreign` (`lab_test_id`),
    KEY `lab_request_items_status_index` (`status`),
    CONSTRAINT `lab_request_items_lab_request_id_foreign` FOREIGN KEY (`lab_request_id`) REFERENCES `lab_requests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `lab_request_items_lab_test_id_foreign`    FOREIGN KEY (`lab_test_id`)    REFERENCES `lab_tests` (`id`)    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `lab_results` (
    `id`                  BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `lab_request_item_id` BIGINT UNSIGNED NOT NULL,
    `technologist_id`     BIGINT UNSIGNED NULL DEFAULT NULL,
    `result_value`        VARCHAR(255)    NULL DEFAULT NULL,
    `remarks`             TEXT            NULL,
    `status`              ENUM('Pending','Encoded','Validated','Released') NOT NULL DEFAULT 'Pending',
    `validated_by`        BIGINT UNSIGNED NULL DEFAULT NULL,
    `released_by`         BIGINT UNSIGNED NULL DEFAULT NULL,
    `validated_at`        TIMESTAMP       NULL DEFAULT NULL,
    `released_at`         TIMESTAMP       NULL DEFAULT NULL,
    `created_at`          TIMESTAMP       NULL DEFAULT NULL,
    `updated_at`          TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `lab_results_lab_request_item_id_foreign` (`lab_request_item_id`),
    KEY `lab_results_technologist_id_index` (`technologist_id`),
    KEY `lab_results_validated_by_index` (`validated_by`),
    KEY `lab_results_released_by_index` (`released_by`),
    CONSTRAINT `lab_results_lab_request_item_id_foreign` FOREIGN KEY (`lab_request_item_id`) REFERENCES `lab_request_items` (`id`) ON DELETE CASCADE  ON UPDATE CASCADE,
    CONSTRAINT `lab_results_technologist_id_foreign`      FOREIGN KEY (`technologist_id`)     REFERENCES `users` (`id`)              ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `lab_results_validated_by_foreign`         FOREIGN KEY (`validated_by`)         REFERENCES `users` (`id`)              ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `lab_results_released_by_foreign`          FOREIGN KEY (`released_by`)          REFERENCES `users` (`id`)              ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ════════════════════════════════════════════════════════════════════════════
-- RIS — RADIOLOGY INFORMATION SYSTEM
-- ════════════════════════════════════════════════════════════════════════════

CREATE TABLE IF NOT EXISTS `radiology_requests` (
    `id`                     BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `request_no`             VARCHAR(255)    NOT NULL,
    `patient_id`             BIGINT UNSIGNED NOT NULL,
    `doctor_id`              BIGINT UNSIGNED NOT NULL,
    `modality`               VARCHAR(255)    NOT NULL,
    `body_part`              VARCHAR(255)    NOT NULL,
    `clinical_information`   TEXT            NULL,
    `priority`               ENUM('Routine','Urgent','STAT') NOT NULL DEFAULT 'Routine',
    `status`                 ENUM('Pending','Scheduled','In Progress','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
    `requested_at`           TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `scheduled_at`           TIMESTAMP       NULL DEFAULT NULL,
    `completed_at`           TIMESTAMP       NULL DEFAULT NULL,
    `created_at`             TIMESTAMP       NULL DEFAULT NULL,
    `updated_at`             TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `radiology_requests_request_no_unique` (`request_no`),
    KEY `radiology_requests_patient_id_foreign` (`patient_id`),
    KEY `radiology_requests_doctor_id_foreign` (`doctor_id`),
    KEY `radiology_requests_status_index` (`status`),
    KEY `idx_rad_req_doctor_status` (`doctor_id`, `status`),
    CONSTRAINT `radiology_requests_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `radiology_requests_doctor_id_foreign`  FOREIGN KEY (`doctor_id`)  REFERENCES `users` (`id`)    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `radiology_images` (
    `id`                    BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `radiology_request_id`  BIGINT UNSIGNED NOT NULL,
    `file_path`             VARCHAR(255)    NOT NULL,
    `file_name`             VARCHAR(255)    NOT NULL,
    `file_type`             VARCHAR(20)     NULL DEFAULT NULL,
    `file_size`             BIGINT UNSIGNED NULL DEFAULT NULL,
    `uploaded_by`           BIGINT UNSIGNED NOT NULL,
    `uploaded_at`           TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `notes`                 TEXT            NULL,
    `created_at`            TIMESTAMP       NULL DEFAULT NULL,
    `updated_at`            TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `radiology_images_radiology_request_id_foreign` (`radiology_request_id`),
    KEY `radiology_images_uploaded_by_index` (`uploaded_by`),
    CONSTRAINT `radiology_images_radiology_request_id_foreign` FOREIGN KEY (`radiology_request_id`) REFERENCES `radiology_requests` (`id`) ON DELETE CASCADE  ON UPDATE CASCADE,
    CONSTRAINT `radiology_images_uploaded_by_foreign`           FOREIGN KEY (`uploaded_by`)           REFERENCES `users` (`id`)              ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `radiology_reports` (
    `id`                    BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `radiology_request_id`  BIGINT UNSIGNED NOT NULL,
    `radiologist_id`        BIGINT UNSIGNED NOT NULL,
    `findings`              TEXT            NOT NULL,
    `impression`            TEXT            NOT NULL,
    `recommendations`       TEXT            NULL,
    `status`                ENUM('Draft','Approved','Released') NOT NULL DEFAULT 'Draft',
    `approved_by`           BIGINT UNSIGNED NULL DEFAULT NULL,
    `released_by`           BIGINT UNSIGNED NULL DEFAULT NULL,
    `approved_at`           TIMESTAMP       NULL DEFAULT NULL,
    `released_at`           TIMESTAMP       NULL DEFAULT NULL,
    `created_at`            TIMESTAMP       NULL DEFAULT NULL,
    `updated_at`            TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `radiology_reports_radiology_request_id_unique` (`radiology_request_id`),
    KEY `radiology_reports_radiologist_id_index` (`radiologist_id`),
    KEY `radiology_reports_approved_by_index` (`approved_by`),
    KEY `radiology_reports_released_by_index` (`released_by`),
    CONSTRAINT `radiology_reports_radiology_request_id_foreign` FOREIGN KEY (`radiology_request_id`) REFERENCES `radiology_requests` (`id`) ON DELETE CASCADE  ON UPDATE CASCADE,
    CONSTRAINT `radiology_reports_radiologist_id_foreign`        FOREIGN KEY (`radiologist_id`)        REFERENCES `users` (`id`)              ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `radiology_reports_approved_by_foreign`           FOREIGN KEY (`approved_by`)           REFERENCES `users` (`id`)              ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `radiology_reports_released_by_foreign`           FOREIGN KEY (`released_by`)           REFERENCES `users` (`id`)              ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ════════════════════════════════════════════════════════════════════════════
-- PMS — PHARMACY MANAGEMENT SYSTEM
-- ════════════════════════════════════════════════════════════════════════════

CREATE TABLE IF NOT EXISTS `prescriptions` (
    `id`              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `prescription_no` VARCHAR(255)    NOT NULL,
    `patient_id`      BIGINT UNSIGNED NOT NULL,
    `doctor_id`       BIGINT UNSIGNED NOT NULL,
    `status`          ENUM('Pending','Verified','Partially Dispensed','Dispensed','Cancelled') NOT NULL DEFAULT 'Pending',
    `notes`           TEXT            NULL,
    `diagnosis`       VARCHAR(255)    NULL DEFAULT NULL,
    `prescribed_at`   TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `verified_by`     BIGINT UNSIGNED NULL DEFAULT NULL,
    `verified_at`     TIMESTAMP       NULL DEFAULT NULL,
    `created_at`      TIMESTAMP       NULL DEFAULT NULL,
    `updated_at`      TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `prescriptions_prescription_no_unique` (`prescription_no`),
    KEY `prescriptions_patient_id_foreign` (`patient_id`),
    KEY `prescriptions_doctor_id_foreign` (`doctor_id`),
    KEY `prescriptions_status_index` (`status`),
    KEY `prescriptions_verified_by_index` (`verified_by`),
    KEY `idx_rx_doctor_status` (`doctor_id`, `status`),
    CONSTRAINT `prescriptions_patient_id_foreign`  FOREIGN KEY (`patient_id`)  REFERENCES `patients` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `prescriptions_doctor_id_foreign`   FOREIGN KEY (`doctor_id`)   REFERENCES `users` (`id`)    ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `prescriptions_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`)    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `prescription_items` (
    `id`              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `prescription_id` BIGINT UNSIGNED NOT NULL,
    `medication_name` VARCHAR(255)    NOT NULL,
    `dosage`          VARCHAR(255)    NOT NULL,
    `route`           VARCHAR(255)    NULL DEFAULT NULL,
    `frequency`       VARCHAR(255)    NOT NULL,
    `duration`        VARCHAR(255)    NOT NULL,
    `quantity`        INT             NOT NULL,
    `instructions`    TEXT            NULL,
    `status`          ENUM('Pending','Dispensed') NOT NULL DEFAULT 'Pending',
    `created_at`      TIMESTAMP       NULL DEFAULT NULL,
    `updated_at`      TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `prescription_items_prescription_id_foreign` (`prescription_id`),
    KEY `prescription_items_status_index` (`status`),
    CONSTRAINT `prescription_items_prescription_id_foreign` FOREIGN KEY (`prescription_id`) REFERENCES `prescriptions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `dispensing_records` (
    `id`                    BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `prescription_item_id`  BIGINT UNSIGNED NOT NULL,
    `pharmacist_id`         BIGINT UNSIGNED NOT NULL,
    `quantity_dispensed`    INT             NOT NULL,
    `lot_number`           VARCHAR(255)    NULL DEFAULT NULL,
    `expiry_date`          DATE            NULL DEFAULT NULL,
    `notes`                TEXT            NULL,
    `dispensed_at`         TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `created_at`           TIMESTAMP       NULL DEFAULT NULL,
    `updated_at`           TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `dispensing_records_prescription_item_id_foreign` (`prescription_item_id`),
    KEY `dispensing_records_pharmacist_id_index` (`pharmacist_id`),
    CONSTRAINT `dispensing_records_prescription_item_id_foreign` FOREIGN KEY (`prescription_item_id`) REFERENCES `prescription_items` (`id`) ON DELETE CASCADE  ON UPDATE CASCADE,
    CONSTRAINT `dispensing_records_pharmacist_id_foreign`         FOREIGN KEY (`pharmacist_id`)         REFERENCES `users` (`id`)              ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ════════════════════════════════════════════════════════════════════════════
-- SORS — SURGICAL & OPERATING ROOM SYSTEM
-- ════════════════════════════════════════════════════════════════════════════

CREATE TABLE IF NOT EXISTS `operating_rooms` (
    `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`       VARCHAR(255)    NOT NULL,
    `location`   VARCHAR(255)    NULL DEFAULT NULL,
    `status`     ENUM('Available','Occupied','Under Maintenance') NOT NULL DEFAULT 'Available',
    `equipment`  TEXT            NULL,
    `is_active`  TINYINT(1)      NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP       NULL DEFAULT NULL,
    `updated_at` TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `operating_rooms_name_unique` (`name`),
    KEY `operating_rooms_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `surgery_requests` (
    `id`                 BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `request_no`         VARCHAR(255)    NOT NULL,
    `patient_id`         BIGINT UNSIGNED NOT NULL,
    `doctor_id`          BIGINT UNSIGNED NOT NULL,
    `procedure_name`     VARCHAR(255)    NOT NULL,
    `diagnosis`          VARCHAR(255)    NULL DEFAULT NULL,
    `urgency`            ENUM('Elective','Urgent','Emergency') NOT NULL DEFAULT 'Elective',
    `status`             ENUM('Pending','Scheduled','In Progress','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
    `notes`              TEXT            NULL,
    `anesthesia_type`    VARCHAR(255)    NULL DEFAULT NULL,
    `estimated_duration` INT             NULL DEFAULT NULL,
    `requested_at`       TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `created_at`         TIMESTAMP       NULL DEFAULT NULL,
    `updated_at`         TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `surgery_requests_request_no_unique` (`request_no`),
    KEY `surgery_requests_patient_id_foreign` (`patient_id`),
    KEY `surgery_requests_doctor_id_foreign` (`doctor_id`),
    KEY `surgery_requests_status_index` (`status`),
    KEY `idx_surg_req_doctor_status` (`doctor_id`, `status`),
    CONSTRAINT `surgery_requests_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `surgery_requests_doctor_id_foreign`  FOREIGN KEY (`doctor_id`)  REFERENCES `users` (`id`)    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `surgical_teams` (
    `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`       VARCHAR(255)    NOT NULL,
    `surgeon_id` BIGINT UNSIGNED NOT NULL,
    `notes`      TEXT            NULL,
    `is_active`  TINYINT(1)      NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP       NULL DEFAULT NULL,
    `updated_at` TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `surgical_teams_surgeon_id_index` (`surgeon_id`),
    CONSTRAINT `surgical_teams_surgeon_id_foreign` FOREIGN KEY (`surgeon_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `surgical_team_members` (
    `id`               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `surgical_team_id` BIGINT UNSIGNED NOT NULL,
    `user_id`          BIGINT UNSIGNED NOT NULL,
    `role_in_team`     VARCHAR(255)    NOT NULL,
    `created_at`       TIMESTAMP       NULL DEFAULT NULL,
    `updated_at`       TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `surgical_team_members_team_user_unique` (`surgical_team_id`, `user_id`),
    KEY `surgical_team_members_user_id_foreign` (`user_id`),
    CONSTRAINT `surgical_team_members_surgical_team_id_foreign` FOREIGN KEY (`surgical_team_id`) REFERENCES `surgical_teams` (`id`) ON DELETE CASCADE  ON UPDATE CASCADE,
    CONSTRAINT `surgical_team_members_user_id_foreign`           FOREIGN KEY (`user_id`)           REFERENCES `users` (`id`)          ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `surgery_schedules` (
    `id`                  BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `surgery_request_id`  BIGINT UNSIGNED NOT NULL,
    `operating_room_id`   BIGINT UNSIGNED NOT NULL,
    `surgical_team_id`    BIGINT UNSIGNED NOT NULL,
    `scheduled_by`        BIGINT UNSIGNED NOT NULL,
    `scheduled_at`        TIMESTAMP       NOT NULL,
    `duration_minutes`    INT             NOT NULL DEFAULT 60,
    `status`              ENUM('Scheduled','In Progress','Completed','Cancelled','Postponed') NOT NULL DEFAULT 'Scheduled',
    `notes`               TEXT            NULL,
    `created_at`          TIMESTAMP       NULL DEFAULT NULL,
    `updated_at`          TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `surgery_schedules_surgery_request_id_foreign` (`surgery_request_id`),
    KEY `surgery_schedules_operating_room_id_foreign` (`operating_room_id`),
    KEY `surgery_schedules_surgical_team_id_foreign` (`surgical_team_id`),
    KEY `surgery_schedules_scheduled_by_index` (`scheduled_by`),
    KEY `surgery_schedules_status_index` (`status`),
    KEY `idx_surg_sched_at_status` (`scheduled_at`, `status`),
    CONSTRAINT `surgery_schedules_surgery_request_id_foreign` FOREIGN KEY (`surgery_request_id`) REFERENCES `surgery_requests` (`id`) ON DELETE CASCADE  ON UPDATE CASCADE,
    CONSTRAINT `surgery_schedules_operating_room_id_foreign`  FOREIGN KEY (`operating_room_id`)  REFERENCES `operating_rooms` (`id`)  ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `surgery_schedules_surgical_team_id_foreign`   FOREIGN KEY (`surgical_team_id`)   REFERENCES `surgical_teams` (`id`)   ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `surgery_schedules_scheduled_by_foreign`       FOREIGN KEY (`scheduled_by`)       REFERENCES `users` (`id`)            ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ════════════════════════════════════════════════════════════════════════════
-- DNMS — DIET & NUTRITION MANAGEMENT SYSTEM
-- ════════════════════════════════════════════════════════════════════════════

CREATE TABLE IF NOT EXISTS `diet_requests` (
    `id`                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `request_no`        VARCHAR(255)    NOT NULL,
    `patient_id`        BIGINT UNSIGNED NOT NULL,
    `doctor_id`         BIGINT UNSIGNED NOT NULL,
    `diet_type`         VARCHAR(255)    NOT NULL,
    `allergies`         TEXT            NULL,
    `food_restrictions` TEXT            NULL,
    `clinical_notes`    TEXT            NULL,
    `status`            ENUM('Pending','Active','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
    `requested_at`      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `created_at`        TIMESTAMP       NULL DEFAULT NULL,
    `updated_at`        TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `diet_requests_request_no_unique` (`request_no`),
    KEY `diet_requests_patient_id_foreign` (`patient_id`),
    KEY `diet_requests_doctor_id_foreign` (`doctor_id`),
    KEY `diet_requests_status_index` (`status`),
    KEY `idx_diet_req_doctor_status` (`doctor_id`, `status`),
    CONSTRAINT `diet_requests_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `diet_requests_doctor_id_foreign`  FOREIGN KEY (`doctor_id`)  REFERENCES `users` (`id`)    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `diet_plans` (
    `id`              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `diet_request_id` BIGINT UNSIGNED NOT NULL,
    `dietitian_id`    BIGINT UNSIGNED NOT NULL,
    `plan_details`    TEXT            NOT NULL,
    `total_calories`  INT             NULL DEFAULT NULL,
    `protein_grams`   DECIMAL(8,2)    NULL DEFAULT NULL,
    `carb_grams`      DECIMAL(8,2)    NULL DEFAULT NULL,
    `fat_grams`       DECIMAL(8,2)    NULL DEFAULT NULL,
    `start_date`      DATE            NOT NULL,
    `end_date`        DATE            NULL DEFAULT NULL,
    `status`          ENUM('Active','Completed','Revised') NOT NULL DEFAULT 'Active',
    `notes`           TEXT            NULL,
    `created_at`      TIMESTAMP       NULL DEFAULT NULL,
    `updated_at`      TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `diet_plans_diet_request_id_unique` (`diet_request_id`),
    KEY `diet_plans_dietitian_id_index` (`dietitian_id`),
    KEY `diet_plans_status_index` (`status`),
    CONSTRAINT `diet_plans_diet_request_id_foreign` FOREIGN KEY (`diet_request_id`) REFERENCES `diet_requests` (`id`) ON DELETE CASCADE  ON UPDATE CASCADE,
    CONSTRAINT `diet_plans_dietitian_id_foreign`    FOREIGN KEY (`dietitian_id`)    REFERENCES `users` (`id`)          ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `meal_schedules` (
    `id`           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `diet_plan_id` BIGINT UNSIGNED NOT NULL,
    `meal_type`    ENUM('Breakfast','Mid-Morning Snack','Lunch','Afternoon Snack','Dinner','Bedtime Snack') NOT NULL,
    `meal_date`    DATE            NOT NULL,
    `menu`         TEXT            NOT NULL,
    `calories`     INT             NULL DEFAULT NULL,
    `is_served`    TINYINT(1)      NOT NULL DEFAULT 0,
    `notes`        TEXT            NULL,
    `created_at`   TIMESTAMP       NULL DEFAULT NULL,
    `updated_at`   TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `meal_schedules_diet_plan_id_foreign` (`diet_plan_id`),
    CONSTRAINT `meal_schedules_diet_plan_id_foreign` FOREIGN KEY (`diet_plan_id`) REFERENCES `diet_plans` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ════════════════════════════════════════════════════════════════════════════
-- AUDIT TRAIL
-- ════════════════════════════════════════════════════════════════════════════

CREATE TABLE IF NOT EXISTS `activity_logs` (
    `id`             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`        BIGINT UNSIGNED NULL DEFAULT NULL,
    `action`         VARCHAR(255)    NOT NULL,
    `module`         VARCHAR(255)    NOT NULL,
    `description`    TEXT            NULL,
    `loggable_type`  VARCHAR(255)    NULL DEFAULT NULL,
    `loggable_id`    BIGINT UNSIGNED NULL DEFAULT NULL,
    `ip_address`     VARCHAR(45)     NULL DEFAULT NULL,
    `logged_at`      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `created_at`     TIMESTAMP       NULL DEFAULT NULL,
    `updated_at`     TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `activity_logs_loggable_type_loggable_id_index` (`loggable_type`, `loggable_id`),
    KEY `idx_activity_user_created` (`user_id`, `created_at`),
    CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ════════════════════════════════════════════════════════════════════════════

SET FOREIGN_KEY_CHECKS = 1;
