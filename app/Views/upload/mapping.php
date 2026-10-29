<!-- Column Mapping Page Content -->
<div class="page-header">
    <h1>Pemetaan Kolom</h1>
</div>
<div class="page-body">
    <div class="alert alert-info" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:0.75rem;margin-bottom:1.25rem;">
        <div>
            Berkas: <strong><?= esc($fileName ?? '') ?></strong> &bull;
            Judul kolom diambil dari: <strong>Baris <?= esc($headerRow ?? 1) ?></strong>
        </div>
        <?php if (!empty($candidateHeaderRows) && count($candidateHeaderRows) > 1): ?>
        <div style="display:flex;align-items:center;gap:0.5rem;">
            <label for="select-header-row" style="font-size:0.85rem;margin:0;font-weight:600;">Ganti baris header:</label>
            <select id="select-header-row" class="form-control" style="width:auto;padding:0.25rem 0.6rem;font-size:0.85rem;display:inline-block;" onchange="window.location.href='<?= base_url('/upload/mapping?header_row=') ?>' + this.value;">
                <?php foreach ($candidateHeaderRows as $rNum => $rSummary): ?>
                <option value="<?= $rNum ?>" <?= (($headerRow ?? 1) == $rNum) ? 'selected' : '' ?>>
                    Baris <?= $rNum ?>: <?= esc(mb_strimwidth($rSummary, 0, 55, '...')) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>
    </div>

    <!-- Pratinjau 20 baris pertama -->
    <div class="card" style="margin-bottom:1.25rem;">
        <div class="card-header">Pratinjau Data (20 baris pertama)</div>
        <div class="preview-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <?php foreach (($headers ?? []) as $h): ?>
                        <th><?= esc($h) ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($preview)): ?>
                    <tr>
                        <td colspan="<?= max(1, count($headers ?? [])) ?>" style="text-align:center;color:#6b7280;padding:1.5rem;">
                            Tidak ada data untuk ditampilkan.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($preview as $row): ?>
                    <tr>
                        <?php foreach (($headers ?? []) as $h): ?>
                        <td><?= esc($row[$h] ?? '') ?></td>
                        <?php endforeach; ?>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Formulir Pemetaan Kolom -->
    <div class="card">
        <div class="card-header">Petakan Kolom</div>
        <p style="font-size:.85rem;color:#6b7280;margin-bottom:1rem;">
            Pilih kolom Excel yang sesuai untuk setiap data yang dibutuhkan. Pilihan telah disesuaikan secara otomatis.
        </p>
        <form method="post" action="<?= base_url('/upload/process') ?>" id="form-mapping">
            <?= csrf_field() ?>
            <input type="hidden" name="header_row" value="<?= esc($headerRow ?? 1) ?>">
            
            <div class="mapping-grid">
                <div class="form-group">
                    <label class="form-label" for="col_tanggal">Tanggal kunjungan <span style="color:#dc2626;">*</span></label>
                    <select class="form-control" id="col_tanggal" name="col_tanggal" required>
                        <option value="">– Pilih kolom –</option>
                        <?php foreach (($headers ?? []) as $h): ?>
                        <option value="<?= esc($h) ?>" <?= (($suggestions['col_tanggal'] ?? '') === $h) ? 'selected' : '' ?>><?= esc($h) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="col_nama">Nama <span style="color:#dc2626;">*</span></label>
                    <select class="form-control" id="col_nama" name="col_nama" required>
                        <option value="">– Pilih kolom –</option>
                        <?php foreach (($headers ?? []) as $h): ?>
                        <option value="<?= esc($h) ?>" <?= (($suggestions['col_nama'] ?? '') === $h) ? 'selected' : '' ?>><?= esc($h) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="col_diagnosa">Diagnosa <span style="color:#dc2626;">*</span></label>
                    <select class="form-control" id="col_diagnosa" name="col_diagnosa" required>
                        <option value="">– Pilih kolom –</option>
                        <?php foreach (($headers ?? []) as $h): ?>
                        <option value="<?= esc($h) ?>" <?= (($suggestions['col_diagnosa'] ?? '') === $h) ? 'selected' : '' ?>><?= esc($h) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="col_alamat">Alamat <span style="color:#dc2626;">*</span></label>
                    <select class="form-control" id="col_alamat" name="col_alamat" required>
                        <option value="">– Pilih kolom –</option>
                        <?php foreach (($headers ?? []) as $h): ?>
                        <option value="<?= esc($h) ?>" <?= (($suggestions['col_alamat'] ?? '') === $h) ? 'selected' : '' ?>><?= esc($h) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div style="margin-top:1.25rem;display:flex;gap:.75rem;">
                <button type="submit" class="btn btn-primary" id="btn-process" onclick="if(document.getElementById('form-mapping').checkValidity()){ this.disabled=true; this.innerText='Memproses data...'; this.form.submit(); }">Proses Data</button>
                <a href="<?= base_url('/upload') ?>" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
