/**
 * e-Matrika – Map Module
 * Leaflet + Esri basemaps, heatmap, clustering
 */
(function () {
    'use strict';

    /* ── Config ── */
    const API_BASE = (document.querySelector('meta[name="api-base"]')?.content || '').replace(/\/+$/, '');
    const ESRI_ATTR = 'Tiles &copy; Esri';

    /* ── Basemaps ── */
    const basemaps = {
        'Jalan': L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}', {
            attribution: ESRI_ATTR + ' &mdash; Esri, DeLorme, NAVTEQ',
            maxZoom: 19
        }),
        'Gelap (Dark)': L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Canvas/World_Dark_Gray_Base/MapServer/tile/{z}/{y}/{x}', {
            attribution: ESRI_ATTR + ' &mdash; Esri, HERE, Garmin',
            maxZoom: 16
        }),
        'Satelit': L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: ESRI_ATTR + ' &mdash; Esri, Maxar, Earthstar',
            maxZoom: 19
        }),
        'Topografi': L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}', {
            attribution: ESRI_ATTR + ' &mdash; Esri, HERE, Garmin',
            maxZoom: 19
        }),
        'Abu-abu terang': L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Canvas/World_Light_Gray_Base/MapServer/tile/{z}/{y}/{x}', {
            attribution: ESRI_ATTR + ' &mdash; Esri, HERE, Garmin',
            maxZoom: 16
        })
    };

    /* ── Initialize Map with Active Theme ── */
    const initialTheme = document.documentElement.getAttribute('data-theme') || 'light';
    const initialBasemap = initialTheme === 'dark' ? basemaps['Gelap (Dark)'] : basemaps['Jalan'];

    const map = L.map('map', {
        center: [-0.95, 100.35],   // Default: Padang, Sumatera Barat
        zoom: 12,
        layers: [initialBasemap],
        zoomControl: false
    });

    // Zoom control (top-right, ArcGIS style)
    L.control.zoom({ position: 'topright' }).addTo(map);

    // Scale control
    L.control.scale({ position: 'bottomright', metric: true, imperial: false }).addTo(map);

    // Layer control for basemaps
    L.control.layers(basemaps, null, { position: 'topright', collapsed: true }).addTo(map);

    /* ── Sinkronisasi Tema Otomatis pada Basemap Peta ── */
    function onThemeChange(e) {
        const theme = e.detail && e.detail.theme;
        if (theme === 'dark') {
            if (map.hasLayer(basemaps['Jalan']) || map.hasLayer(basemaps['Abu-abu terang'])) {
                map.removeLayer(basemaps['Jalan']);
                map.removeLayer(basemaps['Abu-abu terang']);
                basemaps['Gelap (Dark)'].addTo(map);
            }
        } else if (theme === 'light') {
            if (map.hasLayer(basemaps['Gelap (Dark)'])) {
                map.removeLayer(basemaps['Gelap (Dark)']);
                basemaps['Jalan'].addTo(map);
            }
        }
    }
    window.addEventListener('ematrika-theme-change', onThemeChange);
    window.addEventListener('epimap-theme-change', onThemeChange);

    /* ── Coordinate display on mouse move ── */
    const coordEl = document.getElementById('coord-display');
    const coordValEl = coordEl ? (coordEl.querySelector('.coord-val') || coordEl) : null;
    if (coordValEl) {
        map.on('mousemove', function (e) {
            coordValEl.textContent = e.latlng.lat.toFixed(5) + ', ' + e.latlng.lng.toFixed(5);
        });
    }

    /* ── Data layers ── */
    let markerCluster = null;
    let heatLayer = null;
    let allCases = [];
    let currentMode = 'titik'; // 'titik' | 'panas'

    /* ── Diagnosis color palette ── */
    const COLORS = [
        '#e6194b', '#3cb44b', '#4363d8', '#f58231', '#911eb4',
        '#42d4f4', '#f032e6', '#bfef45', '#fabed4', '#469990',
        '#dcbeff', '#9A6324', '#800000', '#aaffc3', '#808000',
        '#ffd8b1', '#000075', '#a9a9a9'
    ];
    const diagColorMap = {};
    let colorIdx = 0;

    function getDiagColor(code) {
        if (!code) return '#999';
        if (!diagColorMap[code]) {
            diagColorMap[code] = COLORS[colorIdx % COLORS.length];
            colorIdx++;
        }
        return diagColorMap[code];
    }

    /* ── Fetch cases from API ── */
    function loadCases() {
        fetch(API_BASE + '/api/cases')
            .then(r => r.json())
            .then(data => {
                allCases = data.cases || [];
                updateSummary(data);
                renderCaseList(allCases);
                renderMap();
            })
            .catch(err => {
                console.error('Gagal memuat data:', err);
            });
    }

    /* ── Render map markers or heatmap ── */
    function renderMap() {
        clearLayers();

        if (currentMode === 'titik') {
            renderMarkers();
        } else {
            renderHeatmap();
        }
    }

    function clearLayers() {
        if (markerCluster) {
            map.removeLayer(markerCluster);
            markerCluster = null;
        }
        if (heatLayer) {
            map.removeLayer(heatLayer);
            heatLayer = null;
        }
    }

    function renderMarkers() {
        markerCluster = L.markerClusterGroup({
            maxClusterRadius: 40,
            spiderfyOnMaxZoom: true,
            showCoverageOnHover: false,
            disableClusteringAtZoom: 16
        });

        allCases.forEach(c => {
            if (c.latitude == null || c.longitude == null) return;

            const color = getDiagColor(c.icd10_code);
            const icon = L.divIcon({
                className: 'custom-marker',
                html: '<div style="width:12px;height:12px;border-radius:50%;background:' + color + ';border:2px solid #fff;box-shadow:0 1px 3px rgba(0,0,0,.3);"></div>',
                iconSize: [12, 12],
                iconAnchor: [6, 6]
            });

            const marker = L.marker([c.latitude, c.longitude], { icon: icon });

            // Popup content
            const isSensitive = c.sensitif == 1 || c.sensitif === true;
            let popupHtml = '<div class="popup-title">' + (isSensitive ? '<em>Data dilindungi</em>' : escHtml(c.nama)) + '</div>';
            popupHtml += '<div class="popup-row"><span class="popup-label">Diagnosa:</span> ' + escHtml(c.icd10_code || '') + ' – ' + escHtml(c.diagnosa_teks || c.icd10_code || '') + '</div>';
            popupHtml += '<div class="popup-row"><span class="popup-label">Tanggal:</span> ' + escHtml(c.tanggal_kunjungan) + '</div>';
            if (!isSensitive) {
                popupHtml += '<div class="popup-row"><span class="popup-label">Alamat:</span> ' + escHtml(c.alamat) + '</div>';
            }
            if (c.lokasi_perkiraan) {
                popupHtml += '<div class="popup-meta" style="color:#ca8a04;">📍 Lokasi perkiraan</div>';
            }

            marker.bindPopup(popupHtml, { maxWidth: 280 });
            markerCluster.addLayer(marker);
        });

        map.addLayer(markerCluster);

        // Fit bounds if there are cases
        if (allCases.length > 0) {
            const valid = allCases.filter(c => c.latitude && c.longitude);
            if (valid.length > 0) {
                const bounds = L.latLngBounds(valid.map(c => [c.latitude, c.longitude]));
                map.fitBounds(bounds, { padding: [30, 30], maxZoom: 15 });
            }
        }
    }

    function renderHeatmap() {
        const points = allCases
            .filter(c => c.latitude && c.longitude)
            .map(c => [parseFloat(c.latitude), parseFloat(c.longitude), 1]);

        if (points.length > 0) {
            heatLayer = L.heatLayer(points, {
                radius: 25,
                blur: 15,
                maxZoom: 16,
                gradient: {
                    0.2: '#ffffb2',
                    0.4: '#fed976',
                    0.6: '#feb24c',
                    0.8: '#f03b20',
                    1.0: '#bd0026'
                }
            }).addTo(map);
        }
    }

    /* ── Update summary stats ── */
    function updateSummary(data) {
        const totalEl = document.getElementById('stat-total');
        const diagEl = document.getElementById('stat-diagnosa');
        const mappedEl = document.getElementById('stat-mapped');

        if (totalEl) totalEl.textContent = Number(data.total || 0).toLocaleString('id-ID');
        if (diagEl) diagEl.textContent = Number(data.unique_diagnosa || 0).toLocaleString('id-ID');

        const mappedCount = (data.cases || []).filter(c => c.latitude != null && c.longitude != null).length;
        if (mappedEl) mappedEl.textContent = Number(mappedCount).toLocaleString('id-ID');
    }

    /* ── Render case list in sidebar ── */
    function renderCaseList(cases) {
        const listEl = document.getElementById('case-list');
        if (!listEl) return;

        if (cases.length === 0) {
            listEl.innerHTML = '<li class="loading-case-item">Belum ada data kasus yang cocok.</li>';
            return;
        }

        const maxShow = 200;
        const showing = cases.slice(0, maxShow);
        let html = '';
        showing.forEach(c => {
            const isSensitive = c.sensitif == 1;
            const color = getDiagColor(c.icd10_code);
            const hasCoord = (c.latitude != null && c.longitude != null);

            html += '<li class="case-card-item' + (!hasCoord ? ' unmapped' : '') + '" data-lat="' + (c.latitude || '') + '" data-lng="' + (c.longitude || '') + '">';
            html += '  <div class="case-card-top">';
            html += '    <span class="case-diag-dot" style="background:' + color + '"></span>';
            html += '    <span class="case-patient-name">' + (isSensitive ? '<em>Data dilindungi</em>' : escHtml(c.nama)) + '</span>';
            html += '    <span class="case-icd-pill">' + escHtml(c.icd10_code || '–') + '</span>';
            html += '  </div>';
            html += '  <div class="case-card-meta">';
            html += '    <span class="case-date-text">' + escHtml(c.tanggal_kunjungan) + '</span>';
            if (c.lokasi_perkiraan) {
                html += '    <span class="case-geo-status warning">Perkiraan</span>';
            } else if (!hasCoord) {
                html += '    <span class="case-geo-status error">Tanpa Titik</span>';
            }
            html += '  </div>';
            html += '</li>';
        });
        if (cases.length > maxShow) {
            html += '<li class="case-more-info">… dan ' + (cases.length - maxShow) + ' kasus lainnya</li>';
        }
        listEl.innerHTML = html;

        // Click to center map
        listEl.querySelectorAll('.case-card-item').forEach(li => {
            li.addEventListener('click', function () {
                const lat = parseFloat(this.dataset.lat);
                const lng = parseFloat(this.dataset.lng);
                if (!isNaN(lat) && !isNaN(lng)) {
                    map.setView([lat, lng], 16, { animate: true });
                }
            });
        });
    }

    /* ── Utility: escape HTML ── */
    function escHtml(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    /* ── Event listeners ── */

    // Mode toggle (titik / panas)
    document.querySelectorAll('.btn-toggle[data-mode]').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.btn-toggle[data-mode]').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentMode = this.dataset.mode;

            const modeBadge = document.getElementById('stat-mode-badge');
            if (modeBadge) {
                modeBadge.textContent = currentMode === 'titik' ? 'Titik Klaster' : 'Peta Panas (Heatmap)';
            }

            renderMap();
        });
    });

    // Fit all
    document.getElementById('btn-fit-all')?.addEventListener('click', function () {
        const valid = allCases.filter(c => c.latitude && c.longitude);
        if (valid.length > 0) {
            const bounds = L.latLngBounds(valid.map(c => [c.latitude, c.longitude]));
            map.fitBounds(bounds, { padding: [30, 30], maxZoom: 15 });
        }
    });

    /* ── Init ── */
    loadCases();

    // Trigger Leaflet size recalculation for responsive container
    setTimeout(() => {
        map.invalidateSize();
    }, 300);

})();
