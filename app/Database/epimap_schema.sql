-- ============================================================
-- e-Matrika Database Schema
-- Web GIS Pemetaan Distribusi Penyakit
-- ============================================================

CREATE DATABASE IF NOT EXISTS ematrika
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_general_ci;

USE ematrika;

-- ------------------------------------------------------------
-- Fasilitas Kesehatan
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS facilities (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama        VARCHAR(200) NOT NULL,
    kode        VARCHAR(50),
    jenis       VARCHAR(100),
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Pengguna
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username        VARCHAR(100) NOT NULL UNIQUE,
    password_hash   VARCHAR(500) NOT NULL,
    role            ENUM('admin','petugas') NOT NULL DEFAULT 'petugas',
    facility_id     INT UNSIGNED NULL,
    active          TINYINT(1) DEFAULT 1,
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (facility_id) REFERENCES facilities(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Batch Impor
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS import_batches (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    facility_id     INT UNSIGNED NULL,
    user_id         INT UNSIGNED NOT NULL,
    nama_file       VARCHAR(255) NOT NULL,
    status          ENUM('processing','done','failed') DEFAULT 'processing',
    total_baris     INT DEFAULT 0,
    baris_berhasil  INT DEFAULT 0,
    baris_gagal     INT DEFAULT 0,
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (facility_id) REFERENCES facilities(id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Kasus
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS cases_data (
    id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    batch_id            INT UNSIGNED NOT NULL,
    facility_id         INT UNSIGNED NULL,
    tanggal_kunjungan   DATE NOT NULL,
    nama                VARCHAR(500) NOT NULL,
    icd10_code          VARCHAR(20),
    diagnosa_teks       VARCHAR(500),
    alamat              VARCHAR(1000) NOT NULL,
    latitude            DECIMAL(10,8) NULL,
    longitude           DECIMAL(11,8) NULL,
    lokasi_perkiraan    TINYINT(1) DEFAULT 0,
    created_at          DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (batch_id) REFERENCES import_batches(id) ON DELETE CASCADE,
    FOREIGN KEY (facility_id) REFERENCES facilities(id) ON DELETE SET NULL,
    INDEX idx_tanggal   (tanggal_kunjungan),
    INDEX idx_icd10     (icd10_code),
    INDEX idx_coords    (latitude, longitude),
    INDEX idx_batch     (batch_id)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Referensi ICD-10
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS icd10_ref (
    code        VARCHAR(20) PRIMARY KEY,
    nama_id     VARCHAR(500) NOT NULL,
    nama_en     VARCHAR(500),
    bab         VARCHAR(10),
    blok        VARCHAR(20),
    sensitif    TINYINT(1) DEFAULT 0
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Cache Geocoding
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS geocode_cache (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    alamat_hash VARCHAR(64) NOT NULL UNIQUE,
    alamat_asli VARCHAR(1000),
    latitude    DECIMAL(10,8),
    longitude   DECIMAL(11,8),
    sumber      VARCHAR(100),
    updated_at  DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Catatan Aktivitas (Audit Log)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS audit_logs (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     INT UNSIGNED NULL,
    aksi        VARCHAR(100) NOT NULL,
    objek       VARCHAR(500),
    ip          VARCHAR(45),
    waktu       DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_waktu (waktu)
) ENGINE=InnoDB;

-- ============================================================
-- SEED DATA
-- ============================================================

-- Default facility
INSERT INTO facilities (nama, kode, jenis) VALUES
    ('Puskesmas Umum', 'PKM-001', 'Puskesmas');

-- Default admin (password: admin123, bcrypt hash)
INSERT INTO users (username, password_hash, role, facility_id, active) VALUES
    ('admin', '$2y$10$BvW4lvn80iW2d8typpFBluZbLLCt6mTxnNuGIVIbyEf6hsc3khsVu', 'admin', 1, 1);

-- ============================================================
-- ICD-10 Reference (common codes)
-- ============================================================
INSERT INTO icd10_ref (code, nama_id, nama_en, bab, blok, sensitif) VALUES
-- Bab I: Penyakit infeksi dan parasit
('A01.0','Demam tifoid','Typhoid fever','I','A00-A09',0),
('A09','Diare dan gastroenteritis','Diarrhoea and gastroenteritis','I','A00-A09',0),
('A15','Tuberkulosis paru','Respiratory tuberculosis','I','A15-A19',0),
('A16','Tuberkulosis paru tanpa konfirmasi','Respiratory TB without confirmation','I','A15-A19',0),
('A90','Demam dengue','Dengue fever','I','A90-A99',0),
('A91','Demam berdarah dengue','Dengue haemorrhagic fever','I','A90-A99',0),
('B00','Infeksi virus herpes simplex','Herpesviral infections','I','B00-B09',0),
('B05','Campak','Measles','I','B05-B06',0),
('B15','Hepatitis A akut','Acute hepatitis A','I','B15-B19',0),
('B20','HIV dengan penyakit infeksi','HIV with infectious diseases','I','B20-B24',1),
('B24','HIV tidak ditentukan','Unspecified HIV disease','I','B20-B24',1),
('B26','Gondongan','Mumps','I','B25-B34',0),
('B37','Kandidiasis','Candidiasis','I','B35-B49',0),
-- Bab II: Neoplasma
('C50','Neoplasma ganas payudara','Malignant neoplasm of breast','II','C50',0),
('C53','Neoplasma ganas serviks uteri','Malignant neoplasm of cervix uteri','II','C51-C58',0),
-- Bab IV: Endokrin, nutrisi, metabolik
('E10','Diabetes melitus tipe 1','Type 1 diabetes mellitus','IV','E10-E14',0),
('E11','Diabetes melitus tipe 2','Type 2 diabetes mellitus','IV','E10-E14',0),
('E66','Obesitas','Obesity','IV','E65-E68',0),
('E78','Gangguan metabolisme lipoprotein','Disorders of lipoprotein metabolism','IV','E70-E90',0),
-- Bab V: Gangguan jiwa (sensitif)
('F20','Skizofrenia','Schizophrenia','V','F20-F29',1),
('F32','Episode depresif','Depressive episode','V','F30-F39',1),
('F41','Gangguan cemas lainnya','Other anxiety disorders','V','F40-F48',1),
-- Bab VI: Penyakit sistem saraf
('G40','Epilepsi','Epilepsy','VI','G40-G47',0),
('G43','Migrain','Migraine','VI','G43-G44',0),
-- Bab IX: Penyakit sistem sirkulasi
('I10','Hipertensi esensial (primer)','Essential (primary) hypertension','IX','I10-I15',0),
('I11','Penyakit jantung hipertensi','Hypertensive heart disease','IX','I10-I15',0),
('I21','Infark miokard akut','Acute myocardial infarction','IX','I20-I25',0),
('I50','Gagal jantung','Heart failure','IX','I30-I52',0),
('I63','Infark serebral (stroke)','Cerebral infarction','IX','I60-I69',0),
-- Bab X: Penyakit sistem pernapasan
('J00','Nasofaringitis akut (flu biasa)','Acute nasopharyngitis (common cold)','X','J00-J06',0),
('J02','Faringitis akut','Acute pharyngitis','X','J00-J06',0),
('J06','Infeksi saluran napas atas akut','Acute upper respiratory infection','X','J00-J06',0),
('J06.9','ISPA tidak ditentukan','Acute upper respiratory infection, unspecified','X','J00-J06',0),
('J18','Pneumonia','Pneumonia, unspecified organism','X','J12-J18',0),
('J45','Asma','Asthma','X','J40-J47',0),
-- Bab XI: Penyakit sistem pencernaan
('K21','Penyakit refluks gastroesofageal','Gastro-oesophageal reflux disease','XI','K20-K31',0),
('K29','Gastritis dan duodenitis','Gastritis and duodenitis','XI','K20-K31',0),
('K30','Dispepsia fungsional','Functional dyspepsia','XI','K20-K31',0),
('K35','Apendisitis akut','Acute appendicitis','XI','K35-K38',0),
-- Bab XII: Penyakit kulit
('L20','Dermatitis atopik','Atopic dermatitis','XII','L20-L30',0),
('L50','Urtikaria','Urticaria','XII','L50-L54',0),
-- Bab XIII: Penyakit sistem otot dan tulang
('M54','Nyeri punggung','Dorsalgia','XIII','M50-M54',0),
('M79','Gangguan jaringan lunak lainnya','Other and unspecified soft tissue disorders','XIII','M70-M79',0),
-- Bab XIV: Penyakit sistem genitourinari
('N39.0','Infeksi saluran kemih','Urinary tract infection','XIV','N30-N39',0),
-- Bab XV: Kehamilan dan persalinan
('O80','Persalinan spontan tunggal','Single spontaneous delivery','XV','O80-O84',0),
-- Bab XVIII: Gejala dan tanda
('R50','Demam tidak diketahui sebabnya','Fever of other and unknown origin','XVIII','R50-R69',0),
('R10','Nyeri perut dan panggul','Abdominal and pelvic pain','XVIII','R10-R19',0),
('R51','Sakit kepala','Headache','XVIII','R50-R69',0),
-- Bab XIX: Cedera
('S61','Luka terbuka tangan','Open wound of wrist, hand and fingers','XIX','S60-S69',0),
('T14','Cedera tidak ditentukan','Injury, unspecified','XIX','T08-T14',0);
