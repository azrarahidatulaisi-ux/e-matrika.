<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="e-Matrika - Pemetaan Distribusi Penyakit Berbasis WebGIS">
    <meta name="api-base" content="<?= base_url() ?>">
    <title><?= esc($pageTitle ?? 'e-Matrika') ?> - e-Matrika</title>
    
    <!-- Favicon / Logo Icon Aplikasi -->
    <link rel="icon" type="image/png" href="<?= base_url('assets/Untitled.png') ?>">
    <link rel="apple-touch-icon" href="<?= base_url('assets/Untitled.png') ?>">
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- App Stylesheet (dengan cache-buster otomatis) -->
    <link rel="stylesheet" href="<?= base_url('css/app.css?v=' . (file_exists(FCPATH . 'css/app.css') ? filemtime(FCPATH . 'css/app.css') : time())) ?>">
    
    <!-- Inisialisasi Tema Gelap/Terang (Mencegah Layar Berkedip / FOUC) -->
    <script>
        (function() {
            try {
                const savedTheme = localStorage.getItem('ematrika-theme') || localStorage.getItem('epimap-theme') || 
                    (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
                document.documentElement.setAttribute('data-theme', savedTheme);
            } catch (e) {}
        })();
    </script>

    <?php if (($activePage ?? '') === 'map'): ?>
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />
    <?php endif; ?>
</head>
<body class="app-body">
    <!-- Mobile Sidebar Backdrop Overlay -->
    <div class="sidebar-backdrop" id="sidebar-backdrop" onclick="toggleMobileSidebar(false)"></div>

    <!-- ── Left Sidebar (Gaya Modern app-main) ── -->
    <aside class="app-sidebar" id="app-sidebar">
        <!-- Brand Logo -->
        <div class="sidebar-brand">
            <a href="<?= base_url('/map') ?>" class="brand-link" title="e-Matrika GIS">
                <div class="brand-logo-icon">
                    <img src="<?= base_url('assets/Untitled.png') ?>" alt="Logo Aplikasi" class="brand-logo-img">
                </div>
                <div class="brand-text">
                    <span class="brand-title">e-Matrika</span>
                    <span class="brand-subtitle">Health GIS</span>
                </div>
            </a>
            <button class="mobile-close-btn" onclick="toggleMobileSidebar(false)" aria-label="Tutup menu">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 6L6 18M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Sidebar Navigation Menu -->
        <nav class="sidebar-nav">
            <div class="nav-section-label">Navigasi Utama</div>
            <ul class="nav-list">
                <li>
                    <a href="<?= base_url('/map') ?>" class="nav-item <?= ($activePage ?? '') === 'map' ? 'active' : '' ?>" id="nav-map">
                        <span class="nav-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="7" height="7"></rect>
                                <rect x="14" y="3" width="7" height="7"></rect>
                                <rect x="14" y="14" width="7" height="7"></rect>
                                <rect x="3" y="14" width="7" height="7"></rect>
                            </svg>
                        </span>
                        <span class="nav-label">Dashboard & Peta</span>
                    </a>
                </li>
                <li>
                    <a href="<?= base_url('/upload') ?>" class="nav-item <?= ($activePage ?? '') === 'upload' ? 'active' : '' ?>" id="nav-upload">
                        <span class="nav-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="17 8 12 3 7 8"></polyline>
                                <line x1="12" y1="3" x2="12" y2="15"></line>
                            </svg>
                        </span>
                        <span class="nav-label">Unggah Data</span>
                    </a>
                </li>
                <?php if (session()->get('role') === 'admin'): ?>
                <li>
                    <a href="<?= base_url('/users') ?>" class="nav-item <?= ($activePage ?? '') === 'users' ? 'active' : '' ?>" id="nav-users">
                        <span class="nav-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                        </span>
                        <span class="nav-label">Pengguna</span>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>

        <!-- Sidebar Footer / Quick User Info -->
        <div class="sidebar-footer">
            <div class="sidebar-user-card">
                <div class="user-avatar-circle">
                    <?= esc(strtoupper(substr(session()->get('username') ?? 'U', 0, 1))) ?>
                </div>
                <div class="user-details">
                    <span class="user-name-text"><?= esc(session()->get('username') ?? 'Pengguna') ?></span>
                    <span class="user-role-badge <?= session()->get('role') === 'admin' ? 'role-admin' : 'role-petugas' ?>">
                        <?= esc(ucfirst(session()->get('role') ?? 'Petugas')) ?>
                    </span>
                </div>
                <a href="<?= base_url('/logout') ?>" class="logout-icon-btn" title="Keluar dari akun">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                </a>
            </div>
        </div>
    </aside>

    <!-- ── Main App Shell Wrapper ── -->
    <div class="app-main-wrapper">
        <!-- Top Header Bar (Sesuai UserMenu & Header app-main) -->
        <header class="app-header">
            <div class="header-left">
                <!-- Mobile Menu Hamburger -->
                <button type="button" class="mobile-toggle-btn" onclick="toggleMobileSidebar(true)" aria-label="Buka menu">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                </button>

                <!-- Page Headline & Status Indicator -->
                <div class="header-breadcrumb">
                    <span class="breadcrumb-root">e-Matrika</span>
                    <span class="breadcrumb-separator">/</span>
                    <h1 class="header-title"><?= esc($pageTitle ?? 'Dashboard') ?></h1>
                </div>

                <div class="live-pill">
                    <span class="live-pulse"></span>
                    <span>Live GIS</span>
                </div>
            </div>

            <div class="header-right">
                <!-- Theme Mode Toggle Button -->
                <button type="button" class="btn-theme-toggle" id="theme-toggle-btn" aria-label="Ganti mode gelap/terang" title="Beralih Mode Gelap/Terang">
                    <div class="theme-toggle-switch">
                        <span class="theme-icon sun" aria-hidden="true">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="5"></circle>
                                <line x1="12" y1="1" x2="12" y2="3"></line>
                                <line x1="12" y1="21" x2="12" y2="23"></line>
                                <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                                <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                                <line x1="1" y1="12" x2="3" y2="12"></line>
                                <line x1="21" y1="12" x2="23" y2="12"></line>
                                <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                                <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                            </svg>
                        </span>
                        <span class="theme-icon moon" aria-hidden="true">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                            </svg>
                        </span>
                        <span class="theme-toggle-thumb"></span>
                    </div>
                    <span class="theme-toggle-text" id="theme-toggle-text">Mode Gelap</span>
                </button>

                <a href="<?= base_url('/upload') ?>" class="btn-header-action" title="Unggah data rekam medis baru">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="17 8 12 3 7 8"></polyline>
                        <line x1="12" y1="3" x2="12" y2="15"></line>
                    </svg>
                    <span>Impor Kasus</span>
                </a>

                <div class="header-user-pill">
                    <div class="header-avatar">
                        <?= esc(strtoupper(substr(session()->get('username') ?? 'U', 0, 1))) ?>
                    </div>
                    <div class="header-user-meta">
                        <span class="header-username"><?= esc(session()->get('username') ?? '') ?></span>
                    </div>
                    <a href="<?= base_url('/logout') ?>" class="header-logout-link" title="Keluar">
                        Keluar
                    </a>
                </div>
            </div>
        </header>

        <!-- Flash Messages -->
        <?php if (session()->getFlashdata('error')): ?>
        <div class="toast-banner toast-error">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
            <span><?= esc(session()->getFlashdata('error')) ?></span>
        </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('success')): ?>
        <div class="toast-banner toast-success">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
            <span><?= esc(session()->getFlashdata('success')) ?></span>
        </div>
        <?php endif; ?>

        <!-- Content Area -->
        <main class="app-content-body">
            <?= $content ?? '' ?>
        </main>
    </div>

    <!-- Mobile Drawer Toggle Script -->
    <script>
        function toggleMobileSidebar(show) {
            const sidebar = document.getElementById('app-sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');
            if (!sidebar || !backdrop) return;
            if (show) {
                sidebar.classList.add('mobile-open');
                backdrop.classList.add('active');
            } else {
                sidebar.classList.remove('mobile-open');
                backdrop.classList.remove('active');
            }
        }

        // ── e-Matrika Theme Switcher Controller ──
        (function() {
            const toggleBtn = document.getElementById('theme-toggle-btn');
            const toggleText = document.getElementById('theme-toggle-text');

            function syncThemeUI(theme) {
                const isDark = theme === 'dark';
                document.documentElement.setAttribute('data-theme', theme);

                if (toggleText) {
                    toggleText.textContent = isDark ? 'Mode Terang' : 'Mode Gelap';
                }
                if (toggleBtn) {
                    toggleBtn.setAttribute('title', isDark ? 'Ubah ke Mode Terang' : 'Ubah ke Mode Gelap');
                    toggleBtn.setAttribute('aria-label', isDark ? 'Ubah ke Mode Terang' : 'Ubah ke Mode Gelap');
                }

                // Sync with Dashboard panel toggle if present
                const btnLight = document.getElementById('btn-theme-light');
                const btnDark = document.getElementById('btn-theme-dark');
                if (btnLight && btnDark) {
                    if (isDark) {
                        btnDark.classList.add('active');
                        btnLight.classList.remove('active');
                    } else {
                        btnLight.classList.add('active');
                        btnDark.classList.remove('active');
                    }
                }
            }

            function applyTheme(theme) {
                try {
                    localStorage.setItem('ematrika-theme', theme);
                    localStorage.setItem('epimap-theme', theme);
                } catch (e) {}
                syncThemeUI(theme);
                window.dispatchEvent(new CustomEvent('ematrika-theme-change', { detail: { theme: theme } }));
                window.dispatchEvent(new CustomEvent('epimap-theme-change', { detail: { theme: theme } }));
            }

            // Inisialisasi status awal
            const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
            syncThemeUI(currentTheme);

            if (toggleBtn) {
                toggleBtn.addEventListener('click', function() {
                    const activeTheme = document.documentElement.getAttribute('data-theme') || 'light';
                    applyTheme(activeTheme === 'dark' ? 'light' : 'dark');
                });
            }

            // Expose globally
            window.eMatrikaSetTheme = applyTheme;
            window.epiMapSetTheme = applyTheme;
        })();
    </script>

    <?php if (($activePage ?? '') === 'map'): ?>
    <!-- Leaflet JS & Plugins -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
    <script src="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>
    <script src="<?= base_url('js/map.js?v=' . (file_exists(FCPATH . 'js/map.js') ? filemtime(FCPATH . 'js/map.js') : time())) ?>"></script>
    <?php endif; ?>
</body>
</html>
