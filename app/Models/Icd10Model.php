<?php

namespace App\Models;

use CodeIgniter\Model;

class Icd10Model extends Model
{
    protected $table = 'icd10_ref';
    protected $primaryKey = 'code';
    protected $allowedFields = ['code', 'nama_id', 'nama_en', 'bab', 'blok', 'sensitif'];
    protected $useTimestamps = false;
    protected $returnType = 'array';

    /**
     * Cari kode ICD-10 dari teks diagnosa (kode atau nama).
     */
    public function matchDiagnosis(string $text): ?array
    {
        $text = trim($text);
        if (empty($text)) return null;

        // 1. Ekstrak pola kode ICD-10 (misal: "Hipertensi (I10)", "(A09)", "J02.9", "A01.0")
        if (preg_match('/(?:\(|\b)([A-Z][0-9]{2}(?:\.[0-9]+)?)(?:\)|\b)/i', $text, $matches)) {
            $extractedCode = strtoupper($matches[1]);
            
            // Cocokkan langsung kode hasil ekstraksi
            $direct = $this->find($extractedCode);
            if ($direct) return $direct;

            // Jika kode turunan (misal J02.9), coba cari kode induk 3 karakter (J02)
            if (str_contains($extractedCode, '.')) {
                $baseCode = substr($extractedCode, 0, 3);
                $parent = $this->find($baseCode);
                if ($parent) {
                    return [
                        'code'     => $extractedCode,
                        'nama_id'  => $parent['nama_id'],
                        'nama_en'  => $parent['nama_en'] ?? null,
                        'bab'      => $parent['bab'] ?? null,
                        'blok'     => $parent['blok'] ?? null,
                        'sensitif' => $parent['sensitif'] ?? 0,
                    ];
                }
            }

            // Kode ICD-10 valid walaupun belum ada di tabel referensi
            return [
                'code'     => $extractedCode,
                'nama_id'  => $text,
                'nama_en'  => null,
                'bab'      => null,
                'blok'     => null,
                'sensitif' => 0,
            ];
        }

        // 2. Coba cocokkan langsung sebagai kode murni
        $direct = $this->find(strtoupper($text));
        if ($direct) return $direct;

        // 3. Coba dengan menghilangkan tanda selain alfanumerik (J069 → J06.9)
        $normalized = strtoupper(preg_replace('/[^A-Z0-9]/', '', $text));
        if (strlen($normalized) >= 3 && preg_match('/^[A-Z][0-9]{2,3}$/', $normalized)) {
            $code = substr($normalized, 0, 3);
            if (strlen($normalized) > 3) {
                $code .= '.' . substr($normalized, 3);
            }
            $direct = $this->find($code);
            if ($direct) return $direct;
        }

        // 4. Cari berdasarkan nama (LIKE)
        $result = $this->like('nama_id', $text, 'both')
            ->orLike('nama_en', $text, 'both')
            ->first();
        if ($result) return $result;

        return null;
    }

    /**
     * Apakah kode ini sensitif?
     */
    public function isSensitive(string $code): bool
    {
        $ref = $this->find(strtoupper(trim($code)));
        return $ref && $ref['sensitif'] == 1;
    }

    /**
     * Ambil semua kode, dikelompokkan per bab.
     */
    public function getAllGrouped(): array
    {
        $all = $this->orderBy('code')->findAll();
        $grouped = [];
        foreach ($all as $row) {
            $grouped[$row['bab']][] = $row;
        }
        return $grouped;
    }
}
