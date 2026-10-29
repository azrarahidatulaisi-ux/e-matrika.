<?php

namespace App\Models;

use CodeIgniter\Model;

class FacilityModel extends Model
{
    protected $table = 'facilities';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nama', 'kode', 'jenis', 'created_at'];
    protected $useTimestamps = false;
    protected $returnType = 'array';

    /**
     * Ambil daftar fasilitas untuk dropdown.
     */
    public function getDropdown(): array
    {
        $list = $this->orderBy('nama')->findAll();
        $options = [];
        foreach ($list as $f) {
            $options[$f['id']] = $f['nama'];
        }
        return $options;
    }
}
