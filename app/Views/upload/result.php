<!-- Upload Result Page Content -->
<div class="page-header">
    <h1>Hasil Unggahan</h1>
</div>
<div class="page-body">
    <div class="card" style="margin-bottom:1.25rem;">
        <div class="card-header">Laporan Hasil</div>

        <div class="alert <?= ($success ?? 0) > 0 ? 'alert-success' : 'alert-warning' ?>">
            Berkas <strong><?= esc($fileName ?? '') ?></strong> selesai diproses.
        </div>

        <div style="max-width:400px;">
            <div class="result-stat">
                <span class="label">Total baris</span>
                <span class="value"><?= esc($total ?? 0) ?></span>
            </div>
            <div class="result-stat">
                <span class="label">Berhasil</span>
                <span class="value" style="color:#16a34a;"><?= esc($success ?? 0) ?></span>
            </div>
            <div class="result-stat">
                <span class="label">Gagal</span>
                <span class="value" style="color:#dc2626;"><?= esc($failed ?? 0) ?></span>
            </div>
            <div class="result-stat">
                <span class="label">Kembar (tidak dihitung dua kali)</span>
                <span class="value"><?= esc($duplicates ?? 0) ?></span>
            </div>
            <div class="result-stat">
                <span class="label">Alamat tanpa titik lokasi</span>
                <span class="value"><?= esc($noGeocode ?? 0) ?></span>
            </div>
        </div>
    </div>

    <?php if (!empty($failedRows)): ?>
    <div class="card" style="margin-bottom:1.25rem;">
        <div class="card-header">Baris yang Gagal</div>
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Baris</th>
                        <th>Alasan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($failedRows as $fr): ?>
                    <tr>
                        <td><?= esc($fr['baris']) ?></td>
                        <td><?= esc($fr['alasan']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <div style="display:flex;gap:.75rem;">
        <a href="<?= base_url('/map') ?>" class="btn btn-primary">Lihat Peta</a>
        <a href="<?= base_url('/upload') ?>" class="btn btn-secondary">Unggah Lagi</a>
    </div>
</div>
