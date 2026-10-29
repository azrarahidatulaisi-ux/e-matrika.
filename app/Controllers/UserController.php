<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\FacilityModel;
use App\Models\AuditLogModel;
use CodeIgniter\HTTP\RedirectResponse;

class UserController extends BaseController
{
    public function index(): string
    {
        $userModel = new UserModel();
        $facilityModel = new FacilityModel();

        $data = [
            'pageTitle'   => 'Pengguna',
            'activePage'  => 'users',
            'users'       => $userModel->findAll(),
            'facilities'  => $facilityModel->getDropdown(),
        ];

        return view('layouts/main', [
            'pageTitle'  => $data['pageTitle'],
            'activePage' => $data['activePage'],
            'content'    => view('users/index', $data),
        ]);
    }

    public function create(): RedirectResponse
    {
        $rules = [
            'username' => 'required|min_length[3]|alpha_numeric|is_unique[users.username]',
            'password' => 'required|min_length[6]',
            'role'     => 'required|in_list[admin,petugas]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userModel = new UserModel();
        $password = $this->request->getPost('password');
        $hash = password_hash($password, PASSWORD_BCRYPT);

        $userModel->insert([
            'username'      => $this->request->getPost('username'),
            'password_hash' => $hash,
            'role'          => $this->request->getPost('role'),
            'facility_id'   => $this->request->getPost('facility_id') ?: null,
            'active'        => 1,
        ]);

        (new AuditLogModel())->log('tambah_pengguna', 'Username: ' . $this->request->getPost('username'));

        return redirect()->to(base_url('/users'))->with('success', 'Pengguna ditambahkan.');
    }

    public function delete(int $id): RedirectResponse
    {
        $userModel = new UserModel();
        $user = $userModel->find($id);

        if (!$user) {
            return redirect()->to(base_url('/users'))->with('error', 'Pengguna tidak ditemukan.');
        }

        // Akun admin utama (id=1) tidak bisa dihapus
        if ($id === 1) {
            return redirect()->to(base_url('/users'))->with('error', 'Akun admin utama tidak bisa dihapus.');
        }

        // Jangan hapus diri sendiri
        if ($id == session()->get('user_id')) {
            return redirect()->to(base_url('/users'))->with('error', 'Anda tidak bisa menghapus akun sendiri.');
        }

        $userModel->delete($id);
        (new AuditLogModel())->log('hapus_pengguna', 'Username: ' . $user['username']);

        return redirect()->to(base_url('/users'))->with('success', 'Pengguna dihapus.');
    }
}
