<?php

namespace App\Models;

use CodeIgniter\Model;

class ImportBatchModel extends Model
{
    protected $table = 'import_batches';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'facility_id', 'user_id', 'nama_file', 'status',
        'total_baris', 'baris_berhasil', 'baris_gagal', 'created_at'
    ];
    protected $useTimestamps = false;
    protected $returnType = 'array';

    /**
     * Ambil daftar batch dengan info pengguna.
     */
    public function getWithUser(?int $facilityId = null): array
    {
        $builder = $this->builder();
        $builder->select('import_batches.*, users.username');
        $builder->join('users', 'users.id = import_batches.user_id', 'left');

        if ($facilityId) {
            $builder->where('import_batches.facility_id', $facilityId);
        }

        $builder->orderBy('import_batches.created_at', 'DESC');
        return $builder->get()->getResultArray();
    }
}
