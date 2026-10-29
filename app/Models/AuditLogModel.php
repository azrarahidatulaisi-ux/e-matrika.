<?php

namespace App\Models;

use CodeIgniter\Model;

class AuditLogModel extends Model
{
    protected $table = 'audit_logs';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'aksi', 'objek', 'ip', 'waktu'];
    protected $useTimestamps = false;
    protected $returnType = 'array';

    /**
     * Catat aktivitas pengguna.
     */
    public function log(string $aksi, ?string $objek = null): void
    {
        $this->insert([
            'user_id' => session()->get('user_id'),
            'aksi'    => $aksi,
            'objek'   => $objek,
            'ip'      => service('request')->getIPAddress(),
            'waktu'   => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Ambil catatan aktivitas terbaru.
     */
    public function getRecent(int $limit = 100): array
    {
        return $this->builder()
            ->select('audit_logs.*, users.username')
            ->join('users', 'users.id = audit_logs.user_id', 'left')
            ->orderBy('audit_logs.waktu', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }
}
