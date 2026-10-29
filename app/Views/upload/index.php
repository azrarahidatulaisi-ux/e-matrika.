<!-- Upload Page Content -->
<div class="page-header" style="display:flex;justify-content:space-between;align-items:center;">
    <div>
        <h1>Unggah Data Kasus</h1>
        <p style="color:#6b7280;font-size:.875rem;margin-top:.25rem;">
            Impor berkas Excel atau CSV untuk memetakan sebaran kasus epidemiologi ke dalam peta.
        </p>
    </div>
    <div>
        <a href="<?= base_url('/upload/template') ?>" class="btn btn-secondary btn-sm" id="btn-download-template" title="Unduh contoh format CSV yang siap digunakan">
            📥 Unduh Template Contoh (.csv)
        </a>
    </div>
</div>

<div class="page-body">
    <!-- Formulir Unggah -->
    <div class="card" style="margin-bottom:1.5rem;">
        <div class="card-header">Pilih atau Seret Berkas</div>

        <form method="post" action="<?= base_url('/upload/do') ?>" enctype="multipart/form-data" id="form-upload">
            <?= csrf_field() ?>

            <!-- Area Dropzone -->
            <div class="upload-area" id="upload-area">
                <div class="upload-area-icon" style="font-size:2.5rem;margin-bottom:.75rem;">📂</div>
                <div class="upload-area-text" style="font-size:1rem;font-weight:500;color:#1e293b;margin-bottom:.5rem;">
                    Tarik dan lepaskan berkas ke sini
                </div>
                <div style="font-size:.875rem;color:#64748b;margin-bottom:1rem;">
                    atau klik tombol di bawah untuk memilih dari perangkat Anda
                </div>

                <input type="file" id="datafile" name="datafile" accept=".csv,.xlsx,.xls,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel,text/csv" style="display:none;">

                <button type="button" class="btn btn-secondary" id="btn-browse" style="padding:.5rem 1.25rem;">
                    📁 Pilih Berkas
                </button>

                <div style="margin-top:.85rem;font-size:.78rem;color:#94a3b8;">
                    Format yang didukung: <strong>.xlsx</strong>, <strong>.xls</strong>, <strong>.csv</strong> (Maks. 25 MB)
                </div>
            </div>

            <!-- Kartu Info Berkas Terpilih -->
            <div id="file-info-card" style="display:none;margin-top:1.25rem;padding:1rem;border:1px solid #e2e8f0;border-radius:6px;background:#f8fafc;">
                <div style="display:flex;align-items:center;justify-content:space-between;gap:1rem;">
                    <div style="display:flex;align-items:center;gap:.75rem;">
                        <span style="font-size:1.75rem;">📄</span>
                        <div>
                            <div id="file-name" style="font-weight:600;color:#0f172a;font-size:.9rem;word-break:break-all;"></div>
                            <div id="file-size" style="font-size:.78rem;color:#64748b;"></div>
                        </div>
                    </div>
                    <div style="display:flex;gap:.5rem;align-items:center;">
                        <button type="button" class="btn btn-secondary btn-sm" id="btn-change-file">Ganti</button>
                        <button type="submit" class="btn btn-primary" id="btn-submit-upload" style="padding:.5rem 1.25rem;">
                            Lanjutkan ke Pemetaan Kolom →
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <div style="margin-top:1.25rem;padding-top:1rem;border-top:1px solid #f1f5f9;">
            <div style="font-size:.85rem;font-weight:600;color:#334155;margin-bottom:.35rem;">
                Persyaratan Kolom Berkas:
            </div>
            <p style="font-size:.8rem;color:#64748b;line-height:1.5;margin:0;">
                Berkas harus memuat 4 data wajib: <strong>Tanggal Kunjungan</strong>, <strong>Nama Pasien</strong>, <strong>Diagnosa (Nama atau Kode ICD-10)</strong>, dan <strong>Alamat</strong>.<br>
                Kolom lain seperti NIK, No. Rekam Medis, No. Telepon akan diabaikan demi privasi. Anda akan dapat mencocokkan nama kolom di langkah berikutnya.
            </p>
        </div>
    </div>

    <!-- Riwayat Unggahan -->
    <div class="card">
        <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;">
            <span>Riwayat Unggahan</span>
        </div>
        <div class="table-wrapper">
            <table class="table" id="table-batches">
                <thead>
                    <tr>
                        <th>Berkas</th>
                        <th>Petugas</th>
                        <th>Waktu</th>
                        <th>Status</th>
                        <th>Berhasil</th>
                        <th>Gagal</th>
                        <th>Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (($batches ?? []) as $b): ?>
                    <tr>
                        <td style="font-weight:500;"><?= esc($b['nama_file']) ?></td>
                        <td><?= esc($b['username'] ?? '–') ?></td>
                        <td style="color:#64748b;font-size:.825rem;"><?= esc($b['created_at']) ?></td>
                        <td>
                            <?php if ($b['status'] === 'done'): ?>
                                <span style="display:inline-block;padding:.2rem .5rem;border-radius:4px;background:#dcfce7;color:#15803d;font-size:.78rem;font-weight:600;">
                                    ✓ Selesai
                                </span>
                            <?php elseif ($b['status'] === 'failed'): ?>
                                <span style="display:inline-block;padding:.2rem .5rem;border-radius:4px;background:#fee2e2;color:#b91c1c;font-size:.78rem;font-weight:600;">
                                    ✗ Gagal
                                </span>
                            <?php else: ?>
                                <span style="display:inline-block;padding:.2rem .5rem;border-radius:4px;background:#fef9c3;color:#a16207;font-size:.78rem;font-weight:600;">
                                    ⏳ Diproses
                                </span>
                            <?php endif; ?>
                        </td>
                        <td style="color:#16a34a;font-weight:600;"><?= (int)$b['baris_berhasil'] ?></td>
                        <td style="color:<?= (int)$b['baris_gagal'] > 0 ? '#dc2626' : '#94a3b8' ?>;font-weight:600;"><?= (int)$b['baris_gagal'] ?></td>
                        <td>
                            <form method="post" action="<?= base_url('/upload/delete/' . $b['id']) ?>"
                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus unggahan ini dan seluruh data kasus di dalamnya?');" style="display:inline;">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-danger btn-sm" style="padding:.2rem .5rem;font-size:.75rem;">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($batches)): ?>
                    <tr><td colspan="7" style="text-align:center;padding:2rem;color:#94a3b8;">Belum ada riwayat unggahan.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
(function() {
    const uploadArea = document.getElementById('upload-area');
    const fileInput = document.getElementById('datafile');
    const btnBrowse = document.getElementById('btn-browse');
    const btnChangeFile = document.getElementById('btn-change-file');
    const fileInfoCard = document.getElementById('file-info-card');
    const fileNameEl = document.getElementById('file-name');
    const fileSizeEl = document.getElementById('file-size');
    const formUpload = document.getElementById('form-upload');
    const btnSubmit = document.getElementById('btn-submit-upload');

    function formatBytes(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function handleFileSelected(file) {
        if (!file) return;
        fileNameEl.textContent = file.name;
        fileSizeEl.textContent = formatBytes(file.size);
        fileInfoCard.style.display = 'block';
        uploadArea.style.display = 'none';
    }

    // Klik tombol pilih berkas
    btnBrowse.addEventListener('click', function(e) {
        e.stopPropagation();
        fileInput.click();
    });

    uploadArea.addEventListener('click', function(e) {
        if (e.target !== btnBrowse) {
            fileInput.click();
        }
    });

    btnChangeFile.addEventListener('click', function() {
        fileInput.value = '';
        fileInfoCard.style.display = 'none';
        uploadArea.style.display = 'block';
    });

    fileInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            handleFileSelected(this.files[0]);
        }
    });

    // Drag and drop
    ['dragenter', 'dragover'].forEach(eventName => {
        uploadArea.addEventListener(eventName, function(e) {
            e.preventDefault();
            e.stopPropagation();
            uploadArea.classList.add('dragover');
            uploadArea.style.borderColor = '#0079c1';
            uploadArea.style.background = '#f0f9ff';
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        uploadArea.addEventListener(eventName, function(e) {
            e.preventDefault();
            e.stopPropagation();
            uploadArea.classList.remove('dragover');
            uploadArea.style.borderColor = '';
            uploadArea.style.background = '';
        }, false);
    });

    uploadArea.addEventListener('drop', function(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        if (files && files.length > 0) {
            fileInput.files = files;
            handleFileSelected(files[0]);
        }
    }, false);

    // Mencegah double submit & beri indikator
    formUpload.addEventListener('submit', function() {
        btnSubmit.disabled = true;
        btnSubmit.textContent = 'Membaca berkas...';
    });
})();
</script>
