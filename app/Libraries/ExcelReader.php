<?php

namespace App\Libraries;

/**
 * Pembaca file Excel (.xlsx), CSV, dan HTML-table (.xls).
 * Menggunakan ZipArchive + SimpleXML untuk XLSX murni, tanpa dependensi eksternal.
 * Dilengkapi pendeteksi otomatis baris judul kolom (header) cerdas.
 */
class ExcelReader
{
    private array $headers = [];
    private array $rows = [];
    private int $detectedHeaderRow = 1;
    private array $candidateHeaderRows = [];

    /**
     * Baca file berdasarkan ekstensi atau format yang dipaksakan.
     * $headerRow = 0 berarti deteksi otomatis baris header terbaik.
     */
    public function read(string $filePath, int $headerRow = 0, ?string $forceExt = null): self
    {
        if (!file_exists($filePath)) {
            throw new \RuntimeException('Berkas tidak ditemukan: ' . basename($filePath));
        }

        $ext = $forceExt ? strtolower(trim($forceExt, '.')) : strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        if ($ext === 'csv') {
            $this->readCsv($filePath, $headerRow);
        } elseif ($ext === 'xlsx') {
            $this->readXlsx($filePath, $headerRow);
        } elseif ($ext === 'xls') {
            $this->readXls($filePath, $headerRow);
        } else {
            // Coba deteksi isi
            $contentHead = @file_get_contents($filePath, false, null, 0, 512) ?: '';
            if (str_starts_with($contentHead, "PK\x03\x04")) {
                $this->readXlsx($filePath, $headerRow);
            } else {
                $this->readCsv($filePath, $headerRow);
            }
        }

        return $this;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getRows(): array
    {
        return $this->rows;
    }

    public function getDetectedHeaderRow(): int
    {
        return $this->detectedHeaderRow;
    }

    public function getCandidateHeaderRows(): array
    {
        return $this->candidateHeaderRows;
    }

    /**
     * Ambil pratinjau (n baris pertama).
     */
    public function preview(int $count = 20): array
    {
        return array_slice($this->rows, 0, $count);
    }

    /**
     * Deteksi otomatis baris header terbaik dari baris-baris pertama data.
     * Mengeliminasi baris judul banner/laporan (seperti "DATA PASIEN") dan memilih baris
     * dengan kolom terbanyak serta kata kunci header yang cocok.
     */
    public function detectHeaderRow(array $allRows): int
    {
        $keywords = [
            'tanggal', 'tgl', 'date', 'kunjungan', 'nama', 'name', 'pasien', 'patient',
            'diagnosa', 'diagnosis', 'diag', 'icd', 'penyakit', 'alamat', 'address',
            'ruangan', 'poli', 'asuransi', 'bpjs', 'no', 'nomor', 'kelurahan', 'kecamatan'
        ];

        $candidates = [];
        $bestRow = 1;
        $maxScore = -999;

        $checkLimit = min(25, count($allRows));
        $rowsToCheck = array_slice($allRows, 0, $checkLimit, true);

        foreach ($rowsToCheck as $rowNum => $cells) {
            if (!is_array($cells)) continue;

            $nonEmpty = array_values(array_filter($cells, fn($c) => trim((string)$c) !== ''));
            $nonEmptyCount = count($nonEmpty);
            if ($nonEmptyCount === 0) continue;

            // Simpan ringkasan untuk opsi UI jika pengguna ingin mengganti manual
            $summary = implode(' | ', array_slice($nonEmpty, 0, 5));
            $candidates[$rowNum] = $summary;

            // Hitung kata kunci kolom
            $kwMatches = 0;
            foreach ($nonEmpty as $val) {
                $lower = mb_strtolower(trim((string)$val));
                foreach ($keywords as $kw) {
                    if (str_contains($lower, $kw)) {
                        $kwMatches++;
                        break;
                    }
                }
            }

            // Skor: kata kunci bernilai besar (+15), jumlah kolom (+1 s.d. +15)
            // Penalti -30 jika hanya 1 kolom (seperti banner "DATA PASIEN" atau nomor tanggal cetak)
            $score = ($kwMatches * 15) + min($nonEmptyCount, 15);
            if ($nonEmptyCount <= 1) {
                $score -= 30;
            }

            if ($score > $maxScore) {
                $maxScore = $score;
                $bestRow = $rowNum;
            }
        }

        $this->candidateHeaderRows = $candidates;
        $this->detectedHeaderRow = $bestRow;

        return $bestRow;
    }

    /* ── CSV Reader ── */
    private function readCsv(string $filePath, int $headerRow): void
    {
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new \RuntimeException('Tidak dapat membuka berkas CSV.');
        }

        $this->headers = [];
        $this->rows = [];

        // Baca 4KB pertama untuk deteksi delimiter dan strip BOM
        $sample = fread($handle, 4096);
        rewind($handle);

        // Deteksi delimiter
        $delimiters = [',', ';', "\t", '|'];
        $bestDelimiter = ',';
        $maxCount = 0;
        foreach ($delimiters as $d) {
            $count = substr_count($sample, $d);
            if ($count > $maxCount) {
                $maxCount = $count;
                $bestDelimiter = $d;
            }
        }

        $allRawRows = [];
        $currentRow = 0;
        while (($data = fgetcsv($handle, 0, $bestDelimiter)) !== false) {
            if (count($data) === 1 && trim($data[0] ?? '') === '') {
                continue;
            }
            $currentRow++;
            if ($currentRow === 1 && !empty($data[0])) {
                $data[0] = preg_replace('/^\xEF\xBB\xBF/', '', $data[0]);
            }
            $allRawRows[$currentRow] = $data;
        }
        fclose($handle);

        $this->detectHeaderRow($allRawRows);
        $effectiveHeaderRow = $headerRow > 0 ? $headerRow : $this->detectedHeaderRow;
        $this->detectedHeaderRow = $effectiveHeaderRow;

        if (isset($allRawRows[$effectiveHeaderRow])) {
            $this->headers = array_map(function ($h) {
                $cleaned = preg_replace('/^\xEF\xBB\xBF/', '', trim((string)$h));
                return trim($cleaned, " \t\n\r\0\x0B\"'");
            }, $allRawRows[$effectiveHeaderRow]);
        }

        while (!empty($this->headers) && end($this->headers) === '') {
            array_pop($this->headers);
        }

        foreach ($allRawRows as $num => $data) {
            if ($num <= $effectiveHeaderRow) continue;

            $row = [];
            foreach ($this->headers as $i => $h) {
                if ($h === '') continue;
                $row[$h] = isset($data[$i]) ? trim((string)$data[$i]) : '';
            }

            $nonEmpty = array_filter($row, fn($v) => $v !== '');
            if (!empty($nonEmpty)) {
                $this->rows[] = $row;
            }
        }
    }

    /* ── XLSX Reader (ZipArchive + SimpleXML tanpa masalah namespace) ── */
    private function readXlsx(string $filePath, int $headerRow): void
    {
        if (!class_exists('ZipArchive')) {
            throw new \RuntimeException('Ekstensi ZIP PHP diperlukan untuk membaca berkas Excel (.xlsx).');
        }

        $zip = new \ZipArchive();
        $res = $zip->open($filePath);
        if ($res !== true) {
            throw new \RuntimeException('Tidak dapat membuka berkas Excel (Zip error: ' . $res . ').');
        }

        // Cari shared strings (case-insensitive)
        $sharedStrings = [];
        $ssIndex = -1;
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            if (strcasecmp($name, 'xl/sharedstrings.xml') === 0) {
                $ssIndex = $i;
                break;
            }
        }

        if ($ssIndex >= 0) {
            $ssXml = $zip->getFromIndex($ssIndex);
            if ($ssXml) {
                // Hapus deklarasi namespace agar SimpleXML bisa diakses langsung tanpa prefix
                $cleanSsXml = preg_replace('/xmlns[^=]*="[^"]*"/i', '', $ssXml);
                $ss = @simplexml_load_string($cleanSsXml);
                if ($ss && isset($ss->si)) {
                    foreach ($ss->si as $si) {
                        $text = '';
                        if (isset($si->t)) {
                            $text = (string)$si->t;
                        } elseif (isset($si->r)) {
                            foreach ($si->r as $r) {
                                $text .= (string)($r->t ?? '');
                            }
                        }
                        $sharedStrings[] = $text;
                    }
                }
            }
        }

        // Cari worksheet pertama (misal xl/worksheets/sheet1.xml)
        $sheetXml = null;
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            if (preg_match('#xl/worksheets/sheet\d+\.xml#i', $name)) {
                $sheetXml = $zip->getFromIndex($i);
                break;
            }
        }

        if (!$sheetXml) {
            $zip->close();
            throw new \RuntimeException('Tidak menemukan lembar kerja (worksheet) di dalam berkas Excel.');
        }

        $zip->close();

        // Hapus namespace XML dari sheet agar terhindar dari SimpleXML XPath namespace prefix bug
        $cleanSheetXml = preg_replace('/xmlns[^=]*="[^"]*"/i', '', $sheetXml);
        $sheet = @simplexml_load_string($cleanSheetXml);

        if (!$sheet) {
            throw new \RuntimeException('Gagal mengurai lembar kerja XML Excel.');
        }

        $allRows = [];

        if (isset($sheet->sheetData->row)) {
            $rowCounter = 0;
            foreach ($sheet->sheetData->row as $row) {
                $rowCounter++;
                $rowNum = !empty($row['r']) ? (int)$row['r'] : $rowCounter;
                $rowData = [];
                $colCounter = 0;

                if (isset($row->c)) {
                    foreach ($row->c as $cell) {
                        $colRef = (string)($cell['r'] ?? '');
                        if ($colRef !== '') {
                            $colLetter = preg_replace('/[0-9]/', '', $colRef);
                            $colIdx = $this->columnLetterToIndex($colLetter);
                        } else {
                            $colIdx = $colCounter;
                        }
                        $colCounter = $colIdx + 1;

                        $type = (string)($cell['t'] ?? '');
                        $value = '';

                        if ($type === 's') {
                            $idx = isset($cell->v) ? (int)(string)$cell->v : -1;
                            $value = $sharedStrings[$idx] ?? '';
                        } elseif ($type === 'inlineStr') {
                            $value = (string)($cell->is->t ?? '');
                        } else {
                            $value = (string)($cell->v ?? '');
                        }

                        while (count($rowData) <= $colIdx) {
                            $rowData[] = '';
                        }
                        $rowData[$colIdx] = trim($value);
                    }
                }

                $allRows[$rowNum] = $rowData;
            }
        }

        // Tentukan header
        $this->headers = [];
        $this->rows = [];

        $this->detectHeaderRow($allRows);
        $effectiveHeaderRow = $headerRow > 0 ? $headerRow : $this->detectedHeaderRow;
        $this->detectedHeaderRow = $effectiveHeaderRow;

        if (isset($allRows[$effectiveHeaderRow])) {
            $this->headers = array_map(function($h) {
                $cleaned = preg_replace('/^\xEF\xBB\xBF/', '', trim((string)$h));
                return trim($cleaned, " \t\n\r\0\x0B\"'");
            }, $allRows[$effectiveHeaderRow]);
        } else {
            $first = reset($allRows);
            if ($first) {
                $this->headers = array_map('trim', $first);
            }
        }

        // Hapus trailing empty headers
        while (!empty($this->headers) && end($this->headers) === '') {
            array_pop($this->headers);
        }

        foreach ($allRows as $num => $rowData) {
            if ($num <= $effectiveHeaderRow) continue;

            $nonEmpty = array_filter($rowData, fn($v) => $v !== '');
            if (empty($nonEmpty)) continue;

            $row = [];
            foreach ($this->headers as $i => $h) {
                if ($h === '') continue;
                $row[$h] = $rowData[$i] ?? '';
            }
            $this->rows[] = $row;
        }
    }

    /* ── XLS Handler (Support XLSX-in-disguise, HTML table, CSV, or friendly notice) ── */
    private function readXls(string $filePath, int $headerRow): void
    {
        // 1. Coba buka sebagai Zip (bila file .xlsx dinamai .xls)
        if (class_exists('ZipArchive')) {
            $zip = new \ZipArchive();
            if ($zip->open($filePath) === true) {
                $zip->close();
                $this->readXlsx($filePath, $headerRow);
                return;
            }
        }

        // 2. Baca isi teks
        $content = @file_get_contents($filePath);
        if ($content === false) {
            throw new \RuntimeException('Tidak dapat membaca berkas .xls');
        }

        // Bila berisi tag HTML <table> (umum dari ekspor web Simpus/Dinkes)
        if (stripos($content, '<table') !== false) {
            $this->readHtmlTable($content, $headerRow);
            return;
        }

        // Bila berisi teks CSV
        if (substr_count($content, ',') > 5 || substr_count($content, ';') > 5) {
            $this->readCsv($filePath, $headerRow);
            return;
        }

        // Bila binary BIFF format lama (Excel 97-2003 murni)
        throw new \RuntimeException(
            'Berkas menggunakan format biner lama (Excel 97-2003 .xls). ' .
            'Silakan buka berkas tersebut di Excel lalu simpan sebagai format .xlsx atau .csv sebelum diunggah.'
        );
    }

    /**
     * Parse file HTML table (ekspor sistem RS/Puskesmas yang diberi ekstensi .xls).
     */
    private function readHtmlTable(string $html, int $headerRow): void
    {
        $dom = new \DOMDocument();
        @$dom->loadHTML('<?xml encoding="UTF-8">' . $html);

        $tables = $dom->getElementsByTagName('table');
        if ($tables->length === 0) {
            throw new \RuntimeException('Tabel data tidak ditemukan di dalam berkas.');
        }

        $table = $tables->item(0);
        $rows = $table->getElementsByTagName('tr');

        $this->headers = [];
        $this->rows = [];
        $allRawRows = [];
        $currentRow = 0;

        foreach ($rows as $tr) {
            $currentRow++;
            $cells = [];
            foreach ($tr->childNodes as $node) {
                if ($node->nodeName === 'th' || $node->nodeName === 'td') {
                    $cells[] = trim($node->textContent);
                }
            }

            if (empty($cells)) continue;
            $allRawRows[$currentRow] = $cells;
        }

        $this->detectHeaderRow($allRawRows);
        $effectiveHeaderRow = $headerRow > 0 ? $headerRow : $this->detectedHeaderRow;
        $this->detectedHeaderRow = $effectiveHeaderRow;

        if (isset($allRawRows[$effectiveHeaderRow])) {
            $this->headers = $allRawRows[$effectiveHeaderRow];
        }

        while (!empty($this->headers) && end($this->headers) === '') {
            array_pop($this->headers);
        }

        foreach ($allRawRows as $num => $cells) {
            if ($num <= $effectiveHeaderRow) continue;

            $row = [];
            foreach ($this->headers as $i => $h) {
                if ($h === '') continue;
                $row[$h] = $cells[$i] ?? '';
            }
            if (!empty(array_filter($row, fn($v) => $v !== ''))) {
                $this->rows[] = $row;
            }
        }
    }

    private function columnLetterToIndex(string $letter): int
    {
        $letter = strtoupper($letter);
        $index = 0;
        $len = strlen($letter);
        for ($i = 0; $i < $len; $i++) {
            $index = $index * 26 + (ord($letter[$i]) - ord('A') + 1);
        }
        return $index - 1;
    }

    /**
     * Konversi tanggal ke format Y-m-d.
     * Mendukung format Excel serial, format tanggal Indonesia & standar.
     */
    public static function excelDateToPhp($value): ?string
    {
        if ($value === null || $value === '') return null;

        $str = trim((string)$value, " \t\n\r\0\x0B\"'");
        if ($str === '') return null;

        // Jika angka murni / desimal: kemungkinan serial date Excel (misal: 45550 atau 45550.5)
        if (is_numeric($str)) {
            $num = (float)$str;
            if ($num > 30000 && $num < 80000) {
                $serial = (int)floor($num);
                // Excel epoch offset: gunakan gmdate agar tidak terpengaruh offset zona waktu lokal
                $unixTimestamp = ($serial - 25569) * 86400;
                if ($unixTimestamp > 0) {
                    return gmdate('Y-m-d', $unixTimestamp);
                }
            }
        }

        // Format tanggal standar & dengan jam
        $formats = [
            'Y-m-d',
            'd/m/Y',
            'd-m-Y',
            'd.m.Y',
            'Y/m/d',
            'Y.m.d',
            'd/m/Y H:i:s',
            'd/m/Y H:i',
            'd-m-Y H:i:s',
            'd-m-Y H:i',
            'd.m.Y H:i:s',
            'd.m.Y H:i',
            'Y-m-d H:i:s',
            'Y-m-d H:i',
            'Y-m-d\TH:i:s',
            'Y-m-d\TH:i:s.v\Z',
            'd/m/y',
            'd-m-y',
            'd.m.y',
        ];

        foreach ($formats as $fmt) {
            $dt = \DateTime::createFromFormat($fmt, $str);
            if ($dt !== false) {
                return $dt->format('Y-m-d');
            }
        }

        // Coba konversi nama bulan Indonesia ke Inggris
        $bulanMap = [
            'januari' => 'january', 'februari' => 'february', 'maret' => 'march',
            'april' => 'april', 'mei' => 'may', 'juni' => 'june',
            'juli' => 'july', 'agustus' => 'august', 'september' => 'september',
            'oktober' => 'october', 'november' => 'november', 'desember' => 'december',
            'agu' => 'aug', 'des' => 'dec', 'pebruari' => 'february'
        ];
        $lower = strtolower($str);
        $translated = strtr($lower, $bulanMap);
        $ts = strtotime($translated);
        if ($ts !== false && $ts > 0) {
            return date('Y-m-d', $ts);
        }

        return null;
    }
}
