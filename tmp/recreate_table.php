<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

Schema::dropIfExists('medisense_interactions');

DB::statement("CREATE TABLE `medisense_interactions` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NULL,
    `patient_id` BIGINT UNSIGNED NULL,
    `task` VARCHAR(100) NOT NULL,
    `provider` VARCHAR(100) NOT NULL,
    `status` VARCHAR(50) NOT NULL,
    `result_summary` TEXT NULL,
    `ip_address` VARCHAR(45) NULL,
    `logged_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    CONSTRAINT `medisense_interactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
    CONSTRAINT `medisense_interactions_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE SET NULL,
    INDEX `idx_medisense_user` (`user_id`),
    INDEX `idx_medisense_patient` (`patient_id`),
    INDEX `idx_medisense_task` (`task`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

echo "Recreated table medisense_interactions successfully!\n";
$cols = Schema::getColumnListing('medisense_interactions');
echo "Columns: " . implode(', ', $cols) . "\n";
