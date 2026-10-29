<!-- e-Matrika Modern Dashboard & WebGIS Workspace -->
<div class="dashboard-container">
    <!-- ── Top Metric Cards Row (Sesuai Gaya Overview app-main) ── -->
    <div class="metrics-grid">
        <!-- Metric 1: Total Kasus -->
        <div class="metric-card group">
            <div class="metric-card-inner">
                <div class="metric-header">
                    <span class="metric-title">Total Kasus Terdata</span>
                    <div class="metric-icon-box blue">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="20" x2="18" y2="10"></line>
                            <line x1="12" y1="20" x2="12" y2="4"></line>
                            <line x1="6" y1="20" x2="6" y2="14"></line>
                        </svg>
                    </div>
                </div>
                <div class="metric-value-row">
                    <div class="metric-number" id="stat-total">0</div>
                </div>
                <div class="metric-subtext">
                    <span class="subtext-badge positive">Rekam Medis</span>
                    <span>Kasus pasien terdata</span>
                </div>
            </div>
        </div>

        <!-- Metric 2: Ragam Diagnosa -->
        <div class="metric-card group">
            <div class="metric-card-inner">
                <div class="metric-header">
                    <span class="metric-title">Klasifikasi Penyakit</span>
                    <div class="metric-icon-box purple">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                            <line x1="7" y1="7" x2="7.01" y2="7"></line>
                        </svg>
                    </div>
                </div>
                <div class="metric-value-row">
                    <div class="metric-number" id="stat-diagnosa">0</div>
                </div>
                <div class="metric-subtext">
                    <span class="subtext-badge neutral">ICD-10</span>
                    <span>Varian diagnosa aktif</span>
                </div>
            </div>
        </div>

        <!-- Metric 3: Titik Geospasial -->
        <div class="metric-card group">
            <div class="metric-card-inner">
                <div class="metric-header">
                    <span class="metric-title">Titik Terpetakan</span>
                    <div class="metric-icon-box emerald">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                    </div>
                </div>
                <div class="metric-value-row">
                    <div class="metric-number" id="stat-mapped">0</div>
                </div>
                <div class="metric-subtext">
                    <span class="subtext-badge positive">GIS Aktif</span>
                    <span>Koordinat valid di peta</span>
                </div>
            </div>
        </div>

        <!-- Metric 4: Mode Layer & Analisis -->
        <div class="metric-card group">
            <div class="metric-card-inner">
                <div class="metric-header">
                    <span class="metric-title">Mode Tampilan Peta</span>
                    <div class="metric-icon-box amber">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                            <polyline points="2 17 12 22 22 17"></polyline>
                            <polyline points="2 12 12 17 22 12"></polyline>
                        </svg>
                    </div>
                </div>
                <div class="metric-value-row">
                    <div class="metric-mode-status" id="stat-mode-badge">Titik Klaster</div>
                </div>
                <div class="metric-subtext">
                    <span class="subtext-badge neutral">ArcGIS Base</span>
                    <span>Visualisasi sebaran epidemiologi</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Main Map Workspace (Card Layout) ── -->
    <div class="workspace-card">
        <!-- ── Sidebar Filter & Daftar Kasus ── -->
        <aside class="panel-sidebar" id="panel-sidebar">
            <div class="panel-header">
                <div class="panel-header-title">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                        <polyline points="2 17 12 22 22 17"></polyline>
                        <polyline points="2 12 12 17 22 12"></polyline>
                    </svg>
                    <span>Kontrol & Daftar Kasus</span>
                </div>
            </div>

            <div class="panel-body">

                <!-- Switch Mode Visualisasi -->
                <div class="filter-group">
                    <label class="filter-label">Mode Visualisasi Spasial</label>
                    <div class="view-mode-toggle">
                        <button type="button" class="btn-toggle active" data-mode="titik">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            <span>Titik Klaster</span>
                        </button>
                        <button type="button" class="btn-toggle" data-mode="panas">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"></path>
                            </svg>
                            <span>Peta Panas (Heatmap)</span>
                        </button>
                    </div>
                </div>

                <!-- Switch Tema Tampilan Dashboard (Gelap / Terang) -->
                <div class="filter-group">
                    <label class="filter-label">Tema Tampilan Dashboard</label>
                    <div class="theme-mode-toggle" id="panel-theme-toggle">
                        <button type="button" class="btn-toggle" id="btn-theme-light" onclick="(window.eMatrikaSetTheme || window.epiMapSetTheme) && (window.eMatrikaSetTheme || window.epiMapSetTheme)('light')">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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
                            <span>Terang</span>
                        </button>
                        <button type="button" class="btn-toggle" id="btn-theme-dark" onclick="(window.eMatrikaSetTheme || window.epiMapSetTheme) && (window.eMatrikaSetTheme || window.epiMapSetTheme)('dark')">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                            </svg>
                            <span>Gelap</span>
                        </button>
                    </div>
                </div>

                <!-- Tombol Kembali ke Semua Kasus -->
                <div class="filter-group">
                    <button class="btn-modern-secondary" id="btn-fit-all">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"></polygon>
                        </svg>
                        <span>Fokus ke Seluruh Wilayah</span>
                    </button>
                </div>

                <!-- Daftar Kasus Terkini -->
                <div class="cases-panel-section">
                    <div class="cases-header-row">
                        <span class="filter-label" style="margin-bottom:0;">Daftar Kasus</span>
                        <span class="live-tag">Interaktif</span>
                    </div>
                    <ul class="case-list-modern" id="case-list">
                        <li class="loading-case-item">Memuat data kasus…</li>
                    </ul>
                </div>
            </div>
        </aside>

        <!-- ── Main GIS Map Canvas ── -->
        <div class="panel-map-container">
            <div id="map"></div>
            <!-- Live Coordinate HUD Overlay (Glassmorphism Pill) -->
            <div class="coord-hud" id="coord-display">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="2" y1="12" x2="22" y2="12"></line>
                    <line x1="12" y1="2" x2="12" y2="22"></line>
                </svg>
                <span class="coord-val">–, –</span>
            </div>
        </div>
    </div>
</div>
