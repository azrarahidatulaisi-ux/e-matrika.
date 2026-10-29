<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Filter: memastikan pengguna yang login adalah Admin.
 * Pemeriksaan di sisi server (bukan hanya menyembunyikan menu).
 */
class AdminFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('loggedIn')) {
            return redirect()->to(base_url('/login'));
        }
        if (session()->get('role') !== 'admin') {
            return redirect()->to(base_url('/map'))->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak ada aksi setelah respons.
    }
}
