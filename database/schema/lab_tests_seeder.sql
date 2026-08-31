-- ============================================================================
-- DITC HIMS — Laboratory Test Categories & Tests Data Seeder
-- MySQL 8.x Compatible
--
-- PURPOSE:
--   Re-populates lab_test_categories and lab_tests on a fresh or empty
--   Railway production database that already has the schema from
--   ditc_hms_schema.sql applied.
--
-- SOURCE:
--   Recovered from git commit 0654805 (LabTestCategorySeeder.php &
--   LabTestSeeder.php), cross-validated against the working local data
--   and the production HTML rendering 22 tests.
--
-- USAGE:
--   File → Run SQL Script in MySQL Workbench targeting database: railway
--   Then verify with:
--     SELECT COUNT(*) FROM lab_test_categories;  -- expect 8
--     SELECT COUNT(*) FROM lab_tests;            -- expect 22
--
-- SAFE:
--   Uses INSERT ... ON DUPLICATE KEY UPDATE with the unique key on `code`.
--   Will INSERT missing records and silently no-op on existing identical
--   records. Will NOT drop, truncate, or reset any table.
-- ============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ────────────────────────────────────────────────────────────────────────────
-- 1. LABORATORY TEST CATEGORIES
-- ────────────────────────────────────────────────────────────────────────────
INSERT INTO `lab_test_categories`
    (`id`, `name`, `code`, `description`, `is_active`, `created_at`, `updated_at`)
VALUES
    (1, 'Hematology',            'HEM',   'Blood cell counts and related tests',          1, NOW(), NOW()),
    (2, 'Clinical Chemistry',    'CHEM',  'Biochemical analysis of blood and serum',      1, NOW(), NOW()),
    (3, 'Microbiology',          'MICRO', 'Culture and sensitivity tests',                1, NOW(), NOW()),
    (4, 'Urinalysis',            'URI',   'Urine analysis and microscopy',                1, NOW(), NOW()),
    (5, 'Serology / Immunology', 'SERO',  'Antibody and antigen tests',                  1, NOW(), NOW()),
    (6, 'Coagulation',           'COAG',  'Clotting and bleeding time tests',             1, NOW(), NOW()),
    (7, 'Hormones / Endocrine',  'ENDO',  'Thyroid, hormonal panels',                    1, NOW(), NOW()),
    (8, 'Blood Banking',         'BB',    'Blood type, crossmatch and transfusion tests', 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE
    `name`        = VALUES(`name`),
    `description` = VALUES(`description`),
    `is_active`   = VALUES(`is_active`),
    `updated_at`  = NOW();

-- ────────────────────────────────────────────────────────────────────────────
-- 2. LABORATORY TESTS
--    FK:  lab_test_category_id → lab_test_categories.id
--    Unique key on: code
-- ────────────────────────────────────────────────────────────────────────────
INSERT INTO `lab_tests`
    (`id`, `lab_test_category_id`, `name`, `code`, `normal_range`, `unit`, `method`, `price`, `is_active`, `created_at`, `updated_at`)
VALUES
    -- ── Hematology (category_id = 1) ────────────────────────────────────
    (1,  1, 'Complete Blood Count (CBC)',           'CBC',   '4.5–11.0',      'x10³/µL', 'Automated', 250.00, 1, NOW(), NOW()),
    (2,  1, 'Hemoglobin',                           'HGB',   '12.0–17.5',     'g/dL',    'Automated', 150.00, 1, NOW(), NOW()),
    (3,  1, 'Hematocrit',                           'HCT',   '36–50',         '%',       'Automated', 150.00, 1, NOW(), NOW()),
    (4,  1, 'Platelet Count',                       'PLT',   '150–400',       'x10³/µL', 'Automated', 200.00, 1, NOW(), NOW()),
    (5,  1, 'Erythrocyte Sedimentation Rate',       'ESR',   '0–20',          'mm/hr',   'Automated', 180.00, 1, NOW(), NOW()),

    -- ── Clinical Chemistry (category_id = 2) ────────────────────────────
    (6,  2, 'Fasting Blood Sugar (FBS)',            'FBS',   '70–99',         'mg/dL',   'Automated', 150.00, 1, NOW(), NOW()),
    (7,  2, 'Blood Urea Nitrogen (BUN)',            'BUN',   '7–20',          'mg/dL',   'Automated', 200.00, 1, NOW(), NOW()),
    (8,  2, 'Serum Creatinine',                     'CREA',  '0.7–1.2',       'mg/dL',   'Automated', 200.00, 1, NOW(), NOW()),
    (9,  2, 'SGPT (ALT)',                           'SGPT',  '7–56',          'U/L',     'Automated', 250.00, 1, NOW(), NOW()),
    (10, 2, 'SGOT (AST)',                           'SGOT',  '10–40',         'U/L',     'Automated', 250.00, 1, NOW(), NOW()),
    (11, 2, 'Total Cholesterol',                    'CHOL',  '<200',          'mg/dL',   'Automated', 280.00, 1, NOW(), NOW()),
    (12, 2, 'Triglycerides',                        'TRIG',  '<150',          'mg/dL',   'Automated', 280.00, 1, NOW(), NOW()),
    (13, 2, 'Uric Acid',                            'UA',    '3.5–7.2',       'mg/dL',   'Automated', 200.00, 1, NOW(), NOW()),

    -- ── Urinalysis (category_id = 4) ────────────────────────────────────
    (14, 4, 'Routine Urinalysis',                   'RUA',   'Refer to form', '',        'Automated', 120.00, 1, NOW(), NOW()),
    (15, 4, 'Urine Culture & Sensitivity',          'UCS',   'No growth',     '',        'Automated', 450.00, 1, NOW(), NOW()),

    -- ── Serology / Immunology (category_id = 5) ─────────────────────────
    (16, 5, 'Hepatitis B Surface Antigen (HBsAg)',  'HBSAG', 'Non-reactive',  '',        'Automated', 350.00, 1, NOW(), NOW()),
    (17, 5, 'Anti-HIV',                             'HIV',   'Non-reactive',  '',        'Automated', 450.00, 1, NOW(), NOW()),
    (18, 5, 'Dengue NS1 Antigen',                   'NS1',   'Negative',      '',        'Automated', 600.00, 1, NOW(), NOW()),

    -- ── Coagulation (category_id = 6) ───────────────────────────────────
    (19, 6, 'Prothrombin Time (PT)',                'PT',    '11–13',         'seconds', 'Automated', 300.00, 1, NOW(), NOW()),
    (20, 6, 'Activated Partial Thromboplastin Time','APTT',  '25–35',         'seconds', 'Automated', 300.00, 1, NOW(), NOW()),

    -- ── Hormones / Endocrine (category_id = 7) ──────────────────────────
    (21, 7, 'Thyroid Stimulating Hormone (TSH)',    'TSH',   '0.4–4.0',       'mIU/L',  'Automated', 550.00, 1, NOW(), NOW()),
    (22, 7, 'Free T4 (FT4)',                        'FT4',   '0.8–1.8',       'ng/dL',  'Automated', 550.00, 1, NOW(), NOW())

ON DUPLICATE KEY UPDATE
    `lab_test_category_id` = VALUES(`lab_test_category_id`),
    `name`                 = VALUES(`name`),
    `normal_range`         = VALUES(`normal_range`),
    `unit`                 = VALUES(`unit`),
    `method`               = VALUES(`method`),
    `price`                = VALUES(`price`),
    `is_active`            = VALUES(`is_active`),
    `updated_at`           = NOW();

SET FOREIGN_KEY_CHECKS = 1;

-- ────────────────────────────────────────────────────────────────────────────
-- VERIFICATION QUERIES (run after import to confirm)
-- ────────────────────────────────────────────────────────────────────────────
-- SELECT COUNT(*) AS category_count FROM lab_test_categories;  -- expect 8
-- SELECT COUNT(*) AS test_count FROM lab_tests;                -- expect 22
-- SELECT id, lab_test_category_id, name, code FROM lab_tests ORDER BY id;
