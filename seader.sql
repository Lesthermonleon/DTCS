-- ============================================================================
-- DITC Hospital Management System — Comprehensive SQL Seeder (seader.sql)
-- MySQL 8.x Compatible · Standard SQL
-- 
-- CONTENTS:
--   1. 30 Patients with authentic Filipino names & demographic details
--   2. Laboratory Information System (LIS) — requests, items, results (ALL STATUSES)
--   3. Radiology Information System (RIS) — requests, images, reports (ALL STATUSES)
--   4. Pharmacy Management System (PMS) — prescriptions, items, dispensing records (ALL STATUSES)
--   5. Surgical & Operating Room System (SORS) — ORs, teams, requests, schedules (ALL STATUSES)
--   6. Diet & Nutrition Management System (DNMS) — requests, plans, meal schedules (ALL STATUSES)
-- ============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------------------------------------------------------
-- 1. PATIENTS SEEDER (30 Patients with Filipino Names)
-- ----------------------------------------------------------------------------
INSERT INTO `patients` (
    `id`, `patient_no`, `first_name`, `last_name`, `middle_name`,
    `date_of_birth`, `gender`, `blood_type`, `address`, `phone`, `email`,
    `emergency_contact_name`, `emergency_contact_phone`, `patient_type`,
    `ward`, `bed_number`, `clinical_findings`, `created_at`, `updated_at`
) VALUES
(1,  'PAT-2026-0001', 'Juan', 'Dela Cruz', 'Santos', '1985-04-12', 'Male', 'O+', '123 Mabini St, Sampaloc, Manila', '09171234001', 'juan.delacruz@gmail.com', 'Maria Dela Cruz', '09181234001', 'Inpatient', 'St. Luke Ward 3A', 'BED-301', 'Acute Appendicitis - RLQ tenderness, fever, elevated WBC count.', NOW(), NOW()),
(2,  'PAT-2026-0002', 'Maria Clara', 'Santos', 'Rizal', '1990-08-25', 'Female', 'A+', '45 Katipunan Ave, Loyola Heights, Quezon City', '09171234002', 'maria.santos@yahoo.com', 'Jose Santos', '09181234002', 'Outpatient', NULL, NULL, 'Essential Hypertension - Essential routine checkup, blood pressure 140/90 mmHg.', NOW(), NOW()),
(3,  'PAT-2026-0003', 'Jose', 'Reyes', 'Valdez', '1978-12-30', 'Male', 'B+', '78 Session Road, Baguio City', '09171234003', 'jose.reyes@gmail.com', 'Ana Reyes', '09181234003', 'Inpatient', 'ICU Unit 1', 'ICU-02', 'Community-Acquired Pneumonia - High grade fever, productive cough, crackles on auscultation.', NOW(), NOW()),
(4,  'PAT-2026-0004', 'Corazon', 'Mendoza', 'Aquino', '1965-01-25', 'Female', 'O-', '12 Colon Street, Cebu City', '09171234004', 'cora.mendoza@outlook.com', 'Beni Mendoza', '09181234004', 'Outpatient', NULL, NULL, 'Type 2 Diabetes Mellitus - Routine HbA1c monitor and glycemic control assessment.', NOW(), NOW()),
(5,  'PAT-2026-0005', 'Bayani', 'Garcia', 'Agbayani', '1992-06-15', 'Male', 'AB+', '88 EDSA, Wack-Wack, Mandaluyong City', '09171234005', 'bayani.garcia@gmail.com', 'Luz Garcia', '09181234005', 'Outpatient', NULL, NULL, 'Acute Gastroenteritis - Watery diarrhea, nausea, mild dehydration.', NOW(), NOW()),
(6,  'PAT-2026-0006', 'Fernando', 'Ramos', 'Poe', '1958-08-20', 'Male', 'A-', '56 Roxas Boulevard, Pasay City', '09171234006', 'fernando.ramos@gmail.com', 'Susan Ramos', '09181234006', 'Inpatient', 'Cardiac Care Unit', 'CCU-04', 'Congestive Heart Failure Stage III - Bilateral bipedal edema, dyspnea on exertion.', NOW(), NOW()),
(7,  'PAT-2026-0007', 'Angelica', 'Flores', 'Panganiban', '1995-11-04', 'Female', 'O+', '34 Taft Avenue, Malate, Manila', '09171234007', 'angelica.flores@gmail.com', 'Carlo Flores', '09181234007', 'Outpatient', NULL, NULL, 'Microcytic Hypochromic Anemia - Dizziness, pallor, low serum ferritin.', NOW(), NOW()),
(8,  'PAT-2026-0008', 'Joshua', 'Gonzales', 'Garcia', '1997-10-07', 'Male', 'A+', '102 JP Laurel St, Davao City', '09171234008', 'joshua.gonzales@gmail.com', 'Julia Gonzales', '09181234008', 'Inpatient', 'Orthopedic Ward B', 'BED-105', 'Closed Fracture Right Femur post-motorcycle accident.', NOW(), NOW()),
(9,  'PAT-2026-0009', 'Liza', 'Villanueva', 'Soberano', '1998-01-04', 'Female', 'B-', '19 Shaw Blvd, Pasig City', '09171234009', 'liza.villanueva@gmail.com', 'Enrique Villanueva', '09181234009', 'Outpatient', NULL, NULL, 'Moderate Perennial Allergic Rhinitis - Nasal congestion, sneezing, watery eyes.', NOW(), NOW()),
(10, 'PAT-2026-0010', 'Kathryn', 'Fernandez', 'Bernardo', '1996-03-26', 'Female', 'O+', '77 Commonwealth Ave, Quezon City', '09171234010', 'kathryn.fernandez@gmail.com', 'Daniel Fernandez', '09181234010', 'Inpatient', 'Surgical Ward A', 'BED-202', 'Acute Calculous Cholecystitis - RUQ pain radiating to right shoulder, positive Murphy sign.', NOW(), NOW()),
(11, 'PAT-2026-0011', 'Daniel', 'Navarro', 'Padilla', '1995-04-26', 'Male', 'AB-', '23 Alabang-Zapote Rd, Muntinlupa City', '09171234011', 'daniel.navarro@gmail.com', 'Karla Navarro', '09181234011', 'Outpatient', NULL, NULL, 'Bronchial Asthma in Mild Exacerbation - Wheezing, shortness of breath after upper respiratory infection.', NOW(), NOW()),
(12, 'PAT-2026-0012', 'Rodolfo', 'Quizon', 'Vera', '1970-07-25', 'Male', 'A+', '44 Espana Blvd, Sampaloc, Manila', '09171234012', 'rodolfo.quizon@gmail.com', 'Zsa Quizon', '09181234012', 'Inpatient', 'Surgical Ward B', 'BED-210', 'Right Inguinal Hernia - Reduceable bulge in right groin region.', NOW(), NOW()),
(13, 'PAT-2026-0013', 'Vic', 'Castelo', 'Sotto', '1968-04-28', 'Male', 'O+', '89 Ortigas Ave, San Juan City', '09171234013', 'vic.castelo@gmail.com', 'Pauleen Castelo', '09181234013', 'Outpatient', NULL, NULL, 'Hyperuricemia with Gouty Arthritis - Acute pain and erythema right 1st metatarsophalangeal joint.', NOW(), NOW()),
(14, 'PAT-2026-0014', 'Joey', 'Mercado', 'De Leon', '1969-10-14', 'Male', 'B+', '15 Timog Ave, Sacred Heart, Quezon City', '09171234014', 'joey.mercado@gmail.com', 'Eileen Mercado', '09181234014', 'Outpatient', NULL, NULL, 'Chronic Erosive Gastritis - Epigastric discomfort, postprandial fullness.', NOW(), NOW()),
(15, 'PAT-2026-0015', 'Andrea', 'Diaz', 'Brillantes', '2003-03-12', 'Female', 'A+', '67 Bonifacio High Street, BGC, Taguig City', '09171234015', 'andrea.diaz@gmail.com', 'Belle Diaz', '09181234015', 'Inpatient', 'Pediatric Isolation Ward', 'PED-03', 'Dengue Fever Warning Signs - High grade fever 4 days, petechiae, thrombocytopenia (PLT 85k).', NOW(), NOW()),
(16, 'PAT-2026-0016', 'Catriona', 'Rivera', 'Gray', '1994-01-06', 'Female', 'O+', '92 Ayala Avenue, Makati City', '09171234016', 'catriona.rivera@gmail.com', 'Sam Rivera', '09181234016', 'Outpatient', NULL, NULL, 'Episodic Migraine without Aura - Unilateral throbbing headache with photophobia.', NOW(), NOW()),
(17, 'PAT-2026-0017', 'Pia', 'Morales', 'Wurtzbach', '1989-09-24', 'Female', 'AB+', '31 McKinley Hill, Taguig City', '09171234017', 'pia.morales@gmail.com', 'Jeremy Morales', '09181234017', 'Inpatient', 'Gynecology Ward', 'OB-108', 'Benign Right Ovarian Cyst - Scheduled for laparoscopic cystectomy.', NOW(), NOW()),
(18, 'PAT-2026-0018', 'Emmanuel', 'Salazar', 'Pacquiao', '1978-12-17', 'Male', 'O+', '55 General Santos Highway, GenSan City', '09171234018', 'emmanuel.salazar@gmail.com', 'Jinkee Salazar', '09181234018', 'Outpatient', NULL, NULL, 'Right Rotator Cuff Tendinopathy - Shoulder joint pain during abduction.', NOW(), NOW()),
(19, 'PAT-2026-0019', 'Efren', 'Santiago', 'Reyes', '1954-08-26', 'Male', 'A+', '18 Angeles City Rd, Pampanga', '09171234019', 'efren.santiago@gmail.com', 'Bata Santiago', '09181234019', 'Inpatient', 'Neurology Ward 2', 'BED-205', 'Acute Ischemic Stroke MCA territory - Right hemiparesis, expressive dysphasia.', NOW(), NOW()),
(20, 'PAT-2026-0020', 'Lea', 'Guzman', 'Salonga', '1971-02-22', 'Female', 'B+', '73 Tomas Morato Ave, Quezon City', '09171234020', 'lea.guzman@gmail.com', 'Robert Guzman', '09181234020', 'Outpatient', NULL, NULL, 'Vocal Cord Strain & Nodules - Hoarseness of voice for 3 weeks.', NOW(), NOW()),
(21, 'PAT-2026-0021', 'Arnel', 'Pascual', 'Pineda', '1967-09-05', 'Male', 'O-', '29 Olongapo Highway, Subic, Zambales', '09171234021', 'arnel.pascual@gmail.com', 'Cherry Pascual', '09181234021', 'Outpatient', NULL, NULL, 'Acute Viral Upper Respiratory Tract Infection - Sore throat, dry cough, malaise.', NOW(), NOW()),
(22, 'PAT-2026-0022', 'Regine', 'Soriano', 'Velasquez', '1970-04-22', 'Female', 'A+', '84 West Avenue, Quezon City', '09171234022', 'regine.soriano@gmail.com', 'Ogie Soriano', '09181234022', 'Outpatient', NULL, NULL, 'Primary Hypothyroidism - Fatigue, weight gain, elevated TSH levels.', NOW(), NOW()),
(23, 'PAT-2026-0023', 'Ogie', 'Torres', 'Alcasid', '1967-08-27', 'Male', 'AB+', '84 West Avenue, Quezon City', '09171234023', 'ogie.torres@gmail.com', 'Regine Torres', '09181234023', 'Outpatient', NULL, NULL, 'Lumbar Spondylosis - Chronic lower back stiffness and discomfort.', NOW(), NOW()),
(24, 'PAT-2026-0024', 'Sarah', 'Roxas', 'Geronimo', '1988-07-25', 'Female', 'O+', '51 Katipunan Extension, Quezon City', '09171234024', 'sarah.roxas@gmail.com', 'Matteo Roxas', '09181234024', 'Inpatient', 'Maternity Ward 1', 'OB-205', 'Hyperemesis Gravidarum - 10 weeks gestation, persistent vomiting, electrolyte imbalance.', NOW(), NOW()),
(25, 'PAT-2026-0025', 'Matteo', 'Domingo', 'Guidicelli', '1990-03-26', 'Male', 'B+', '51 Katipunan Extension, Quezon City', '09171234025', 'matteo.domingo@gmail.com', 'Sarah Domingo', '09181234025', 'Outpatient', NULL, NULL, 'Annual Pre-employment Executive Physical Examination.', NOW(), NOW()),
(26, 'PAT-2026-0026', 'Dingdong', 'Dela Rosa', 'Dantes', '1980-08-02', 'Male', 'A+', '37 Corinthian Gardens, Quezon City', '09171234026', 'dingdong.delarosa@gmail.com', 'Marian Dela Rosa', '09181234026', 'Inpatient', 'ICU Unit 2', 'ICU-01', 'Severe Urosepsis - High grade fever, hypotension, altered mental status secondary to Complicated UTI.', NOW(), NOW()),
(27, 'PAT-2026-0027', 'Marian', 'Dizon', 'Rivera', '1984-08-12', 'Female', 'O+', '37 Corinthian Gardens, Quezon City', '09171234027', 'marian.dizon@gmail.com', 'Dingdong Dizon', '09181234027', 'Outpatient', NULL, NULL, 'Iron Deficiency Anemia secondary to Menorrhagia.', NOW(), NOW()),
(28, 'PAT-2026-0028', 'Judy Ann', 'Toledo', 'Santos', '1978-05-11', 'Female', 'AB-', '63 Maginhawa St, Teacher\'s Village, Quezon City', '09171234028', 'judyann.toledo@gmail.com', 'Ryan Toledo', '09181234028', 'Inpatient', 'Medical Ward 1', 'BED-112', 'Acute Uncomplicated Pyelonephritis - Flank pain, fever, dysuria, costovertebral angle tenderness.', NOW(), NOW()),
(29, 'PAT-2026-0029', 'Jericho', 'Castro', 'Rosales', '1979-09-22', 'Male', 'B-', '90 Palanca St, Legaspi Village, Makati City', '09171234029', 'jericho.castro@gmail.com', 'Kim Castro', '09181234029', 'Outpatient', NULL, NULL, 'Acute Lumbar Muscle Strain post-heavy lifting.', NOW(), NOW()),
(30, 'PAT-2026-0030', 'Heart', 'Alonzo', 'Evangelista', '1985-02-14', 'Female', 'A+', '14 Forbes Park, Makati City', '09171234030', 'heart.alonzo@gmail.com', 'Chiz Alonzo', '09181234030', 'Outpatient', NULL, NULL, 'Tension-Type Headache & Mild Anxiety Disorder.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
    `first_name` = VALUES(`first_name`),
    `last_name` = VALUES(`last_name`),
    `middle_name` = VALUES(`middle_name`),
    `date_of_birth` = VALUES(`date_of_birth`),
    `gender` = VALUES(`gender`),
    `blood_type` = VALUES(`blood_type`),
    `address` = VALUES(`address`),
    `phone` = VALUES(`phone`),
    `email` = VALUES(`email`),
    `patient_type` = VALUES(`patient_type`),
    `ward` = VALUES(`ward`),
    `bed_number` = VALUES(`bed_number`),
    `clinical_findings` = VALUES(`clinical_findings`),
    `updated_at` = NOW();


-- ----------------------------------------------------------------------------
-- 2. LABORATORY INFORMATION SYSTEM (LIS) SEEDER
-- All statuses covered: Pending, In Progress, Completed, Cancelled
-- Lab Results statuses covered: Pending, Encoded, Validated, Released
-- ----------------------------------------------------------------------------
INSERT INTO `lab_requests` (
    `id`, `request_no`, `patient_id`, `doctor_id`, `priority`, `status`,
    `clinical_notes`, `specimen_type`, `requested_at`, `received_at`, `completed_at`, `created_at`, `updated_at`
) VALUES
-- Pending Status (Patient 1)
(1, 'LR-2026-0001', 1, 2, 'STAT', 'Pending', 'Rule out acute appendicitis. Check WBC count and inflammatory markers.', 'Whole Blood', NOW(), NULL, NULL, NOW(), NOW()),

-- In Progress Status (Patient 3)
(2, 'LR-2026-0002', 3, 2, 'Urgent', 'In Progress', 'Evaluate pneumonia severity. Sputum Gram stain and Blood Culture.', 'Blood & Sputum', NOW(), NOW(), NULL, NOW(), NOW()),

-- Completed Status (Patient 7)
(3, 'LR-2026-0003', 7, 2, 'Routine', 'Completed', 'Evaluate microcytic anemia. Iron panel and CBC.', 'Venous Blood', NOW(), NOW(), NOW(), NOW(), NOW()),

-- Cancelled Status (Patient 9)
(4, 'LR-2026-0004', 9, 2, 'Routine', 'Cancelled', 'Duplicate request cancelled by attending physician.', 'Serum', NOW(), NULL, NULL, NOW(), NOW()),

-- Additional Completed Status (Patient 15)
(5, 'LR-2026-0005', 15, 2, 'STAT', 'Completed', 'Dengue NS1 Ag and serial Platelet count monitoring.', 'Whole Blood', NOW(), NOW(), NOW(), NOW(), NOW()),

-- Additional Pending Status (Patient 24)
(6, 'LR-2026-0006', 24, 2, 'Urgent', 'Pending', 'Serum Electrolytes (Na, K, Cl) and Urine Ketones for hyperemesis.', 'Serum & Urine', NOW(), NULL, NULL, NOW(), NOW()),

-- Additional In Progress Status (Patient 26)
(7, 'LR-2026-0007', 26, 2, 'STAT', 'In Progress', 'Urine Culture & Blood Culture for severe sepsis workup.', 'Blood & Urine', NOW(), NOW(), NULL, NOW(), NOW()),

-- Additional Completed Status (Patient 28)
(8, 'LR-2026-0008', 28, 2, 'Urgent', 'Completed', 'Routine Urinalysis and Serum Creatinine for pyelonephritis.', 'Urine & Blood', NOW(), NOW(), NOW(), NOW(), NOW())
ON DUPLICATE KEY UPDATE
    `status` = VALUES(`status`),
    `updated_at` = NOW();

-- Lab Request Items
INSERT INTO `lab_request_items` (
    `id`, `lab_request_id`, `lab_test_id`, `status`, `created_at`, `updated_at`
) VALUES
-- Pending Request Items
(1, 1, 1, 'Pending', NOW(), NOW()),     -- CBC for Patient 1
(2, 1, 5, 'Pending', NOW(), NOW()),     -- ESR for Patient 1

-- In Progress Request Items
(3, 2, 1, 'In Progress', NOW(), NOW()), -- CBC for Patient 3
(4, 2, 3, 'In Progress', NOW(), NOW()), -- HCT for Patient 3

-- Completed Request Items
(5, 3, 1, 'Completed', NOW(), NOW()),   -- CBC for Patient 7
(6, 3, 2, 'Completed', NOW(), NOW()),   -- HGB for Patient 7

-- Cancelled Request Items
(7, 4, 6, 'Pending', NOW(), NOW()),     -- FBS for Patient 9 (cancelled req)

-- Completed Request Items (Dengue)
(8, 5, 18, 'Completed', NOW(), NOW()),  -- Dengue NS1 for Patient 15
(9, 5, 4, 'Completed', NOW(), NOW()),   -- Platelet Count for Patient 15

-- In Progress Request Items (Sepsis)
(10, 7, 8, 'In Progress', NOW(), NOW()),-- Creatinine for Patient 26

-- Completed Request Items (Pyelonephritis)
(11, 8, 14, 'Completed', NOW(), NOW()), -- Urinalysis for Patient 28
(12, 8, 8, 'Completed', NOW(), NOW())   -- Creatinine for Patient 28
ON DUPLICATE KEY UPDATE
    `status` = VALUES(`status`),
    `updated_at` = NOW();

-- Lab Results (Statuses: Pending, Encoded, Validated, Released)
INSERT INTO `lab_results` (
    `id`, `lab_request_item_id`, `technologist_id`, `result_value`, `remarks`, `status`,
    `validated_by`, `released_by`, `validated_at`, `released_at`, `created_at`, `updated_at`
) VALUES
-- Pending Result Status
(1, 1, 3, NULL, 'Specimen received in lab. Awaiting automated analyzer run.', 'Pending', NULL, NULL, NULL, NULL, NOW(), NOW()),

-- Encoded Result Status
(2, 3, 3, 'WBC 16.5 x10³/µL (High), Neutrophils 82%', 'Leukocytosis with left shift consistent with acute infection.', 'Encoded', NULL, NULL, NULL, NULL, NOW(), NOW()),

-- Validated Result Status
(3, 5, 3, 'HGB 9.2 g/dL (Low), HCT 28% (Low)', 'Microcytic hypochromic picture. Suggest Serum Iron / Ferritin test.', 'Validated', 3, NULL, NOW(), NULL, NOW(), NOW()),

-- Released Result Status
(4, 8, 3, 'POSITIVE for Dengue NS1 Antigen', 'Critical result communicated to attending nurse in Pediatric Ward.', 'Released', 3, 3, NOW(), NOW(), NOW(), NOW()),

-- Released Result Status (Platelet)
(5, 9, 3, '85 x10³/µL (Thrombocytopenia)', 'Platelet count critically low. Monitor for hemorrhagic signs.', 'Released', 3, 3, NOW(), NOW(), NOW(), NOW()),

-- Released Result Status (Urinalysis)
(6, 11, 3, 'Pus cells 25-30/hpf, Bacteria Many, Protein (+1)', 'Abnormal urinalysis indicating active urinary tract infection.', 'Released', 3, 3, NOW(), NOW(), NOW(), NOW())
ON DUPLICATE KEY UPDATE
    `status` = VALUES(`status`),
    `result_value` = VALUES(`result_value`),
    `updated_at` = NOW();


-- ----------------------------------------------------------------------------
-- 3. RADIOLOGY INFORMATION SYSTEM (RIS) SEEDER
-- Request statuses covered: Pending, Scheduled, In Progress, Completed, Cancelled
-- Report statuses covered: Draft, Approved, Released
-- ----------------------------------------------------------------------------
INSERT INTO `radiology_requests` (
    `id`, `request_no`, `patient_id`, `doctor_id`, `modality`, `body_part`,
    `clinical_information`, `priority`, `status`, `requested_at`, `scheduled_at`, `completed_at`, `created_at`, `updated_at`
) VALUES
-- Pending Status (Patient 2)
(1, 'RAD-2026-0001', 2, 2, 'X-Ray', 'Chest AP/Lateral', 'Routine pre-employment baseline chest radiograph.', 'Routine', 'Pending', NOW(), NULL, NULL, NOW(), NOW()),

-- Scheduled Status (Patient 6)
(2, 'RAD-2026-0002', 6, 2, 'Ultrasound', 'Echocardiogram (2D Echo with Doppler)', 'Evaluate ejection fraction and cardiac wall motion in CHF patient.', 'Urgent', 'Scheduled', NOW(), NOW(), NULL, NOW(), NOW()),

-- In Progress Status (Patient 8)
(3, 'RAD-2026-0003', 8, 2, 'X-Ray', 'Right Femur AP/Lateral', 'Post-trauma evaluation for femur fracture angulation and displacement.', 'STAT', 'In Progress', NOW(), NOW(), NULL, NOW(), NOW()),

-- Completed Status (Patient 10)
(4, 'RAD-2026-0004', 10, 2, 'Ultrasound', 'Whole Abdomen', 'Evaluate gall bladder wall thickening and gallstones (Acute Cholecystitis).', 'Urgent', 'Completed', NOW(), NOW(), NOW(), NOW(), NOW()),

-- Cancelled Status (Patient 11)
(5, 'RAD-2026-0005', 11, 2, 'CT Scan', 'Chest High Resolution', 'Patient symptom improved; request cancelled by physician.', 'Routine', 'Cancelled', NOW(), NULL, NULL, NOW(), NOW()),

-- Additional Completed Status (Patient 19)
(6, 'RAD-2026-0006', 19, 2, 'MRI', 'Brain plain & contrast', 'Evaluate middle cerebral artery acute ischemic stroke infarct extent.', 'STAT', 'Completed', NOW(), NOW(), NOW(), NOW(), NOW())
ON DUPLICATE KEY UPDATE
    `status` = VALUES(`status`),
    `updated_at` = NOW();

-- Radiology Images
INSERT INTO `radiology_images` (
    `id`, `radiology_request_id`, `file_path`, `file_name`, `file_type`, `file_size`,
    `uploaded_by`, `uploaded_at`, `notes`, `created_at`, `updated_at`
) VALUES
(1, 3, '/storage/radiology/femur_rad_0003_ap.dcm', 'femur_rad_0003_ap.dcm', 'DICOM', 15420100, 4, NOW(), 'AP view right femur demonstrating mid-shaft transverse fracture.', NOW(), NOW()),
(2, 4, '/storage/radiology/abd_us_0004_gb.jpg', 'abd_us_0004_gb.jpg', 'JPEG', 2450100, 4, NOW(), 'Sonogram showing distended gallbladder with 1.2cm cholelithiasis.', NOW(), NOW()),
(3, 6, '/storage/radiology/mri_brain_0006_dwi.dcm', 'mri_brain_0006_dwi.dcm', 'DICOM', 48200100, 4, NOW(), 'DWI sequence showing hyperintensity in left MCA territory.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
    `file_name` = VALUES(`file_name`),
    `updated_at` = NOW();

-- Radiology Reports (Statuses: Draft, Approved, Released)
INSERT INTO `radiology_reports` (
    `id`, `radiology_request_id`, `radiologist_id`, `findings`, `impression`,
    `recommendations`, `status`, `approved_by`, `released_by`, `approved_at`, `released_at`, `created_at`, `updated_at`
) VALUES
-- Draft Report Status
(1, 3, 5, 'Complete transverse fracture of the mid-diaphysis of the right femur with 1.5 cm apex-anterior angulation and lateral displacement.', 'Complete transverse fracture mid-shaft right femur.', 'Immediate orthopedic surgery consultation for open reduction and internal fixation (ORIF).', 'Draft', NULL, NULL, NULL, NULL, NOW(), NOW()),

-- Approved Report Status
(2, 4, 5, 'Distended gallbladder with diffusely thickened gallbladder wall measuring 4.5 mm. Multiple hyperechoic foci with acoustic shadowing noted within the gallbladder lumen, largest measuring 1.2 cm.', 'Acute Calculous Cholecystitis with Cholelithiasis.', 'Surgical evaluation for laparoscopic cholecystectomy recommended.', 'Approved', 5, NULL, NOW(), NULL, NOW(), NOW()),

-- Released Report Status
(3, 6, 5, 'Diffusion-weighted imaging (DWI) demonstrates marked hyperintensity in the left insular cortex and frontal subcortical white matter with corresponding hypointensity on ADC map.', 'Acute Ischemic Infarct, Left Middle Cerebral Artery (MCA) territory.', 'Correlate with neurology clinical exam; serial neuro imaging as indicated.', 'Released', 5, 5, NOW(), NOW(), NOW(), NOW())
ON DUPLICATE KEY UPDATE
    `status` = VALUES(`status`),
    `findings` = VALUES(`findings`),
    `updated_at` = NOW();


-- ----------------------------------------------------------------------------
-- 4. PHARMACY MANAGEMENT SYSTEM (PMS) SEEDER
-- Prescription statuses covered: Pending, Verified, Partially Dispensed, Dispensed, Cancelled
-- Prescription Item statuses covered: Pending, Dispensed
-- ----------------------------------------------------------------------------
INSERT INTO `prescriptions` (
    `id`, `prescription_no`, `patient_id`, `doctor_id`, `status`, `notes`,
    `diagnosis`, `prescribed_at`, `verified_by`, `verified_at`, `created_at`, `updated_at`
) VALUES
-- Pending Status (Patient 4)
(1, 'RX-2026-0001', 4, 2, 'Pending', 'Administer with meals. Monitor blood glucose levels daily.', 'Type 2 Diabetes Mellitus', NOW(), NULL, NULL, NOW(), NOW()),

-- Verified Status (Patient 5)
(2, 'RX-2026-0002', 5, 2, 'Verified', 'Oral rehydration therapy and anti-diarrheal regimen.', 'Acute Gastroenteritis', NOW(), 6, NOW(), NOW(), NOW()),

-- Partially Dispensed Status (Patient 13)
(3, 'RX-2026-0003', 13, 2, 'Partially Dispensed', 'Patient requested 1-week partial supply first.', 'Acute Gouty Arthritis', NOW(), 6, NOW(), NOW(), NOW()),

-- Dispensed Status (Patient 14)
(4, 'RX-2026-0004', 14, 2, 'Dispensed', 'Complete 14-day anti-ulcer triple therapy course.', 'Chronic Erosive Gastritis', NOW(), 6, NOW(), NOW(), NOW()),

-- Cancelled Status (Patient 18)
(5, 'RX-2026-0005', 18, 2, 'Cancelled', 'Discontinued due to patient reported NSAID allergy.', 'Rotator Cuff Tendinopathy', NOW(), NULL, NULL, NOW(), NOW()),

-- Additional Dispensed Status (Patient 21)
(6, 'RX-2026-0006', 21, 2, 'Dispensed', 'Symptomatic relief for viral URI symptoms.', 'Acute Viral URTI', NOW(), 6, NOW(), NOW(), NOW()),

-- Additional Verified Status (Patient 22)
(7, 'RX-2026-0007', 22, 2, 'Verified', 'Levothyroxine maintenance dose. Take early morning on empty stomach.', 'Primary Hypothyroidism', NOW(), 6, NOW(), NOW(), NOW())
ON DUPLICATE KEY UPDATE
    `status` = VALUES(`status`),
    `updated_at` = NOW();

-- Prescription Items (Statuses: Pending, Dispensed)
INSERT INTO `prescription_items` (
    `id`, `prescription_id`, `medication_name`, `dosage`, `route`, `frequency`,
    `duration`, `quantity`, `instructions`, `status`, `created_at`, `updated_at`
) VALUES
-- Pending Rx Items
(1, 1, 'Metformin Hydrochloride', '500 mg', 'Oral', 'Twice daily with meals', '30 days', 60, 'Take with breakfast and dinner.', 'Pending', NOW(), NOW()),
(2, 1, 'Gliclazide', '80 mg', 'Oral', 'Once daily before breakfast', '30 days', 30, 'Swallow whole with water.', 'Pending', NOW(), NOW()),

-- Verified Rx Items (Pending Dispensing)
(3, 2, 'Oral Rehydration Salts (ORS)', '1 sachet in 1L water', 'Oral', 'After each loose stool', '5 days', 10, 'Dissolve completely in clean drinking water.', 'Pending', NOW(), NOW()),
(4, 2, 'Zinc Sulfate', '20 mg', 'Oral', 'Once daily', '10 days', 10, 'Take after food.', 'Pending', NOW(), NOW()),

-- Partially Dispensed Rx Items
(5, 3, 'Colchicine', '500 mcg', 'Oral', 'Three times daily', '7 days', 21, 'Stop if diarrhea occurs.', 'Dispensed', NOW(), NOW()),
(6, 3, 'Febuxostat', '40 mg', 'Oral', 'Once daily', '30 days', 30, 'Take continuously for uric acid control.', 'Pending', NOW(), NOW()),

-- Fully Dispensed Rx Items
(7, 4, 'Omeprazole', '20 mg', 'Oral', 'Twice daily before meals', '14 days', 28, 'Take 30 mins before morning & evening meals.', 'Dispensed', NOW(), NOW()),
(8, 4, 'Amoxicillin', '1000 mg', 'Oral', 'Twice daily', '14 days', 28, 'Finish full course of antibiotics.', 'Dispensed', NOW(), NOW()),

-- Cancelled Rx Item
(9, 5, 'Mefenamic Acid', '500 mg', 'Oral', 'Three times daily PRN pain', '5 days', 15, 'Cancelled by doctor.', 'Pending', NOW(), NOW()),

-- Dispensed URI Items
(10, 6, 'Paracetamol', '500 mg', 'Oral', 'Every 6 hours PRN fever/pain', '5 days', 20, 'Do not exceed 4000mg per 24 hours.', 'Dispensed', NOW(), NOW())
ON DUPLICATE KEY UPDATE
    `status` = VALUES(`status`),
    `updated_at` = NOW();

-- Dispensing Records
INSERT INTO `dispensing_records` (
    `id`, `prescription_item_id`, `pharmacist_id`, `quantity_dispensed`, `lot_number`,
    `expiry_date`, `notes`, `dispensed_at`, `created_at`, `updated_at`
) VALUES
(1, 5, 6, 14, 'LOT-COL-2026A', '2027-12-31', 'Dispensed partial 14 tablets per patient request.', NOW(), NOW(), NOW()),
(2, 7, 6, 28, 'LOT-OMP-2025C', '2028-06-30', 'Dispensed full 28 capsules Omeprazole.', NOW(), NOW(), NOW()),
(3, 8, 6, 28, 'LOT-AMX-2026B', '2027-09-30', 'Dispensed full 28 capsules Amoxicillin.', NOW(), NOW(), NOW()),
(4, 10, 6, 20, 'LOT-PCM-2026X', '2028-03-31', 'Dispensed 20 tablets Paracetamol 500mg.', NOW(), NOW(), NOW())
ON DUPLICATE KEY UPDATE
    `quantity_dispensed` = VALUES(`quantity_dispensed`),
    `updated_at` = NOW();


-- ----------------------------------------------------------------------------
-- 5. SURGICAL & OPERATING ROOM SYSTEM (SORS) SEEDER
-- Operating Room definitions & Surgical Teams
-- Surgery Request statuses: Pending, Scheduled, In Progress, Completed, Cancelled
-- Surgery Schedule statuses: Scheduled, In Progress, Completed, Cancelled, Postponed
-- ----------------------------------------------------------------------------
INSERT INTO `operating_rooms` (
    `id`, `name`, `location`, `status`, `equipment`, `is_active`, `created_at`, `updated_at`
) VALUES
(1, 'OR Room 1 - Major Surgery', 'Main Building 3rd Floor', 'Occupied', 'Anesthesia Station, Laparoscopic Tower, C-Arm Fluoroscopy, Surgical Lights', 1, NOW(), NOW()),
(2, 'OR Room 2 - Cardiovascular', 'Main Building 3rd Floor', 'Available', 'Heart-Lung Bypass Machine, Cardiac Monitor, Defibrillator, Intra-aortic Balloon Pump', 1, NOW(), NOW()),
(3, 'OR Room 3 - Minor Surgery', 'Outpatient Surgical Pavilion', 'Available', 'Electrocautery Unit, Minor Instrument Set, Suction Apparatus', 1, NOW(), NOW()),
(4, 'OR Room 4 - Emergency OR', 'Emergency Department 1st Floor', 'Under Maintenance', 'Rapid Infuser, Emergency Anesthesia Machine, Crash Cart', 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE
    `name` = VALUES(`name`),
    `status` = VALUES(`status`),
    `updated_at` = NOW();

INSERT INTO `surgical_teams` (
    `id`, `name`, `surgeon_id`, `notes`, `is_active`, `created_at`, `updated_at`
) VALUES
(1, 'General Surgery Team Alpha', 2, 'Lead Surgeon: Dr. Juan Dela Cruz (General & Laparoscopic)', 1, NOW(), NOW()),
(2, 'Orthopedic Surgery Team', 2, 'Specialized in trauma & joint reconstruction', 1, NOW(), NOW()),
(3, 'OB-GYN Surgical Team', 2, 'Specialized in gynecologic laparoscopy & cesarean section', 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE
    `name` = VALUES(`name`),
    `updated_at` = NOW();

INSERT INTO `surgical_team_members` (
    `id`, `surgical_team_id`, `user_id`, `role_in_team`, `created_at`, `updated_at`
) VALUES
(1, 1, 2, 'Primary Lead Surgeon', NOW(), NOW()),
(2, 1, 8, 'OR Nurse Coordinator', NOW(), NOW()),
(3, 2, 2, 'Primary Orthopedic Surgeon', NOW(), NOW())
ON DUPLICATE KEY UPDATE
    `role_in_team` = VALUES(`role_in_team`),
    `updated_at` = NOW();

-- Surgery Requests (Statuses: Pending, Scheduled, In Progress, Completed, Cancelled)
INSERT INTO `surgery_requests` (
    `id`, `request_no`, `patient_id`, `doctor_id`, `procedure_name`, `diagnosis`,
    `urgency`, `status`, `notes`, `anesthesia_type`, `estimated_duration`, `requested_at`, `created_at`, `updated_at`
) VALUES
-- Pending Status (Patient 1)
(1, 'SURG-2026-0001', 1, 2, 'Laparoscopic Appendectomy', 'Acute Appendicitis', 'Emergency', 'Pending', 'NPO status started. Prepare OR for acute appendectomy.', 'General Anesthesia', 90, NOW(), NOW(), NOW()),

-- Scheduled Status (Patient 8)
(2, 'SURG-2026-0002', 8, 2, 'Open Reduction Internal Fixation (ORIF) Right Femur', 'Closed Fracture Right Femur', 'Urgent', 'Scheduled', 'Crossmatched 2 units PRBC. Femoral nerve block planned.', 'General with Epidural', 180, NOW(), NOW(), NOW()),

-- In Progress Status (Patient 12)
(3, 'SURG-2026-0003', 12, 2, 'Laparoscopic Inguinal Hernioplasty', 'Right Inguinal Hernia', 'Elective', 'In Progress', 'Patient currently in OR Room 1. Mesh repair in progress.', 'Spinal Anesthesia', 120, NOW(), NOW(), NOW()),

-- Completed Status (Patient 17)
(4, 'SURG-2026-0004', 17, 2, 'Laparoscopic Ovarian Cystectomy', 'Right Ovarian Cyst', 'Elective', 'Completed', 'Successfully excised 4cm benign right ovarian cyst. Minimal blood loss.', 'General Anesthesia', 100, NOW(), NOW(), NOW()),

-- Cancelled Status (Patient 21)
(5, 'SURG-2026-0005', 21, 2, 'Excision of Lipoma Back', 'Benign Lipoma', 'Elective', 'Cancelled', 'Cancelled due to active URTI symptoms; rescheduled after recovery.', 'Local Anesthesia', 45, NOW(), NOW(), NOW()),

-- Additional Scheduled Request for Postponed status test (Patient 26)
(6, 'SURG-2026-0006', 26, 2, 'Emergency Cystoscopy & Debridement', 'Complicated Urosepsis', 'Emergency', 'Scheduled', 'Postponed until hemodynamic stability achieved in ICU.', 'General Anesthesia', 60, NOW(), NOW(), NOW())
ON DUPLICATE KEY UPDATE
    `status` = VALUES(`status`),
    `updated_at` = NOW();

-- Surgery Schedules (Statuses: Scheduled, In Progress, Completed, Cancelled, Postponed)
INSERT INTO `surgery_schedules` (
    `id`, `surgery_request_id`, `operating_room_id`, `surgical_team_id`, `scheduled_by`,
    `scheduled_at`, `duration_minutes`, `status`, `notes`, `created_at`, `updated_at`
) VALUES
-- Scheduled Schedule Status
(1, 2, 1, 2, 8, NOW() + INTERVAL 1 DAY, 180, 'Scheduled', 'OR Room 1 reserved 08:00 AM tomorrow.', NOW(), NOW()),

-- In Progress Schedule Status
(2, 3, 1, 1, 8, NOW(), 120, 'In Progress', 'Surgical incision made at 10:15 AM.', NOW(), NOW()),

-- Completed Schedule Status
(3, 4, 1, 3, 8, NOW() - INTERVAL 1 DAY, 100, 'Completed', 'Procedure uncomplicated. Patient transferred to PACU in stable condition.', NOW(), NOW()),

-- Cancelled Schedule Status
(4, 5, 3, 1, 8, NOW() + INTERVAL 2 DAY, 45, 'Cancelled', 'Slot released back to OR pool.', NOW(), NOW()),

-- Postponed Schedule Status
(5, 6, 2, 1, 8, NOW() + INTERVAL 6 HOUR, 60, 'Postponed', 'Postponed pending ICU clearance for blood pressure stabilization.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
    `status` = VALUES(`status`),
    `updated_at` = NOW();


-- ----------------------------------------------------------------------------
-- 6. DIET & NUTRITION MANAGEMENT SYSTEM (DNMS) SEEDER
-- Request statuses covered: Pending, Active, Completed, Cancelled
-- Plan statuses covered: Active, Completed, Revised
-- Meal Schedule types: Breakfast, Mid-Morning Snack, Lunch, Afternoon Snack, Dinner, Bedtime Snack
-- ----------------------------------------------------------------------------
INSERT INTO `diet_requests` (
    `id`, `request_no`, `patient_id`, `doctor_id`, `diet_type`, `allergies`,
    `food_restrictions`, `clinical_notes`, `status`, `requested_at`, `created_at`, `updated_at`
) VALUES
-- Pending Status (Patient 3)
(1, 'DIET-2026-0001', 3, 2, 'High Protein, High Calorie Soft Diet', 'Shellfish, Penicillin', 'No cold drinks, no raw vegetables', 'Pneumonia recovery. Require warm nutrient-dense meals.', 'Pending', NOW(), NOW(), NOW()),

-- Active Status (Patient 10)
(2, 'DIET-2026-0002', 10, 2, 'Low Fat, Low Cholesterol Diet', 'Shrimp', 'Strictly no oily/fried foods', 'Post-cholecystitis management prior to surgery.', 'Active', NOW(), NOW(), NOW()),

-- Completed Status (Patient 15)
(3, 'DIET-2026-0003', 15, 2, 'Soft Bland Diet with Dengue Fluids', 'None', 'No dark colored food (to assess GIT bleeding)', 'Dengue inpatient diet with papaya leaf extract supplement.', 'Completed', NOW(), NOW(), NOW()),

-- Cancelled Status (Patient 28)
(4, 'DIET-2026-0004', 28, 2, 'Low Sodium Renal Diet', 'Peanuts', 'Limit sodium <2g/day', 'Cancelled request - Patient placed on Full Liquid Diet instead.', 'Cancelled', NOW(), NOW(), NOW()),

-- Additional Active Request for Revised Diet Plan (Patient 24)
(5, 'DIET-2026-0005', 24, 2, 'Small Frequent High Carbohydrate Diet', 'Dairy/Lactose', 'No greasy or highly spiced food', 'Hyperemesis Gravidarum diet protocol.', 'Active', NOW(), NOW(), NOW())
ON DUPLICATE KEY UPDATE
    `status` = VALUES(`status`),
    `updated_at` = NOW();

-- Diet Plans (Statuses: Active, Completed, Revised)
INSERT INTO `diet_plans` (
    `id`, `diet_request_id`, `dietitian_id`, `plan_details`, `total_calories`,
    `protein_grams`, `carb_grams`, `fat_grams`, `start_date`, `end_date`, `status`, `notes`, `created_at`, `updated_at`
) VALUES
-- Active Diet Plan Status
(1, 2, 7, 'Low fat, moderate protein meal plan divided into 3 main meals and 2 light snacks. Emphasize steamed fish, boiled vegetables, and brown rice.', 1800, 75.00, 250.00, 30.00, CURDATE(), CURDATE() + INTERVAL 7 DAY, 'Active', 'Patient tolerated first 2 days well. No nausea postprandial.', NOW(), NOW()),

-- Completed Diet Plan Status
(2, 3, 7, 'Hydration focused soft diet with oral rehydration solution, lugaw with chicken breast, and fruit juices.', 2000, 70.00, 300.00, 25.00, CURDATE() - INTERVAL 5 DAY, CURDATE(), 'Completed', 'Platelet normalized. Patient discharged.', NOW(), NOW()),

-- Revised Diet Plan Status
(3, 5, 7, 'Revised from 3 meals to 6 small dry carbohydrate-rich meals daily to minimize nausea in hyperemesis gravidarum.', 1600, 60.00, 240.00, 20.00, CURDATE(), CURDATE() + INTERVAL 14 DAY, 'Revised', 'Revised diet plan approved by clinical nutritionist.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
    `status` = VALUES(`status`),
    `plan_details` = VALUES(`plan_details`),
    `updated_at` = NOW();

-- Meal Schedules (Covers all meal types & is_served 0 and 1)
INSERT INTO `meal_schedules` (
    `id`, `diet_plan_id`, `meal_type`, `meal_date`, `menu`, `calories`, `is_served`, `notes`, `created_at`, `updated_at`
) VALUES
(1, 1, 'Breakfast',           CURDATE(), 'Oatmeal with sliced banana, boiled egg whites, green tea', 350, 1, 'Served at 07:30 AM. Patient consumed 100%.', NOW(), NOW()),
(2, 1, 'Mid-Morning Snack',   CURDATE(), 'Steamed saba banana, warm water', 150, 1, 'Served at 10:00 AM.', NOW(), NOW()),
(3, 1, 'Lunch',                 CURDATE(), 'Steamed Tilapia with malunggay soup, 1 cup brown rice', 550, 1, 'Served at 12:15 PM.', NOW(), NOW()),
(4, 1, 'Afternoon Snack',     CURDATE(), 'Vegetable lumpia (un-fried/fresh), papaya slices', 200, 0, 'Scheduled for 03:30 PM.', NOW(), NOW()),
(5, 1, 'Dinner',                CURDATE(), 'Grilled chicken breast, boiled carrots and broccoli, Sinangag-style garlic brown rice', 450, 0, 'Scheduled for 06:30 PM.', NOW(), NOW()),
(6, 1, 'Bedtime Snack',        CURDATE(), 'Warm chamomile tea with skyflakes crackers', 100, 0, 'Scheduled for 09:00 PM.', NOW(), NOW()),

-- Meal Schedules for Revised Plan (Diet Plan 3)
(7, 3, 'Breakfast',           CURDATE(), 'Plain rice porridge (Lugaw) with toasted garlic', 250, 1, 'Tolerated well without emesis.', NOW(), NOW()),
(8, 3, 'Mid-Morning Snack',   CURDATE(), 'Soda crackers with warm ginger tea (Salabat)', 150, 1, 'Helped reduce morning sickness.', NOW(), NOW()),
(9, 3, 'Lunch',                 CURDATE(), 'Boiled chicken breast tinola with chayote, white rice', 400, 0, 'Scheduled for 12:00 PM.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
    `menu` = VALUES(`menu`),
    `is_served` = VALUES(`is_served`),
    `updated_at` = NOW();

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================================
-- END OF SEEDER FILE (seader.sql)
-- Verification Checklist:
--   - Patients: 30 records inserted with Filipino names (PAT-2026-0001 to 0030)
--   - LIS (Lab): Lab Requests (Pending, In Progress, Completed, Cancelled), Results (Pending, Encoded, Validated, Released)
--   - RIS (Rad): Rad Requests (Pending, Scheduled, In Progress, Completed, Cancelled), Reports (Draft, Approved, Released)
--   - PMS (Pharm): Prescriptions (Pending, Verified, Partially Dispensed, Dispensed, Cancelled), Items (Pending, Dispensed)
--   - SORS (Surg): ORs, Teams, Surgery Requests & Schedules (Scheduled, In Progress, Completed, Cancelled, Postponed)
--   - DNMS (Diet): Diet Requests (Pending, Active, Completed, Cancelled), Plans (Active, Completed, Revised), Meal Schedules
-- ============================================================================
