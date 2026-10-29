# PRD: Aplikasi Web GIS Pemetaan Distribusi Penyakit Berbasis Data Rekam Medis

| Atribut | Keterangan |
|---|---|
| Nama Produk | e-Matrika – Web GIS Pemetaan Distribusi Penyakit |
| Versi Dokumen | 1.1 (Draft) |
| Tanggal | 24 Oktober 2026 |
| Penyusun | Azra |
| Status | Draft untuk review |
| Pengguna | Dinas Kesehatan, Puskesmas, Petugas Rekam Medis |

## Riwayat Perubahan

| Versi | Tanggal | Perubahan |
|---|---|---|
| 1.0 | 23 Okt 2026 | Draf awal lengkap (anonimisasi penuh, banyak kolom data). |
| 1.1 | 24 Okt 2026 | Data peta disederhanakan menjadi **4 kolom** (tanggal kunjungan, nama, diagnosa, alamat). Ditambah halaman login sederhana, halaman admin untuk menambah pengguna, tampilan peta bergaya ArcGIS, dan tampilan aplikasi yang polos. Bagian anonimisasi diganti menjadi perlindungan data pasien yang sesuai dengan kebutuhan baru. |

---

## 1. Ringkasan Eksekutif

e-Matrika adalah aplikasi web berbasis Sistem Informasi Geografis (GIS) yang menampilkan sebaran kasus penyakit berdasarkan diagnosa (kode **ICD-10**) dan lokasi tempat tinggal pasien. Data bersumber dari rekam medis yang diekspor ke **file Excel (.xlsx/.csv)**.

Agar peta dapat tampil, file Excel **hanya perlu memuat empat kolom**:

1. Tanggal kunjungan
2. Nama
3. Diagnosa
4. Alamat

Produk ini **tidak dirancang untuk satu puskesmas saja**. Tujuan awalnya adalah **mencoba data Excel yang sudah dimiliki puskesmas**, sehingga sistem harus menerima nama kolom yang berbeda-beda, tahan terhadap data yang kurang rapi, dan bisa dipakai di banyak fasilitas kesehatan tanpa mengubah program.

Aplikasi dibuka melalui halaman login sederhana. Pengguna baru ditambahkan oleh admin melalui halaman khusus. Tampilan peta mengikuti gaya peta ArcGIS (peta dasar Esri, kontrol zoom, skala, dan koordinat), sedangkan seluruh antarmuka dibuat polos dengan latar warna saja dan bahasa sehari-hari.

---

## 2. Latar Belakang

1. Data rekam medis di puskesmas umumnya tersimpan dalam bentuk tabel (Excel) dan jarang dianalisis berdasarkan lokasi.
2. Dinas Kesehatan membutuhkan gambaran cepat tentang wilayah mana yang memiliki banyak kasus untuk pengambilan keputusan (penyuluhan, intervensi, kewaspadaan KLB).
3. Rekap manual per penyakit per bulan tidak menunjukkan pola sebaran dan tren waktu dengan mudah.
4. Data rekam medis bersifat sensitif sehingga pemanfaatannya harus mematuhi ketentuan perlindungan data pribadi dan kerahasiaan rekam medis.
5. Format Excel antar puskesmas tidak seragam (nama kolom, format tanggal, penulisan alamat dan kode ICD-10).

---

## 3. Rumusan Masalah

- Bagaimana mengubah data Excel yang hanya berisi tanggal kunjungan, nama, diagnosa, dan alamat menjadi peta sebaran penyakit tanpa keahlian GIS?
- Bagaimana menentukan titik lokasi pasien dari alamat berbentuk teks?
- Bagaimana menjaga agar data pasien yang tampil di peta hanya bisa dilihat oleh pengguna yang berwenang?

---

## 4. Tujuan Produk

### 4.1 Tujuan Bisnis
- Menyediakan alat bantu pemantauan penyakit berbasis wilayah untuk Dinas Kesehatan.
- Memanfaatkan data rekam medis yang sudah ada tanpa investasi sistem baru yang besar.
- Menyediakan prototipe yang dapat direplikasi di puskesmas lain.

### 4.2 Tujuan Produk
1. Impor data Excel dengan empat kolom wajib beserta pemeriksaan otomatis.
2. Menentukan titik lokasi dari alamat.
3. Menampilkan sebaran kasus sebagai **titik** dan **peta panas (heatmap)** di atas peta bergaya ArcGIS.
4. Filter berdasarkan rentang tanggal dan diagnosa.
5. Login sederhana dan pengelolaan pengguna oleh admin.
6. Mendukung banyak fasilitas kesehatan dengan pemisahan hak akses.

### 4.3 Indikator Keberhasilan

| Metrik | Target |
|---|---|
| Waktu dari unggah Excel sampai peta tampil (≤ 5.000 baris) | ≤ 90 detik |
| Baris Excel yang lolos pemeriksaan | ≥ 90% |
| Baris yang berhasil mendapat titik lokasi (langsung dari alamat atau perkiraan wilayah) | ≥ 85% |
| Waktu peta menyesuaikan setelah filter diubah | ≤ 3 detik |
| Akses ke data pasien oleh pengguna tanpa login | 0 |
| Skor kepuasan pengguna uji (skala 1–5) | ≥ 4 |

---

## 5. Ruang Lingkup

### 5.1 Dalam Lingkup (MVP)
- Halaman login sederhana.
- Halaman admin untuk menambah, melihat, dan menghapus pengguna.
- Unggah Excel/CSV dengan **4 kolom wajib**, pemetaan nama kolom, dan laporan hasil pemeriksaan.
- Penentuan titik lokasi dari alamat.
- Peta bergaya ArcGIS dengan tampilan titik dan peta panas.
- Filter tanggal dan diagnosa.
- Ringkasan jumlah kasus dan daftar kasus.
- Riwayat unggahan dan penghapusan per unggahan.
- Catatan aktivitas (siapa membuka/mengunggah apa dan kapan).

### 5.2 Di Luar Lingkup (MVP)
- Sambungan langsung ke SIMRS/e-Puskesmas/SATUSEHAT (fase berikutnya).
- Prediksi dan analisis statistik spasial lanjutan.
- Aplikasi mobile native.
- Penyimpanan NIK, nomor telepon, dan nomor rekam medis (tidak dibutuhkan; kolom ini diabaikan jika ada di file).
- Pemberitahuan otomatis KLB.
- Peta per wilayah berwarna (choropleth) dan perhitungan insidensi per penduduk (fase berikutnya, karena membutuhkan batas wilayah dan data penduduk).

---

## 6. Peran Pengguna

| Peran | Hak Akses | Kebutuhan Utama |
|---|---|---|
| **Admin** | Semua fitur, termasuk halaman Pengguna, unggah data, dan pengaturan | Mengelola pengguna dan data |
| **Petugas** | Melihat peta, memakai filter, dan (jika diizinkan admin) mengunggah data fasilitasnya | Melihat sebaran kasus dengan cepat |

Peran lain (Analis, Auditor, dan sebagainya) dipertimbangkan pada fase berikutnya.

---

## 7. User Stories

| ID | Sebagai | Saya ingin | Agar |
|---|---|---|---|
| US-01 | Pengguna | masuk lewat halaman login yang sederhana | langsung bisa membuka peta |
| US-02 | Admin | menambah pengguna baru (nama pengguna, kata sandi, peran) | orang yang berwenang bisa memakai aplikasi |
| US-03 | Admin | melihat daftar dan menghapus pengguna | akses selalu terkendali |
| US-04 | Petugas | mengunggah Excel berisi tanggal kunjungan, nama, diagnosa, dan alamat | kasus muncul di peta tanpa input manual |
| US-05 | Petugas | melihat baris yang gagal diproses beserta alasannya | saya bisa memperbaiki file |
| US-06 | Pengguna | memilih rentang tanggal | melihat kasus pada periode tertentu |
| US-07 | Pengguna | memilih diagnosa tertentu | fokus pada satu penyakit |
| US-08 | Pengguna | berpindah antara tampilan titik dan peta panas | mengenali kelompok kasus |
| US-09 | Pengguna | mengganti peta dasar (jalan, satelit, topografi) | menyesuaikan dengan kebutuhan |
| US-10 | Pengguna | mengklik titik dan melihat nama, diagnosa, tanggal, dan alamat | memeriksa kasusnya |

---

## 8. Kebutuhan Fungsional

Prioritas: **P0** = wajib MVP, **P1** = penting, **P2** = opsional/fase berikutnya.

### 8.1 Login

| ID | Kebutuhan | Prioritas |
|---|---|---|
| FR-LOG-01 | Halaman login hanya berisi dua isian (**nama pengguna** dan **kata sandi**) dan satu tombol **Masuk**. | P0 |
| FR-LOG-02 | Kedua isian **kosong** saat halaman dibuka. Tidak ada contoh isian, petunjuk cara mengisi, daftar kata sandi, atau tautan bantuan di halaman. | P0 |
| FR-LOG-03 | Akun awal untuk uji: nama pengguna **admin**, kata sandi **admin123**. | P0 |
| FR-LOG-04 | Jika salah, tampil satu pesan singkat: "Nama pengguna atau kata sandi salah." Pesan tidak menyebutkan mana yang salah. | P0 |
| FR-LOG-05 | Tombol **Keluar** tersedia di semua halaman setelah login. | P0 |
| FR-LOG-06 | Sesi berakhir otomatis jika tidak dipakai dalam waktu tertentu (default 30 menit). | P1 |
| FR-LOG-07 | Pembatasan percobaan masuk berulang (mis. kunci sementara 5 menit setelah 5 kali gagal). | P1 |

> **Catatan:** Kata sandi awal `admin123` hanya untuk prototipe dan uji coba. Sebelum aplikasi dipakai dengan data pasien yang sebenarnya, kata sandi ini wajib diganti dan seluruh kata sandi disimpan dalam bentuk hash (bukan teks biasa).

### 8.2 Halaman Pengguna (khusus Admin)

| ID | Kebutuhan | Prioritas |
|---|---|---|
| FR-USR-01 | Menu **Pengguna** hanya terlihat dan hanya bisa dibuka oleh Admin. | P0 |
| FR-USR-02 | Formulir **Tambah pengguna** berisi: nama pengguna, kata sandi, dan peran (Admin/Petugas). | P0 |
| FR-USR-03 | Pemeriksaan isian: nama pengguna minimal 3 karakter tanpa spasi dan belum dipakai; kata sandi minimal 6 karakter. | P0 |
| FR-USR-04 | Daftar pengguna menampilkan nama pengguna, peran, dan tombol **Hapus**. | P0 |
| FR-USR-05 | Hapus pengguna meminta konfirmasi di dalam halaman (klik kedua). Akun admin utama tidak bisa dihapus. | P0 |
| FR-USR-06 | Admin dapat mengganti kata sandi pengguna. | P1 |
| FR-USR-07 | Admin dapat menonaktifkan pengguna tanpa menghapusnya. | P2 |

### 8.3 Impor Data Excel

| ID | Kebutuhan | Prioritas |
|---|---|---|
| FR-IMP-01 | Mendukung unggah `.xlsx`, `.xls`, dan `.csv` (ukuran maksimum dapat diatur, default 25 MB). | P0 |
| FR-IMP-02 | Pratinjau 20 baris pertama sebelum diproses. | P0 |
| FR-IMP-03 | **Pemetaan kolom**: pengguna menentukan kolom Excel mana yang menjadi tanggal kunjungan, nama, diagnosa, dan alamat. Sistem menyarankan pilihan otomatis dari kemiripan nama kolom. | P0 |
| FR-IMP-04 | Pemetaan kolom dapat disimpan sebagai template per fasilitas. | P1 |
| FR-IMP-05 | Pilihan lembar (sheet) dan baris judul kolom. | P1 |
| FR-IMP-06 | Tanggal dibaca dari berbagai bentuk (dd/mm/yyyy, yyyy-mm-dd, angka tanggal Excel). | P0 |
| FR-IMP-07 | Diagnosa dapat berupa kode ICD-10 (mis. `A09`, `J06.9`) atau nama penyakit; sistem mencocokkan ke daftar ICD-10. | P0 |
| FR-IMP-08 | Laporan hasil: jumlah baris berhasil, gagal, kembar, diagnosa tidak dikenal, dan alamat yang tidak bisa ditentukan lokasinya. | P0 |
| FR-IMP-09 | Baris yang gagal dapat diunduh sebagai CSV beserta alasan. | P1 |
| FR-IMP-10 | Baris kembar (nama + tanggal + diagnosa sama) ditandai dan tidak dihitung dua kali. | P1 |
| FR-IMP-11 | Riwayat unggahan dengan kemampuan menghapus seluruh data satu unggahan. | P0 |
| FR-IMP-12 | File asli dihapus otomatis setelah diproses (default ≤ 24 jam). | P0 |

**Kolom yang dibutuhkan:**

| Kolom | Wajib | Keterangan |
|---|---|---|
| Tanggal kunjungan | Ya | Tanggal pasien datang/didiagnosa |
| Nama | Ya | Nama pasien |
| Diagnosa | Ya | Kode ICD-10 atau nama penyakit |
| Alamat | Ya | Alamat tempat tinggal (jalan, kelurahan/desa, kecamatan) |
| Kolom lain (NIK, telepon, nomor RM, umur, dsb.) | Tidak | Diabaikan dan tidak disimpan |

### 8.4 Penentuan Lokasi dari Alamat

| ID | Kebutuhan | Prioritas |
|---|---|---|
| FR-GEO-01 | Setiap alamat diubah menjadi titik koordinat melalui layanan pencarian alamat. | P0 |
| FR-GEO-02 | Jika alamat tidak ditemukan secara rinci, sistem membaca nama **kelurahan/desa atau kecamatan** dari teks alamat dan menempatkan titik di pusat wilayah tersebut, dengan tanda "lokasi perkiraan". | P0 |
| FR-GEO-03 | Titik yang jatuh di luar wilayah kerja (mis. di luar kabupaten/kota yang ditetapkan) ditandai dan tidak ditampilkan sampai diperbaiki. | P1 |
| FR-GEO-04 | Hasil pencarian alamat disimpan agar alamat yang sama tidak dicari ulang. | P1 |
| FR-GEO-05 | Admin dapat memperbaiki titik yang salah dengan memindahkan penanda di peta. | P1 |
| FR-GEO-06 | Wilayah kerja (kabupaten/kota) dapat diganti tanpa mengubah program. | P1 |

### 8.5 Peta (gaya ArcGIS)

| ID | Kebutuhan | Prioritas |
|---|---|---|
| FR-MAP-01 | Peta dasar dari Esri: **Jalan**, **Citra satelit**, **Topografi**, dan **Abu-abu terang**, dapat diganti oleh pengguna. | P0 |
| FR-MAP-02 | Kontrol bergaya ArcGIS: tombol perbesar/perkecil, tombol **Kembali ke semua kasus**, skala peta, dan tampilan koordinat saat kursor bergerak. | P0 |
| FR-MAP-03 | **Tampilan titik**: setiap kasus berupa satu titik, diberi warna sesuai diagnosa, dengan legenda. | P0 |
| FR-MAP-04 | **Tampilan peta panas**: kepadatan kasus dengan gradasi warna dan legenda tingkat kepadatan. | P0 |
| FR-MAP-05 | Klik titik membuka kotak informasi berisi: **nama, diagnosa (kode dan nama), tanggal kunjungan, dan alamat**. | P0 |
| FR-MAP-06 | Titik yang sangat berdekatan dikelompokkan otomatis saat peta diperkecil. | P1 |
| FR-MAP-07 | Titik "lokasi perkiraan" diberi tanda berbeda. | P1 |
| FR-MAP-08 | Simpan peta sebagai gambar. | P2 |
| FR-MAP-09 | Peta per wilayah berwarna (choropleth) per kecamatan/kelurahan. | P2 |

Keterangan: gaya ArcGIS dimaksudkan pada **tampilan dan perilaku peta** (peta dasar Esri dan kontrolnya). Aplikasi dapat dibangun dengan pustaka peta terbuka (mis. Leaflet) yang memuat peta dasar Esri, atau dengan ArcGIS Maps SDK for JavaScript jika organisasi memiliki lisensi/akun ArcGIS.

### 8.6 Filter dan Ringkasan

| ID | Kebutuhan | Prioritas |
|---|---|---|
| FR-FIL-01 | Filter **tanggal dari** dan **sampai**. Nilai awal: satu bulan terakhir atau seluruh rentang data. | P0 |
| FR-FIL-02 | Filter **diagnosa** (satu diagnosa atau semua), dengan pencarian berdasarkan kode atau nama. | P0 |
| FR-FIL-03 | Filter bab ICD-10 (mis. A00–B99 Penyakit infeksi dan parasit). | P1 |
| FR-FIL-04 | Filter fasilitas kesehatan (sesuai hak akses). | P1 |
| FR-FIL-05 | Ringkasan di samping peta: jumlah kasus dan jumlah jenis diagnosa pada pilihan saat ini. | P0 |
| FR-FIL-06 | **Daftar kasus** (tanggal, nama, diagnosa, alamat) mengikuti filter; klik baris akan memusatkan peta ke titik tersebut. | P0 |
| FR-FIL-07 | Grafik jumlah kasus per hari/minggu. | P2 |

---

## 9. Antarmuka dan Tampilan

Prinsip: **sederhana, polos, dan mudah dipahami petugas non-teknis.**

| Aspek | Ketentuan |
|---|---|
| Latar | Warna polos saja (tanpa gambar latar, gradasi, atau pola). Halaman login berlatar satu warna biru tua; halaman lain berlatar abu-abu kebiruan terang; panel dan formulir berwarna putih. |
| Warna aksen | Biru khas peta Esri untuk tombol dan elemen aktif. |
| Bahasa | Bahasa Indonesia sehari-hari. **Tidak ada istilah teknis** (seperti API, server, database, token, JSON) di layar. |
| Halaman login | Judul kecil, dua isian kosong, satu tombol. Tanpa petunjuk, tanpa contoh kata sandi. |
| Struktur | Bilah atas dengan nama aplikasi, menu **Peta** dan **Pengguna** (khusus Admin), nama pengguna, dan tombol **Keluar**. |
| Halaman Peta | Panel kiri berisi filter, ringkasan, dan daftar kasus; peta mengisi sisa layar. Pada layar kecil, panel berada di atas peta. |
| Halaman Pengguna | Formulir tambah pengguna di atas, daftar pengguna di bawah. |
| Pesan | Singkat dan jelas, mis. "Pengguna ditambahkan." atau "Nama pengguna sudah dipakai." |

**Sketsa tata letak halaman Peta:**

```
+----------------------------------------------------------------+
| Peta Sebaran Penyakit   [Peta] [Pengguna]        admin [Keluar] |
+---------------------+------------------------------------------+
| Tanggal dari/sampai |  [+]                          [peta dasar]|
| Diagnosa            |  [⌂]                                     |
| Tampilan: Titik|Panas|           (peta bergaya ArcGIS)          |
| Peta dasar          |                                          |
| ------------------- |                                          |
| 54 kasus · 6 diag.  |                          [legenda]       |
| Daftar kasus ...    |  koordinat            skala              |
+---------------------+------------------------------------------+
```

---

## 10. Perlindungan Data Pasien

Pada versi 1.0, nama dan alamat dibuang saat impor. Pada versi 1.1, peta **membutuhkan nama dan alamat**, sehingga kedua data ini disimpan dan ditampilkan kepada pengguna yang sudah login. Karena itu perlindungan dipusatkan pada **siapa yang boleh melihat** dan **bagaimana data dijaga**.

### 10.1 Aturan

| Data | Perlakuan |
|---|---|
| Nama, alamat | Disimpan terenkripsi; hanya tampil setelah login; tidak pernah tampil di halaman yang bisa dibuka tanpa login. |
| Tanggal kunjungan, diagnosa | Disimpan dan ditampilkan sesuai filter. |
| NIK, telepon, nomor RM, kolom lain | **Tidak disimpan.** Jika ada di file, kolom tersebut dilewati saat impor. |
| Diagnosa sensitif (mis. HIV/AIDS `B20–B24`, penyakit menular seksual, gangguan jiwa `F00–F99`) | Nama dan alamat **tidak ditampilkan** pada peta dan daftar; hanya jumlah per kecamatan. Daftar kode sensitif dapat diatur admin. |
| File Excel asli | Dihapus otomatis setelah diproses. |
| Ekspor | Ekspor data per orang dinonaktifkan secara bawaan; ekspor hanya memuat jumlah. |

### 10.2 Pengaturan Tampilan Nama
Admin dapat memilih cara nama tampil di peta dan daftar: **nama lengkap** atau **inisial** (mis. "S. R."). Pilihan ini membantu bila peta ditampilkan di depan banyak orang (rapat, presentasi).

### 10.3 Pengendalian Tambahan
- Setiap kali pengguna membuka rincian kasus, mengunggah, atau menghapus data, kejadian dicatat dalam catatan aktivitas (tanpa menyalin nama pasien ke catatan).
- Sembunyikan nama dan alamat pada titik yang diperkecil menjadi kelompok; rincian baru tampil saat peta diperbesar.
- Data uji dan demo memakai **data fiktif**.

### 10.4 Dasar Kepatuhan (untuk ditinjau pihak berwenang)
- UU No. 27 Tahun 2022 tentang Pelindungan Data Pribadi (data kesehatan termasuk data pribadi yang bersifat spesifik).
- Permenkes No. 24 Tahun 2022 tentang Rekam Medis.
- UU No. 17 Tahun 2023 tentang Kesehatan.
- Kebijakan internal fasilitas kesehatan dan Dinas Kesehatan setempat.

> **Catatan:** Dokumen ini bukan nasihat hukum. Sebelum dipakai dengan data pasien yang sebenarnya, perlu persetujuan pimpinan fasilitas dan konsultasi dengan pihak hukum atau pengelola data setempat.

---

## 11. Kebutuhan Non-Fungsional

### 11.1 Keamanan Data

| ID | Kebutuhan |
|---|---|
| NFR-SEC-01 | Seluruh lalu lintas data memakai HTTPS (TLS 1.2+). |
| NFR-SEC-02 | Basis data dan penyimpanan file terenkripsi; kunci disimpan terpisah dari aplikasi. Kolom nama dan alamat dienkripsi tersendiri. |
| NFR-SEC-03 | Kata sandi disimpan sebagai hash (Argon2id/bcrypt), tidak pernah sebagai teks biasa. Kata sandi awal `admin123` wajib diganti sebelum pemakaian data riil. |
| NFR-SEC-04 | Pembatasan percobaan masuk, penguncian sementara, dan sesi yang berakhir otomatis. |
| NFR-SEC-05 | Hak akses berdasarkan peran; Petugas hanya melihat data fasilitasnya, dan halaman Pengguna hanya untuk Admin. Pemeriksaan dilakukan di sisi server, bukan hanya dengan menyembunyikan menu. |
| NFR-SEC-06 | Catatan aktivitas yang tidak dapat diubah (login, unggah, hapus, perubahan pengguna, pembukaan rincian kasus), disimpan minimal 1 tahun. |
| NFR-SEC-07 | Keamanan unggahan: pemeriksaan jenis dan ukuran file, pemindaian virus, penyimpanan sementara terisolasi, penghapusan otomatis, dan penetralan sel Excel yang diawali `=`, `+`, `-`, `@`. |
| NFR-SEC-08 | Perlindungan terhadap serangan web umum (OWASP Top 10): SQL Injection, XSS, CSRF, IDOR. Teks nama dan alamat selalu di-escape saat ditampilkan. |
| NFR-SEC-09 | Lingkungan pengembangan, uji, dan produksi terpisah; **data pasien asli tidak dipakai di luar produksi**. |
| NFR-SEC-10 | Kredensial dan kunci tidak ditulis di dalam kode atau repositori. |
| NFR-SEC-11 | Cadangan terenkripsi harian dengan uji pemulihan berkala. |
| NFR-SEC-12 | Kebijakan retensi: data dapat dihapus per unggahan atau per fasilitas, termasuk dari cadangan sesuai siklusnya. |
| NFR-SEC-13 | Pembatasan jumlah permintaan data per pengguna untuk mencegah pengambilan massal. |
| NFR-SEC-14 | Prosedur penanganan insiden kebocoran data sesuai ketentuan yang berlaku. |
| NFR-SEC-15 | Pembaruan pustaka dan pemindaian kerentanan berkala. |
| NFR-SEC-16 | Pencarian alamat memakai layanan yang disetujui organisasi; alamat dikirim seminimal mungkin dan perjanjian penggunaan layanan diperiksa lebih dulu. |

### 11.2 Kinerja

| ID | Kebutuhan |
|---|---|
| NFR-PERF-01 | Impor 5.000 baris selesai ≤ 90 detik; berkas lebih besar diproses di latar belakang dengan indikator kemajuan. |
| NFR-PERF-02 | Hasil filter muncul ≤ 2 detik untuk 100.000 kasus. |
| NFR-PERF-03 | Peta tetap lancar pada ≥ 20.000 titik (pengelompokan titik, penyajian bertahap). |

### 11.3 Ketersediaan
- Ketersediaan ≥ 99% pada jam kerja.
- Impor bersifat utuh: bila gagal di tengah, tidak ada data setengah jadi.

### 11.4 Skalabilitas dan Portabilitas
- Banyak fasilitas dalam satu aplikasi; fasilitas baru ditambah lewat pengaturan.
- Wilayah kerja tidak ditulis mati di dalam program.
- Dapat dipasang dengan Docker di server Dinkes atau layanan cloud.

### 11.5 Kemudahan Pakai
- Bahasa Indonesia, tanpa istilah teknis di layar.
- Responsif (komputer utama, tablet dan ponsel didukung).
- Kontras warna memadai dan fokus papan ketik terlihat jelas.

### 11.6 Kompatibilitas
- Chrome, Edge, Firefox, dan Safari (dua versi terakhir).

---

## 12. Arsitektur dan Rekomendasi Teknologi (Usulan)

| Lapisan | Opsi Utama | Alternatif |
|---|---|---|
| Tampilan | HTML/JS dengan **Leaflet** + peta dasar Esri, Leaflet.heat untuk peta panas | ArcGIS Maps SDK for JavaScript; MapLibre GL JS |
| Backend | Laravel (PHP) | Node.js, FastAPI, CodeIgniter |
| Basis data | PostgreSQL + PostGIS | MySQL 8 |
| Baca Excel | PhpSpreadsheet / SheetJS | OpenSpout |
| Pencarian alamat | Layanan pencarian alamat yang disetujui (mis. Esri World Geocoder, Nominatim di server sendiri) | Daftar kelurahan/kecamatan lokal sebagai cadangan |
| Antrian pekerjaan | Redis + queue worker | Antrian di basis data |
| Penyajian | Docker Compose, Nginx, HTTPS | — |

**Alur data:**

```
Excel/CSV → Unggah aman → Pemetaan kolom → Pemeriksaan (tanggal, ICD-10, alamat)
→ Penentuan titik dari alamat → Simpan (terenkripsi) → Peta (titik / peta panas) + Daftar kasus
```

---

## 13. Model Data (Konseptual)

**`facilities`**: id, nama, kode, jenis

**`users`**: id, username, password_hash, peran, facility_id, aktif, dibuat_pada

**`import_batches`**: id, facility_id, user_id, nama_file, status, total_baris, baris_berhasil, baris_gagal, dibuat_pada

**`cases`**: id, batch_id, facility_id, tanggal_kunjungan, nama (terenkripsi), icd10_code, alamat (terenkripsi), geom (Point), lokasi_perkiraan (boolean)

**`icd10_ref`**: code, nama_id, nama_en, bab, blok, sensitif

**`geocode_cache`**: alamat_hash, geom, sumber, diperbarui_pada

**`column_mapping_templates`**: id, facility_id, nama, mapping_json

**`audit_logs`**: id, user_id, aksi, objek, ip, waktu (tanpa menyalin nama pasien)

---

## 14. Alur Pengguna Utama

**Alur A – Masuk dan melihat peta**
1. Pengguna membuka aplikasi dan melihat halaman login dengan isian kosong.
2. Memasukkan nama pengguna dan kata sandi, lalu menekan **Masuk**.
3. Peta tampil. Pengguna mengatur tanggal dan diagnosa, memilih Titik atau Peta panas, lalu mengklik titik untuk melihat rincian.

**Alur B – Admin menambah pengguna**
1. Admin membuka menu **Pengguna**.
2. Mengisi nama pengguna, kata sandi, dan peran, lalu menekan **Tambah**.
3. Pengguna baru muncul di daftar dan dapat langsung masuk.

**Alur C – Impor data Excel**
1. Petugas membuka halaman unggah dan memilih file.
2. Sistem menampilkan pratinjau dan saran pemetaan kolom (tanggal kunjungan, nama, diagnosa, alamat).
3. Petugas memeriksa pemetaan lalu menekan **Proses**.
4. Sistem menampilkan laporan hasil. Kasus yang berhasil langsung tampil di peta.

---

## 15. Kriteria Penerimaan

- **AC-01** Halaman login tampil dengan dua isian kosong dan tanpa petunjuk, contoh isian, atau daftar kata sandi.
- **AC-02** Login dengan `admin` / `admin123` berhasil; kombinasi lain ditolak dengan pesan yang sama.
- **AC-03** Menu **Pengguna** hanya tampil untuk Admin, dan alamat halaman tersebut ditolak bagi Petugas.
- **AC-04** Admin dapat menambah pengguna; pengguna baru dapat login; nama pengguna kembar ditolak.
- **AC-05** File Excel dengan empat kolom (nama kolom berbeda dari template) berhasil diproses setelah pemetaan kolom, dan ≥ 90% baris tampil di peta.
- **AC-06** Klik titik menampilkan nama, diagnosa, tanggal kunjungan, dan alamat.
- **AC-07** Filter tanggal + diagnosa menghasilkan jumlah yang sama di peta, ringkasan, dan daftar kasus.
- **AC-08** Pergantian antara tampilan titik dan peta panas, serta antar peta dasar, berjalan tanpa memuat ulang halaman.
- **AC-09** Tidak ada kolom NIK/telepon/nomor RM yang tersimpan setelah impor.
- **AC-10** Diagnosa sensitif tidak menampilkan nama dan alamat di peta maupun daftar.
- **AC-11** Tidak ada tulisan istilah teknis (API, server, database, dll.) di layar.
- **AC-12** Seluruh unggahan, penghapusan, dan perubahan pengguna tercatat di catatan aktivitas.
- **AC-13** Pengguna tanpa login tidak dapat membuka data pasien lewat cara apa pun.

---

## 16. Rencana Rilis

| Fase | Cakupan | Estimasi |
|---|---|---|
| **Fase 0 – Persiapan** | Kumpulkan contoh Excel, izin akses data, daftar ICD-10, pilih layanan pencarian alamat | 2 minggu |
| **Fase 1 – MVP** | Login, halaman Pengguna, impor Excel 4 kolom, penentuan titik dari alamat, peta (titik, peta panas, peta dasar Esri), filter tanggal dan diagnosa | 6–8 minggu |
| **Fase 2 – Penguatan** | Banyak fasilitas, catatan aktivitas lengkap, enkripsi kolom, batas percobaan masuk, pengelompokan titik | 4 minggu |
| **Fase 3 – Uji dan Pilot** | Uji pengguna di beberapa puskesmas, uji keamanan dasar, perbaikan | 3–4 minggu |
| **Fase 4 – Lanjutan** | Peta per wilayah (choropleth), insidensi per penduduk, sambungan ke SIMRS/SATUSEHAT, grafik tren | Setelah pilot |

---

## 17. Asumsi dan Ketergantungan

**Asumsi**
- Puskesmas dapat mengekspor Excel yang memuat tanggal kunjungan, nama, diagnosa, dan alamat.
- Alamat memuat setidaknya kelurahan/desa atau kecamatan agar titik perkiraan dapat dibuat.
- Layanan pencarian alamat dapat dipakai dengan data seperti ini sesuai ketentuan layanannya.

**Ketergantungan**
- Izin pemanfaatan data dari pimpinan fasilitas/Dinkes.
- Tabel referensi ICD-10.
- Hak pakai peta dasar Esri sesuai ketentuan penyedia (atribusi tetap ditampilkan di peta).
- Server atau layanan hosting yang aman.

---

## 18. Risiko dan Mitigasi

| Risiko | Dampak | Mitigasi |
|---|---|---|
| Nama dan alamat tampil di peta | Risiko privasi lebih tinggi dibanding versi 1.0 | Akses hanya setelah login, enkripsi, catatan aktivitas, pilihan tampil inisial, sembunyikan diagnosa sensitif, data uji fiktif |
| Alamat ditulis tidak baku | Titik salah atau tidak ketemu | Titik perkiraan dari kelurahan/kecamatan, laporan alamat bermasalah, perbaikan titik manual |
| Alamat dikirim ke layanan pencarian pihak ketiga | Data alamat keluar dari sistem | Pilih layanan yang disetujui atau pasang pencari alamat di server sendiri; kirim alamat tanpa nama |
| Kata sandi awal `admin123` terlalu lemah | Akses tidak sah | Wajib diganti sebelum data riil; pembatasan percobaan masuk |
| Format Excel beragam | Banyak baris gagal | Pemetaan kolom, template tersimpan, laporan hasil |
| Kode ICD-10 salah atau tidak lengkap | Hasil analisis bias | Pencocokan ke daftar ICD-10, laporan, pelatihan petugas |
| Hanya kasus yang datang ke puskesmas yang tercatat | Sebaran tampak tidak utuh | Keterangan batas data di halaman peta dan laporan |
| Ketiadaan izin | Proyek terhenti | Libatkan pimpinan dan pihak hukum sejak Fase 0; mulai dengan data fiktif |

---

## 19. Pertanyaan Terbuka

1. Wilayah kerja awal (kabupaten/kota) yang dipakai untuk membatasi dan memeriksa titik?
2. Layanan pencarian alamat mana yang akan dipakai (berbayar/gratis, di server sendiri atau layanan luar)?
3. Apakah alamat pada data puskesmas sudah memuat nama jalan, atau hanya kelurahan/kecamatan?
4. Apakah nama ditampilkan lengkap atau inisial sebagai pengaturan awal?
5. Daftar kode ICD-10 sensitif yang disembunyikan perlu ditetapkan bersama Dinkes.
6. Apakah ada lebih dari satu diagnosa per kunjungan?
7. Apakah organisasi memiliki akun/lisensi ArcGIS, atau cukup memakai peta dasar Esri melalui pustaka terbuka?

---

## 20. Lampiran

### Lampiran A – Contoh Template Excel (data fiktif)

| Tanggal Kunjungan | Nama | Diagnosa | Alamat |
|---|---|---|---|
| 2026-09-14 | Siti Rahma (contoh) | A09 | Jl. Melati No. 12, Kec. Padang Timur |
| 2026-09-15 | Budi Santoso (contoh) | J06.9 | Jl. Kenanga No. 3, Kec. Nanggalo |
| 2026-09-15 | Ani Lestari (contoh) | I10 | Jl. Cempaka No. 8, Kec. Kuranji |

*(Seluruh nama dan alamat pada contoh ini fiktif.)*

### Lampiran B – Pengelompokan ICD-10 (Bab)

| Bab | Rentang | Judul Singkat |
|---|---|---|
| I | A00–B99 | Penyakit infeksi dan parasit tertentu |
| II | C00–D48 | Neoplasma |
| IV | E00–E90 | Penyakit endokrin, nutrisi, dan metabolik |
| IX | I00–I99 | Penyakit sistem sirkulasi |
| X | J00–J99 | Penyakit sistem pernapasan |
| XI | K00–K93 | Penyakit sistem pencernaan |
| XV | O00–O99 | Kehamilan, persalinan, dan nifas |
| XIX | S00–T98 | Cedera, keracunan, dan akibat penyebab eksternal |

*(Daftar lengkap diambil dari tabel referensi ICD-10 resmi saat implementasi.)*

### Lampiran C – Istilah

- **ICD-10**: Klasifikasi Penyakit Internasional revisi ke-10 (WHO).
- **Peta panas (heatmap)**: peta kepadatan kasus dengan gradasi warna.
- **Choropleth**: peta yang mewarnai wilayah menurut jumlah kasus.
- **Peta dasar**: lapisan peta di bawah titik kasus (jalan, citra satelit, topografi).
- **Pencarian alamat (geocoding)**: mengubah alamat teks menjadi koordinat.
