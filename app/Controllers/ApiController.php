<?php

namespace App\Controllers;

use App\Models\CaseModel;
use App\Models\Icd10Model;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * API Controller – mengembalikan data JSON untuk peta dan filter.
 */
class ApiController extends BaseController
{
    /**
     * GET /api/cases
     * Kembalikan daftar kasus sesuai filter untuk ditampilkan di peta.
     */
    public function cases(): ResponseInterface
    {
        $filters = [
            'date_from' => $this->request->getGet('date_from'),
            'date_to'   => $this->request->getGet('date_to'),
            'diagnosa'  => $this->request->getGet('diagnosa'),
        ];

        // Jika petugas, batasi ke fasilitasnya
        if (session()->get('role') === 'petugas' && session()->get('facility_id')) {
            $filters['facility_id'] = session()->get('facility_id');
        }

        $caseModel = new CaseModel();
        $cases = $caseModel->getFiltered($filters);

        // Untuk diagnosa sensitif: sembunyikan nama dan alamat
        foreach ($cases as &$c) {
            if (!empty($c['sensitif']) && $c['sensitif'] == 1) {
                $c['nama'] = 'Data dilindungi';
                $c['alamat'] = 'Data dilindungi';
            }
            // Gabungkan diagnosa_teks dari ICD-10 jika ada
            if (!empty($c['diagnosa_nama'])) {
                $c['diagnosa_teks'] = $c['diagnosa_nama'];
            }
        }
        unset($c);

        $summary = $caseModel->getSummary($filters);

        return $this->response->setJSON([
            'cases'           => $cases,
            'total'           => $summary['total'],
            'unique_diagnosa' => $summary['unique_diagnosa'],
        ]);
    }

    /**
     * GET /api/diagnoses
     * Kembalikan daftar diagnosa unik untuk dropdown filter.
     */
    public function diagnoses(): ResponseInterface
    {
        $caseModel = new CaseModel();
        $diagnoses = $caseModel->getUniqueDiagnoses();

        return $this->response->setJSON([
            'diagnoses' => $diagnoses,
        ]);
    }
}
