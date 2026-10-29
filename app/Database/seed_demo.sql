-- ============================================================
-- e-Matrika – Data Contoh (Fiktif)
-- Semua nama dan alamat di bawah ini fiktif.
-- ============================================================
USE ematrika;

-- Batch impor fiktif
INSERT INTO import_batches (facility_id, user_id, nama_file, status, total_baris, baris_berhasil, baris_gagal, created_at)
VALUES (1, 1, 'contoh_data_demo.csv', 'done', 30, 30, 0, NOW());

SET @batch_id = LAST_INSERT_ID();

-- Kasus contoh di sekitar Padang, Sumatera Barat (koordinat fiktif)
INSERT INTO cases_data (batch_id, facility_id, tanggal_kunjungan, nama, icd10_code, diagnosa_teks, alamat, latitude, longitude, lokasi_perkiraan) VALUES
(@batch_id, 1, '2026-09-01', 'Siti Rahma', 'A09', 'Diare dan gastroenteritis', 'Jl. Melati No. 12, Kec. Padang Timur', -0.9492, 100.3543, 0),
(@batch_id, 1, '2026-09-02', 'Budi Santoso', 'J06.9', 'ISPA tidak ditentukan', 'Jl. Kenanga No. 3, Kec. Nanggalo', -0.9185, 100.3487, 0),
(@batch_id, 1, '2026-09-03', 'Ani Lestari', 'I10', 'Hipertensi esensial (primer)', 'Jl. Cempaka No. 8, Kec. Kuranji', -0.9124, 100.3792, 0),
(@batch_id, 1, '2026-09-03', 'Dedi Kurniawan', 'E11', 'Diabetes melitus tipe 2', 'Jl. Mawar No. 5, Kec. Padang Barat', -0.9556, 100.3509, 0),
(@batch_id, 1, '2026-09-04', 'Rina Putri', 'J45', 'Asma', 'Jl. Dahlia No. 15, Kec. Lubuk Begalung', -0.9721, 100.3613, 0),
(@batch_id, 1, '2026-09-05', 'Agus Prasetyo', 'A09', 'Diare dan gastroenteritis', 'Jl. Anggrek No. 20, Kec. Padang Timur', -0.9468, 100.3601, 0),
(@batch_id, 1, '2026-09-05', 'Maya Sari', 'K29', 'Gastritis dan duodenitis', 'Jl. Flamboyan No. 7, Kec. Padang Utara', -0.9287, 100.3552, 0),
(@batch_id, 1, '2026-09-06', 'Hendra Wijaya', 'J00', 'Nasofaringitis akut (flu biasa)', 'Jl. Bougenville No. 11, Kec. Nanggalo', -0.9211, 100.3412, 0),
(@batch_id, 1, '2026-09-07', 'Fitri Handayani', 'I10', 'Hipertensi esensial (primer)', 'Jl. Teratai No. 9, Kec. Koto Tangah', -0.8834, 100.3521, 0),
(@batch_id, 1, '2026-09-08', 'Rizal Fahmi', 'A90', 'Demam dengue', 'Jl. Kamboja No. 14, Kec. Padang Selatan', -0.9612, 100.3598, 0),
(@batch_id, 1, '2026-09-09', 'Dewi Anggraini', 'J06', 'Infeksi saluran napas atas akut', 'Jl. Seroja No. 6, Kec. Padang Timur', -0.9445, 100.3567, 0),
(@batch_id, 1, '2026-09-10', 'Eko Saputra', 'E11', 'Diabetes melitus tipe 2', 'Jl. Mangga No. 22, Kec. Kuranji', -0.9178, 100.3812, 0),
(@batch_id, 1, '2026-09-11', 'Lina Marlina', 'K30', 'Dispepsia fungsional', 'Jl. Rambutan No. 4, Kec. Lubuk Begalung', -0.9698, 100.3578, 0),
(@batch_id, 1, '2026-09-12', 'Wahyu Nugroho', 'A09', 'Diare dan gastroenteritis', 'Jl. Nangka No. 18, Kec. Padang Barat', -0.9534, 100.3489, 0),
(@batch_id, 1, '2026-09-13', 'Ratna Kusuma', 'J18', 'Pneumonia', 'Jl. Durian No. 10, Kec. Padang Utara', -0.9312, 100.3498, 0),
(@batch_id, 1, '2026-09-14', 'Tono Suryadi', 'I10', 'Hipertensi esensial (primer)', 'Jl. Jambu No. 13, Kec. Nanggalo', -0.9198, 100.3445, 0),
(@batch_id, 1, '2026-09-15', 'Sri Mulyani', 'A91', 'Demam berdarah dengue', 'Jl. Pepaya No. 21, Kec. Koto Tangah', -0.8867, 100.3478, 0),
(@batch_id, 1, '2026-09-16', 'Irfan Hakim', 'J06.9', 'ISPA tidak ditentukan', 'Jl. Salak No. 16, Kec. Padang Selatan', -0.9645, 100.3556, 0),
(@batch_id, 1, '2026-09-17', 'Nurul Hidayah', 'M54', 'Nyeri punggung', 'Jl. Apel No. 2, Kec. Padang Timur', -0.9478, 100.3589, 0),
(@batch_id, 1, '2026-09-18', 'Fajar Ramadhan', 'A15', 'Tuberkulosis paru', 'Jl. Kelapa No. 19, Kec. Kuranji', -0.9145, 100.3756, 0),
(@batch_id, 1, '2026-09-19', 'Diana Permata', 'N39.0', 'Infeksi saluran kemih', 'Jl. Sawit No. 8, Kec. Lubuk Begalung', -0.9734, 100.3634, 0),
(@batch_id, 1, '2026-09-20', 'Arief Rahman', 'E78', 'Gangguan metabolisme lipoprotein', 'Jl. Aren No. 25, Kec. Padang Barat', -0.9578, 100.3512, 0),
(@batch_id, 1, '2026-09-21', 'Yuni Astuti', 'J45', 'Asma', 'Jl. Pinang No. 11, Kec. Padang Utara', -0.9298, 100.3534, 0),
(@batch_id, 1, '2026-09-22', 'Bambang Hermanto', 'A09', 'Diare dan gastroenteritis', 'Jl. Sagu No. 17, Kec. Nanggalo', -0.9234, 100.3423, 0),
(@batch_id, 1, '2026-09-23', 'Kartini Wulandari', 'I10', 'Hipertensi esensial (primer)', 'Jl. Rotan No. 6, Kec. Koto Tangah', -0.8812, 100.3567, 0),
(@batch_id, 1, '2026-09-24', 'Joko Susanto', 'R50', 'Demam tidak diketahui sebabnya', 'Jl. Bambu No. 14, Kec. Padang Selatan', -0.9623, 100.3623, 0),
(@batch_id, 1, '2026-09-25', 'Endah Sulistyowati', 'A01.0', 'Demam tifoid', 'Jl. Jati No. 9, Kec. Padang Timur', -0.9456, 100.3578, 0),
(@batch_id, 1, '2026-09-26', 'Rudi Hartono', 'J06', 'Infeksi saluran napas atas akut', 'Jl. Mahoni No. 23, Kec. Kuranji', -0.9112, 100.3823, 0),
(@batch_id, 1, '2026-09-27', 'Wati Supriyanti', 'L20', 'Dermatitis atopik', 'Jl. Cemara No. 12, Kec. Lubuk Begalung', -0.9756, 100.3598, 0),
(@batch_id, 1, '2026-09-28', 'Surya Dharma', 'K29', 'Gastritis dan duodenitis', 'Jl. Akasia No. 5, Kec. Padang Barat', -0.9545, 100.3478, 0);
