<?php
    $activeUser = old('username', $lastUser ?? '');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="e-Matrika - Sistem Informasi Geografis Pemetaan & Surveilans Epidemiologi Penyakit">

    <title>Masuk - e-Matrika (WebGIS Surveilans Epidemiologi)</title>  

    <!-- Favicon / Logo Icon Aplikasi -->
    <link rel="icon" type="image/png" href="<?= base_url('assets/Untitled.png') ?>">
    <link rel="apple-touch-icon" href="<?= base_url('assets/Untitled.png') ?>">

    <!-- ===== CSS ===== -->
    <link rel="stylesheet" href="<?= base_url('assets/css/styles.css') ?>">

    <!-- ===== BOX ICONS ===== -->
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.0.5/css/boxicons.min.css" rel="stylesheet">

    <style>
        /* Penyesuaian pendukung tema e-Matrika */
        .form__subtitle {
            font-size: 0.85rem;
            color: var(--text-color);
            margin-top: -1.5rem;
            margin-bottom: 2rem;
            font-weight: 400;
        }

        .form__alert {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.75rem 0.9rem;
            border-radius: 0.5rem;
            font-size: 0.825rem;
            line-height: 1.4;
            margin-bottom: 1.5rem;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form__alert-error {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #f87171;
        }

        .form__alert-info {
            background-color: #e0f2fe;
            color: #0369a1;
            border: 1px solid #7dd3fc;
        }

        .form__alert i {
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        .form__options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 0.75rem;
            margin-bottom: 2rem;
            font-size: var(--smaller-font-size);
        }

        .form__remember {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            color: var(--text-color);
            cursor: pointer;
            user-select: none;
            font-size: 0.85rem;
        }

        .form__remember input[type="checkbox"] {
            accent-color: var(--first-color);
            width: 15px;
            height: 15px;
            cursor: pointer;
        }

        .form__button {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .form__button:disabled {
            opacity: 0.75;
            cursor: not-allowed;
            box-shadow: none;
        }

        .form__quick-fill {
            background: none;
            border: none;
            padding: 0;
            color: var(--text-color);
            font-size: 0.825rem;
            font-family: inherit;
            cursor: pointer;
            text-decoration: underline;
            transition: color 0.3s;
        }

        .form__quick-fill:hover {
            color: var(--first-color);
        }

        .form__pwd-toggle {
            position: absolute;
            right: 0.5rem;
            top: 0.5rem;
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text-color);
            font-size: 1.15rem;
            padding: 0.25rem;
            display: flex;
            align-items: center;
            transition: color 0.3s;
            z-index: 5;
        }

        .form__pwd-toggle:hover {
            color: var(--first-color);
        }

        /* Tampilan Form Login Terpusat & Rapi */
        .form {
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
            min-height: 100vh;
            padding: 1.5rem;
        }

        .form__content {
            width: 100% !important;
            max-width: 400px !important;
            background: #ffffff;
            padding: 2.25rem 2rem;
            border-radius: 1rem;
            box-shadow: 0 10px 25px -5px rgba(18, 25, 44, 0.08), 0 8px 10px -6px rgba(18, 25, 44, 0.04);
            border: 1px solid #e2e8f0;
            z-index: 10;
        }

        .form__title {
            text-align: center;
            margin-bottom: 0.5rem;
        }

        .form__subtitle {
            text-align: center;
            margin-top: 0;
            margin-bottom: 1.75rem;
        }
    </style>
</head>
<body>
    <div class="l-form">
        <div class="shape1"></div>
        <div class="shape2"></div>

        <div class="form">
            <form method="post" action="<?= base_url('/login') ?>" class="form__content" id="login-form">
                <?= csrf_field() ?>

                <div style="display:flex;justify-content:center;margin-bottom:0.75rem;">
                    <img src="<?= base_url('assets/Untitled.png') ?>" alt="Logo Aplikasi" style="width:58px;height:58px;object-fit:contain;border-radius:12px;background:#f5f5f5;padding:3px;box-shadow:0 3px 8px rgba(0,0,0,0.08);border:1px solid #e2e8f0;">
                </div>

                <h1 class="form__title">e-Matrika</h1>
                <p class="form__subtitle">Sistem Informasi Geografis Surveilans Epidemiologi</p>

                <!-- Pesan Berhasil / Keluar -->
                <?php if (session()->getFlashdata('info')): ?>
                <div class="form__alert form__alert-info">
                    <i class='bx bx-info-circle'></i>
                    <span><?= esc(session()->getFlashdata('info')) ?></span>
                </div>
                <?php endif; ?>

                <!-- Pesan Kesalahan -->
                <?php if (session()->getFlashdata('error')): ?>
                <div class="form__alert form__alert-error">
                    <i class='bx bx-error-circle'></i>
                    <span><?= esc(session()->getFlashdata('error')) ?></span>
                </div>
                <?php endif; ?>

                <!-- Input Nama Pengguna -->
                <div class="form__div form__div-one" id="div-username">
                    <div class="form__icon">
                        <i class='bx bx-user-circle'></i>
                    </div>

                    <div class="form__div-input">
                        <label for="username" class="form__label">Nama Pengguna</label>
                        <input type="text" id="username" name="username" class="form__input" value="<?= esc($activeUser) ?>" required autocomplete="username">
                    </div>
                </div>

                <!-- Input Kata Sandi -->
                <div class="form__div" id="div-password">
                    <div class="form__icon">
                        <i class='bx bx-lock'></i>
                    </div>

                    <div class="form__div-input">
                        <label for="password" class="form__label">Kata Sandi</label>
                        <input type="password" id="password" name="password" class="form__input" required autocomplete="current-password">
                        <button type="button" class="form__pwd-toggle" id="btn-toggle-pwd" onclick="togglePassword()" title="Tampilkan / Sembunyikan sandi">
                            <i class='bx bx-show' id="pwd-icon"></i>
                        </button>
                    </div>
                </div>

                <!-- Opsi Ingat Saya & Akun Demo -->
                <div class="form__options">
                    <label class="form__remember" for="remember">
                        <input type="checkbox" id="remember" name="remember" value="1" <?= (!empty($isRemembered) || !empty($lastUser)) ? 'checked' : '' ?>>
                        <span>Ingat saya</span>
                    </label>

                    <button type="button" class="form__quick-fill" onclick="quickFillAdmin()">
                        Isi Akun Admin
                    </button>
                </div>

                <!-- Tombol Masuk -->
                <button type="submit" class="form__button" id="btn-submit">
                    <i class='bx bx-log-in'></i>
                    <span id="btn-text">Masuk</span>
                </button>

                <!-- Footer Teks -->
                <div class="form__social">
                    <span class="form__social-text" style="font-size:0.8rem;color:var(--text-color);margin-bottom:0.75rem;">
                        Puskesmas &bull; Dinas Kesehatan
                    </span>
                    <div style="font-size:0.75rem;color:var(--text-color);opacity:0.8;">
                        &copy; <?= date('Y') ?> e-Matrika WebGIS
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- ===== MAIN JS ===== -->
    <script src="<?= base_url('assets/js/main.js') ?>"></script>
    <script>
        // Memastikan label naik saat input sudah terisi otomatis / dari cookie
        function checkInputs() {
            const allInputs = document.querySelectorAll('.form__input');
            allInputs.forEach(input => {
                const parent = input.parentNode.parentNode;
                if (input.value && input.value.trim() !== "") {
                    parent.classList.add("focus");
                }
            });
        }

        // 1-Klik Isi Kredensial Uji Coba Admin
        function quickFillAdmin() {
            const u = document.getElementById('username');
            const p = document.getElementById('password');
            u.value = 'admin';
            p.value = 'admin123';
            checkInputs();
            p.focus();
        }

        // Tampilkan / Sembunyikan Sandi
        function togglePassword() {
            const p = document.getElementById('password');
            const icon = document.getElementById('pwd-icon');
            if (p.type === 'password') {
                p.type = 'text';
                icon.className = 'bx bx-hide';
            } else {
                p.type = 'password';
                icon.className = 'bx bx-show';
            }
        }

        // Inisialisasi saat halaman selesai dimuat
        document.addEventListener('DOMContentLoaded', () => {
            checkInputs();

            const userInput = document.getElementById('username');
            const pwdInput = document.getElementById('password');

            if (userInput && userInput.value.trim() !== '') {
                pwdInput.focus();
            } else if (userInput) {
                userInput.focus();
            }

            // Indikator loading saat submit
            const form = document.getElementById('login-form');
            const btn = document.getElementById('btn-submit');
            const btnText = document.getElementById('btn-text');

            if (form && btn) {
                form.addEventListener('submit', () => {
                    btn.disabled = true;
                    if (btnText) btnText.textContent = 'Memverifikasi...';
                });
            }
        });
    </script>
</body>
</html>
