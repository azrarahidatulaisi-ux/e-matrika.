<!-- Users Page Content -->
<div class="page-header">
    <h1>Pengguna</h1>
</div>
<div class="page-body">
    <?php if (!empty($errors = session()->getFlashdata('errors'))): ?>
        <div class="alert alert-error">
            <ul style="margin:0;padding-left:1.25rem;">
                <?php foreach ($errors as $err): ?>
                    <li><?= esc($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Formulir Tambah Pengguna -->
    <div class="card" style="margin-bottom:1.25rem;">
        <div class="card-header">Tambah Pengguna</div>
        <form method="post" action="<?= base_url('/users/create') ?>" id="form-add-user">
            <?= csrf_field() ?>
            <div class="mapping-grid">
                <div class="form-group">
                    <label class="form-label" for="username">Nama pengguna</label>
                    <input type="text" class="form-control" id="username" name="username" required minlength="3"
                           value="<?= old('username') ?>">
                </div>
                <div class="form-group">
                    <label class="form-label" for="password">Kata sandi</label>
                    <input type="password" class="form-control" id="password" name="password" required minlength="6">
                </div>
                <div class="form-group">
                    <label class="form-label" for="role">Peran</label>
                    <select class="form-control" id="role" name="role" required>
                        <option value="petugas" <?= old('role') === 'petugas' ? 'selected' : '' ?>>Petugas</option>
                        <option value="admin" <?= old('role') === 'admin' ? 'selected' : '' ?>>Admin</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="facility_id">Fasilitas</label>
                    <select class="form-control" id="facility_id" name="facility_id">
                        <option value="">– Tidak ditentukan –</option>
                        <?php foreach (($facilities ?? []) as $fid => $fname): ?>
                            <option value="<?= $fid ?>" <?= old('facility_id') == $fid ? 'selected' : '' ?>><?= esc($fname) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary" id="btn-add-user">Tambah</button>
        </form>
    </div>

    <!-- Daftar Pengguna -->
    <div class="card">
        <div class="card-header">Daftar Pengguna</div>
        <div class="table-wrapper">
            <table class="table" id="table-users">
                <thead>
                    <tr>
                        <th>Nama pengguna</th>
                        <th>Peran</th>
                        <th>Dibuat</th>
                        <th style="width:100px;"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (($users ?? []) as $u): ?>
                    <tr>
                        <td><?= esc($u['username']) ?></td>
                        <td><span class="badge badge-<?= $u['role'] ?>"><?= esc(ucfirst($u['role'])) ?></span></td>
                        <td><?= esc($u['created_at'] ?? '–') ?></td>
                        <td>
                            <?php if ((int)$u['id'] !== 1 && (int)$u['id'] !== (int)session()->get('user_id')): ?>
                            <form method="post" action="<?= base_url('/users/delete/' . $u['id']) ?>"
                                  onsubmit="return confirm('Yakin ingin menghapus pengguna ini?');" style="display:inline;">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($users)): ?>
                    <tr><td colspan="4" style="text-align:center;color:#9ca3ae;">Belum ada pengguna.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
