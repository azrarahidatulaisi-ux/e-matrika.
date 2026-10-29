<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\AuditLogModel;
use CodeIgniter\HTTP\RedirectResponse;

class AuthController extends BaseController
{
    public function login(): string|RedirectResponse
    {
        if (session()->get('loggedIn')) {
            return redirect()->to(base_url('/map'));
        }

        if ($this->request->is('post')) {
            $username = trim((string)($this->request->getPost('username') ?? ''));
            $password = (string)($this->request->getPost('password') ?? '');
            $remember = $this->request->getPost('remember');

            $userModel = new UserModel();
            $user = $userModel->where('username', $username)->where('active', 1)->first();

            if ($user && $userModel->verifyPassword($password, $user['password_hash'])) {
                $sessionData = [
                    'user_id'     => $user['id'],
                    'username'    => $user['username'],
                    'role'        => $user['role'],
                    'facility_id' => $user['facility_id'] ?? null,
                    'loggedIn'    => true,
                ];
                session()->set($sessionData);

                // Kelola cookie remember me
                if ($remember) {
                    setcookie('ematrika_remember_user', $user['username'], time() + (30 * 86400), '/');
                } else {
                    setcookie('ematrika_remember_user', '', time() - 3600, '/');
                    setcookie('epimap_remember_user', '', time() - 3600, '/');
                }

                // Simpan last user untuk mempermudah login berikutnya
                setcookie('ematrika_last_user', $user['username'], time() + (30 * 86400), '/');

                (new AuditLogModel())->log('login', 'Username: ' . $user['username']);

                return redirect()->to(base_url('/map'));
            }

            session()->setFlashdata('error', 'Nama pengguna atau kata sandi salah.');
            return redirect()->back()->withInput();
        }

        // Ambil data akun yang diingat / terakhir keluar
        $lastUser = $_COOKIE['ematrika_last_user'] ?? $_COOKIE['epimap_last_user'] ?? $_COOKIE['ematrika_remember_user'] ?? $_COOKIE['epimap_remember_user'] ?? '';
        $isRemembered = !empty($_COOKIE['ematrika_remember_user']) || !empty($_COOKIE['epimap_remember_user']);

        return view('auth/login', [
            'lastUser'     => $lastUser,
            'isRemembered' => $isRemembered,
        ]);
    }

    public function logout(): RedirectResponse
    {
        $session = session();
        $username = $session->get('username');

        if ($username) {
            (new AuditLogModel())->log('logout', 'Username: ' . $username);
            // Simpan akun terakhir agar saat kembali otomatis disiapkan untuk login kembali
            setcookie('ematrika_last_user', $username, time() + (30 * 86400), '/');
        }

        // Hapus data autentikasi dari sesi tanpa menghapus flashdata
        $session->remove(['user_id', 'username', 'role', 'facility_id', 'loggedIn']);
        $session->setFlashdata('info', 'Anda telah keluar dari aplikasi. Masukkan kata sandi untuk masuk kembali.');

        return redirect()->to(base_url('/login'));
    }
}
