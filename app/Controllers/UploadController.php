<?php

namespace App\Controllers;

use App\Libraries\ExcelReader;
use App\Libraries\PadangGeocoder;
use App\Models\CaseModel;
use App\Models\ImportBatchModel;
use App\Models\Icd10Model;
use App\Models\GeocodeCacheModel;
use App\Models\AuditLogModel;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;

class UploadController extends BaseController
{
    public function index(): string
    {
        $batchModel = new ImportBatchModel();
        $batches = $batchModel->getWithUser();

        return view('layouts/main', [
            'pageTitle'  => 'Unggah Data',
            'activePage' => 'upload',
            'content'    => view('upload/index', ['batches' => $batches]),
        ]);
    }

    /**
     * Unduh template format CSV contoh untuk uji coba impor data.
     */
    public function template(): ResponseInterface
    {
        $csv = "\xEF\xBB\xBF"
            . "Tanggal Kunjungan,Nama Pasien,Diagnosa,Alamat Lengkap\r\n"
            . "2026-09-15,Budi Santoso,A90 - Demam dengue,Jl. Sudirman No. 12 Padang\r\n"
            . "2026-09-16,Siti Rahma,A09 - Diare dan gastroenteritis,Jl. Khatib Sulaiman No. 45 Padang\r\n"
            . "2026-09-17,Ahmad Fauzi,A15 - Tuberkulosis paru,Jl. Veteran No. 8 Padang\r\n"
            . "2026-09-18,Nurul Hidayah,J18 - Pneumonia,Jl. Pemuda No. 20 Padang\r\n"
            . "2026-09-19,Rian Pratama,B20 - Penyakit HIV,Jl. M. Yamin No. 15 Padang\r\n";

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="template_data_kasus_ematrika.csv"')
            ->setBody($csv);
    }

    /**
     * Step 1: Upload file → show preview + column mapping.
     */
    public function upload(): string|RedirectResponse
    {
        $file = $this->request->getFile('datafile');

        if (!$file) {
            return redirect()->back()->with('error', 'Silakan pilih berkas Excel atau CSV terlebih dahulu.');
        }

        if (!$file->isValid()) {
            $err = $file->getErrorString();
            return redirect()->back()->with('error', 'Gagal mengunggah berkas: ' . $err . ' (Kode: ' . $file->getError() . ')');
        }

        $originalName = $file->getClientName();
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        if (empty($ext)) {
            $ext = strtolower($file->getClientExtension());
        }
        if (empty($ext)) {
            $ext = strtolower($file->getExtension());
        }

        if (!in_array($ext, ['csv', 'xlsx', 'xls'])) {
            return redirect()->back()->with('error', 'Format berkas "' . esc($ext) . '" tidak didukung. Mohon gunakan berkas .xlsx, .xls, atau .csv.');
        }

        // Max 25 MB
        if ($file->getSizeByUnit('mb') > 25) {
            return redirect()->back()->with('error', 'Ukuran berkas melebihi batas maksimum 25 MB.');
        }

        // Pastikan folder uploads ada
        $uploadDir = WRITEPATH . 'uploads';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Simpan sementara dengan ekstensi yang sesuai
        $newName = bin2hex(random_bytes(16)) . '.' . $ext;
        $file->move($uploadDir, $newName);
        $filePath = $uploadDir . DIRECTORY_SEPARATOR . $newName;

        try {
            $reader = new ExcelReader();
            $reader->read($filePath, 0, $ext); // 0 = deteksi otomatis header
            $headers = $reader->getHeaders();
            $preview = $reader->preview(20);
            $headerRow = $reader->getDetectedHeaderRow();
            $candidates = $reader->getCandidateHeaderRows();
        } catch (\Throwable $e) {
            @unlink($filePath);
            return redirect()->back()->with('error', 'Gagal membaca berkas: ' . $e->getMessage());
        }

        if (empty($headers)) {
            @unlink($filePath);
            return redirect()->back()->with('error', 'Berkas kosong atau tidak memiliki judul kolom.');
        }

        // Auto-suggest column mapping
        $suggestions = $this->suggestMapping($headers);

        // Store file info in session for step 2
        session()->set('upload_file', $filePath);
        session()->set('upload_original', $originalName);
        session()->set('upload_ext', $ext);
        session()->set('upload_header_row', $headerRow);

        return view('layouts/main', [
            'pageTitle'  => 'Pemetaan Kolom',
            'activePage' => 'upload',
            'content'    => view('upload/mapping', [
                'headers'             => $headers,
                'preview'             => $preview,
                'suggestions'         => $suggestions,
                'fileName'            => $originalName,
                'headerRow'           => $headerRow,
                'candidateHeaderRows' => $candidates,
            ]),
        ]);
    }

    /**
     * Memuat ulang pemetaan kolom jika pengguna mengganti baris header.
     */
    public function mapping(): string|RedirectResponse
    {
        $filePath = session()->get('upload_file');
        $originalName = session()->get('upload_original');
        $ext = session()->get('upload_ext') ?? pathinfo((string)$filePath, PATHINFO_EXTENSION);

        if (!$filePath || !file_exists($filePath)) {
            return redirect()->to(base_url('/upload'))->with('error', 'Sesi unggahan telah berakhir. Silakan pilih berkas kembali.');
        }

        $headerRow = (int)($this->request->getGet('header_row') ?: session()->get('upload_header_row') ?: 0);

        try {
            $reader = new ExcelReader();
            $reader->read($filePath, $headerRow, $ext);
            $headers = $reader->getHeaders();
            $preview = $reader->preview(20);
            $effectiveHeaderRow = $reader->getDetectedHeaderRow();
            $candidates = $reader->getCandidateHeaderRows();
        } catch (\Throwable $e) {
            return redirect()->to(base_url('/upload'))->with('error', 'Gagal membaca berkas: ' . $e->getMessage());
        }

        session()->set('upload_header_row', $effectiveHeaderRow);
        $suggestions = $this->suggestMapping($headers);

        return view('layouts/main', [
            'pageTitle'  => 'Pemetaan Kolom',
            'activePage' => 'upload',
            'content'    => view('upload/mapping', [
                'headers'             => $headers,
                'preview'             => $preview,
                'suggestions'         => $suggestions,
                'fileName'            => $originalName,
                'headerRow'           => $effectiveHeaderRow,
                'candidateHeaderRows' => $candidates,
            ]),
        ]);
    }

    /**
     * Step 2: Process file with mapped columns.
     */
    public function process(): string|RedirectResponse
    {
        @set_time_limit(0);
        @ini_set('max_execution_time', '0');
        @ini_set('memory_limit', '512M');

        $filePath = session()->get('upload_file');
        $originalName = session()->get('upload_original');
        $ext = session()->get('upload_ext') ?? pathinfo((string)$filePath, PATHINFO_EXTENSION);
        $headerRow = (int)($this->request->getPost('header_row') ?: session()->get('upload_header_row') ?: 0);

        if (!$filePath || !file_exists($filePath)) {
            return redirect()->to(base_url('/upload'))->with('error', 'Sesi berkas sudah berakhir atau berkas tidak ditemukan. Silakan unggah ulang.');
        }

        $colTanggal  = $this->request->getPost('col_tanggal');
        $colNama     = $this->request->getPost('col_nama');
        $colDiagnosa = $this->request->getPost('col_diagnosa');
        $colAlamat   = $this->request->getPost('col_alamat');

        if (!$colTanggal || !$colNama || !$colDiagnosa || !$colAlamat) {
            return redirect()->back()->with('error', 'Semua kolom wajib dipetakan.');
        }

        // Read file
        $reader = new ExcelReader();
        $reader->read($filePath, $headerRow, $ext);
        $rows = $reader->getRows();
        $effectiveHeaderRow = $reader->getDetectedHeaderRow();

        // Create batch
        $batchModel = new ImportBatchModel();
        $batchId = $batchModel->insert([
            'facility_id'    => session()->get('facility_id'),
            'user_id'        => session()->get('user_id'),
            'nama_file'      => $originalName,
            'status'         => 'processing',
            'total_baris'    => count($rows),
            'baris_berhasil' => 0,
            'baris_gagal'    => 0,
        ]);

        $caseModel    = new CaseModel();
        $icd10Model   = new Icd10Model();
        $geocodeCache = new GeocodeCacheModel();
        $auditLog     = new AuditLogModel();

        $success = 0;
        $failed = 0;
        $failedRows = [];
        $duplicates = 0;

        // In-memory caches to speed up large datasets (e.g. 5,000+ rows in < 2 seconds)
        $seenInBatch = [];
        $icdCache = [];
        $geoMemoryCache = [];
        $newGeocodesToInsert = [];
        $casesToInsert = [];
        $facilityId = session()->get('facility_id');
        $totalRows = count($rows);

        // Preload geocache for unique addresses in this batch to avoid per-row DB roundtrips
        $addrHashes = [];
        foreach ($rows as $r) {
            $addr = trim((string)($r[$colAlamat] ?? ''));
            if ($addr !== '') {
                $lowerAddr = mb_strtolower($addr);
                $addrHashes[hash('sha256', $lowerAddr)] = $lowerAddr;
            }
        }
        if (!empty($addrHashes)) {
            $hashChunks = array_chunk(array_keys($addrHashes), 500);
            foreach ($hashChunks as $chunk) {
                $cachedRows = $geocodeCache->whereIn('alamat_hash', $chunk)->findAll();
                foreach ($cachedRows as $cRow) {
                    $lowerKey = $addrHashes[$cRow['alamat_hash']] ?? null;
                    if ($lowerKey !== null) {
                        $geoMemoryCache[$lowerKey] = [
                            'lat'       => (float)$cRow['latitude'],
                            'lng'       => (float)$cRow['longitude'],
                            'perkiraan' => ($cRow['sumber'] ?? '') !== 'nominatim_house',
                        ];
                    }
                }
            }
        }

        foreach ($rows as $idx => $row) {
            $rowNum = $idx + $effectiveHeaderRow + 1;

            // Extract values
            $tanggalRaw  = $row[$colTanggal] ?? '';
            $nama        = trim($row[$colNama] ?? '');
            $diagnosaRaw = trim($row[$colDiagnosa] ?? '');
            $alamat      = trim($row[$colAlamat] ?? '');

            // Sanitize: strip formula injection characters
            foreach ([$nama, $diagnosaRaw, $alamat] as &$val) {
                if (preg_match('/^[=+\-@]/', $val)) {
                    $val = "'" . $val;
                }
            }
            unset($val);

            // Validate required fields
            if (empty($nama) || empty($alamat)) {
                $failed++;
                if (count($failedRows) < 100) {
                    $failedRows[] = ['baris' => $rowNum, 'alasan' => 'Nama atau alamat kosong.'];
                }
                continue;
            }

            // Parse date
            $tanggal = ExcelReader::excelDateToPhp($tanggalRaw);
            if (!$tanggal) {
                $failed++;
                if (count($failedRows) < 100) {
                    $failedRows[] = ['baris' => $rowNum, 'alasan' => 'Tanggal tidak bisa dibaca: ' . $tanggalRaw];
                }
                continue;
            }

            // Match ICD-10 (cached in memory)
            if (!isset($icdCache[$diagnosaRaw])) {
                $icd10 = $icd10Model->matchDiagnosis($diagnosaRaw);
                $icdCache[$diagnosaRaw] = [
                    'code' => $icd10 ? $icd10['code'] : null,
                    'teks' => $icd10 ? $icd10['nama_id'] : $diagnosaRaw,
                ];
            }
            $icd10Code = $icdCache[$diagnosaRaw]['code'];
            $diagnosaTeks = $icdCache[$diagnosaRaw]['teks'];

            // Check duplicate in-memory
            $dupKey = mb_strtolower($nama) . '|' . $tanggal . '|' . ($icd10Code ?? mb_strtolower($diagnosaRaw));
            if (isset($seenInBatch[$dupKey])) {
                $duplicates++;
                continue;
            }
            $seenInBatch[$dupKey] = true;

            // Geocode address
            $lat = null;
            $lng = null;
            $perkiraan = false;

            $addrKey = mb_strtolower($alamat);
            if (isset($geoMemoryCache[$addrKey])) {
                $lat = $geoMemoryCache[$addrKey]['lat'];
                $lng = $geoMemoryCache[$addrKey]['lng'];
                $perkiraan = $geoMemoryCache[$addrKey]['perkiraan'];
            } else {
                // Cek gazetteer lokal Padang (sangat cepat, 0 timeout, sebaran akurat)
                $localCoords = PadangGeocoder::lookup($alamat);
                if ($localCoords) {
                    $lat = $localCoords['lat'];
                    $lng = $localCoords['lng'];
                    $perkiraan = true;
                    $newGeocodesToInsert[$addrKey] = [
                        'alamat_hash' => hash('sha256', $addrKey),
                        'alamat_asli' => $alamat,
                        'latitude'    => $lat,
                        'longitude'   => $lng,
                        'sumber'      => $localCoords['sumber'] ?? 'wilayah_lokal',
                    ];
                } elseif ($totalRows <= 10) {
                    // Hanya coba query Nominatim jika data sedikit (<= 10 baris) agar tidak kena limit
                    $coords = $this->geocode($alamat);
                    if ($coords) {
                        $lat = $coords['lat'];
                        $lng = $coords['lng'];
                        $perkiraan = $coords['perkiraan'] ?? false;
                        $newGeocodesToInsert[$addrKey] = [
                            'alamat_hash' => hash('sha256', $addrKey),
                            'alamat_asli' => $alamat,
                            'latitude'    => $lat,
                            'longitude'   => $lng,
                            'sumber'      => 'nominatim',
                        ];
                    }
                }

                $geoMemoryCache[$addrKey] = [
                    'lat'       => $lat,
                    'lng'       => $lng,
                    'perkiraan' => $perkiraan,
                ];
            }

            // Insert case queue
            $casesToInsert[] = [
                'batch_id'          => $batchId,
                'facility_id'       => $facilityId,
                'tanggal_kunjungan' => $tanggal,
                'nama'              => $nama,
                'icd10_code'        => $icd10Code,
                'diagnosa_teks'     => $diagnosaTeks,
                'alamat'            => $alamat,
                'latitude'          => $lat,
                'longitude'         => $lng,
                'lokasi_perkiraan'  => $perkiraan ? 1 : 0,
            ];
            $success++;

            // Batch insert per 500 baris
            if (count($casesToInsert) >= 500) {
                $caseModel->insertBatch($casesToInsert);
                $casesToInsert = [];
            }
        }

        // Insert sisa baris kasus
        if (!empty($casesToInsert)) {
            $caseModel->insertBatch($casesToInsert);
            $casesToInsert = [];
        }

        // Simpan cache geocoding baru secara batch
        if (!empty($newGeocodesToInsert)) {
            try {
                $geocodeCache->insertBatch(array_values($newGeocodesToInsert));
            } catch (\Throwable $e) {
                // Abaikan bila ada duplikasi hash antar request paralel
            }
        }

        // Update batch
        $batchModel->update($batchId, [
            'status'         => 'done',
            'baris_berhasil' => $success,
            'baris_gagal'    => $failed,
        ]);

        // Delete temp file
        @unlink($filePath);
        session()->remove(['upload_file', 'upload_original', 'upload_ext', 'upload_header_row']);

        $auditLog->log('unggah_data', 'File: ' . $originalName . ', Berhasil: ' . $success . ', Gagal: ' . $failed . ', Kembar: ' . $duplicates);

        return view('layouts/main', [
            'pageTitle'  => 'Hasil Unggahan',
            'activePage' => 'upload',
            'content'    => view('upload/result', [
                'fileName'   => $originalName,
                'total'      => count($rows),
                'success'    => $success,
                'failed'     => $failed,
                'duplicates' => $duplicates,
                'failedRows' => $failedRows,
                'noGeocode'  => $caseModel->where('batch_id', $batchId)->where('latitude IS NULL')->countAllResults(),
            ]),
        ]);
    }

    /**
     * Hapus batch dan semua kasus terkait.
     */
    public function deleteBatch(int $id): RedirectResponse
    {
        $batchModel = new ImportBatchModel();
        $batch = $batchModel->find($id);
        if (!$batch) {
            return redirect()->to(base_url('/upload'))->with('error', 'Unggahan tidak ditemukan.');
        }

        // Delete cases (cascade via FK)
        $batchModel->delete($id);

        (new AuditLogModel())->log('hapus_unggahan', 'File: ' . $batch['nama_file']);

        return redirect()->to(base_url('/upload'))->with('success', 'Unggahan dan semua data terkait berhasil dihapus.');
    }

    /* ── Helpers ── */

    /**
     * Saran otomatis pemetaan kolom dari kemiripan nama.
     */
    private function suggestMapping(array $headers): array
    {
        $map = [
            'col_tanggal'  => ['tanggal', 'tgl', 'date', 'kunjungan', 'tanggal_kunjungan', 'tgl_kunjungan', 'waktu'],
            'col_nama'     => ['nama', 'name', 'pasien', 'nama_pasien', 'patient'],
            'col_diagnosa' => ['diagnosa', 'diagnosis', 'diag', 'icd', 'icd10', 'kode_icd', 'penyakit', 'diagnosa umum', 'kasus'],
            'col_alamat'   => ['alamat', 'address', 'tempat_tinggal', 'domisili', 'wilayah'],
        ];

        $suggestions = [];
        foreach ($map as $field => $keywords) {
            foreach ($headers as $h) {
                $lower = mb_strtolower(trim($h));
                foreach ($keywords as $kw) {
                    if (str_contains($lower, $kw)) {
                        $suggestions[$field] = $h;
                        break 2;
                    }
                }
            }
        }

        return $suggestions;
    }

    /**
     * Geocode alamat menggunakan Nominatim (OpenStreetMap).
     * Rate limit: 1 request/detik, timeout 2 detik.
     */
    private function geocode(string $address): ?array
    {
        $url = 'https://nominatim.openstreetmap.org/search?'
            . http_build_query([
                'q'            => $address,
                'format'       => 'json',
                'limit'        => 1,
                'countrycodes' => 'id',
            ]);

        $ctx = stream_context_create([
            'http' => [
                'header'  => "User-Agent: e-Matrika/1.0 (PublicHealthSurveillance)\r\n",
                'timeout' => 1.5,
            ]
        ]);

        $response = @file_get_contents($url, false, $ctx);
        if ($response === false) return null;

        $data = json_decode($response, true);
        if (empty($data)) return null;

        return [
            'lat'       => (float)$data[0]['lat'],
            'lng'       => (float)$data[0]['lon'],
            'perkiraan' => ($data[0]['type'] ?? '') !== 'house',
        ];
    }
}
