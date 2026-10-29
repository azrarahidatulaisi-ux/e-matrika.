<?php

namespace App\Models;

use CodeIgniter\Model;

class GeocodeCacheModel extends Model
{
    protected $table = 'geocode_cache';
    protected $primaryKey = 'id';
    protected $allowedFields = ['alamat_hash', 'alamat_asli', 'latitude', 'longitude', 'sumber', 'updated_at'];
    protected $useTimestamps = false;
    protected $returnType = 'array';

    /**
     * Cari dari cache berdasarkan alamat.
     */
    public function findByAddress(string $alamat): ?array
    {
        $hash = hash('sha256', mb_strtolower(trim($alamat)));
        return $this->where('alamat_hash', $hash)->first();
    }

    /**
     * Simpan hasil geocoding ke cache.
     */
    public function cache(string $alamat, float $lat, float $lng, string $sumber = 'nominatim'): void
    {
        $hash = hash('sha256', mb_strtolower(trim($alamat)));
        $existing = $this->where('alamat_hash', $hash)->first();

        if ($existing) {
            $this->update($existing['id'], [
                'latitude'  => $lat,
                'longitude' => $lng,
                'sumber'    => $sumber,
            ]);
        } else {
            $this->insert([
                'alamat_hash' => $hash,
                'alamat_asli' => $alamat,
                'latitude'    => $lat,
                'longitude'   => $lng,
                'sumber'      => $sumber,
            ]);
        }
    }
}
