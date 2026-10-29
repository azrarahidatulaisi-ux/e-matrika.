<?php

namespace App\Models;

use CodeIgniter\Model;

class CaseModel extends Model
{
    protected $table = 'cases_data';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'batch_id', 'facility_id', 'tanggal_kunjungan', 'nama',
        'icd10_code', 'diagnosa_teks', 'alamat',
        'latitude', 'longitude', 'lokasi_perkiraan', 'created_at'
    ];
    protected $useTimestamps = false;
    protected $returnType = 'array';

    /**
     * Ambil kasus dengan filter, gabung info ICD-10 (termasuk flag sensitif).
     */
    public function getFiltered(array $filters = []): array
    {
        $builder = $this->builder();
        $builder->select('cases_data.*, icd10_ref.nama_id AS diagnosa_nama, icd10_ref.sensitif');
        $builder->join('icd10_ref', 'icd10_ref.code = cases_data.icd10_code', 'left');

        if (!empty($filters['date_from'])) {
            $builder->where('cases_data.tanggal_kunjungan >=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $builder->where('cases_data.tanggal_kunjungan <=', $filters['date_to']);
        }
        if (!empty($filters['diagnosa'])) {
            $builder->where('cases_data.icd10_code', $filters['diagnosa']);
        }
        if (!empty($filters['facility_id'])) {
            $builder->where('cases_data.facility_id', $filters['facility_id']);
        }

        $builder->where('cases_data.latitude IS NOT NULL');
        $builder->where('cases_data.longitude IS NOT NULL');
        $builder->orderBy('cases_data.tanggal_kunjungan', 'DESC');

        return $builder->get()->getResultArray();
    }

    /**
     * Hitung statistik ringkasan.
     */
    public function getSummary(array $filters = []): array
    {
        $cases = $this->getFiltered($filters);
        $uniqueDiag = [];
        foreach ($cases as $c) {
            if (!empty($c['icd10_code'])) {
                $uniqueDiag[$c['icd10_code']] = true;
            }
        }
        return [
            'total' => count($cases),
            'unique_diagnosa' => count($uniqueDiag),
        ];
    }

    /**
     * Ambil daftar diagnosa unik yang ada di data.
     */
    public function getUniqueDiagnoses(): array
    {
        return $this->builder()
            ->select('cases_data.icd10_code AS code, icd10_ref.nama_id AS nama')
            ->distinct()
            ->join('icd10_ref', 'icd10_ref.code = cases_data.icd10_code', 'left')
            ->where('cases_data.icd10_code IS NOT NULL')
            ->orderBy('cases_data.icd10_code')
            ->get()
            ->getResultArray();
    }
}
