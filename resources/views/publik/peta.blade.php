@extends('layouts.auth')
@section('title', 'Peta Bencana')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<style>
    #map { height: 500px; width: 100%; z-index: 0; border-radius: 0.75rem; }
    .leaflet-popup-content-wrapper { border-radius: 12px; box-shadow: 0 4px 24px rgba(0,0,0,.15); }
    .leaflet-popup-content { margin: 12px 16px; min-width: 220px; }
    .legend { background: white; padding: 12px 16px; border-radius: 10px;
              box-shadow: 0 2px 12px rgba(0,0,0,.15); line-height: 1.8; font-size: 13px; }
    .legend-dot { display:inline-block; width:12px; height:12px;
                  border-radius:50%; margin-right:7px; vertical-align:middle; }
    .card-bencana { transition: all .2s; cursor: pointer; }
    .card-bencana:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,.12); }
    .card-bencana.active { border-color: #1F4E79 !important; box-shadow: 0 0 0 3px rgba(31,78,121,.25); }
    @keyframes pulse-dot { 0%,100%{opacity:1} 50%{opacity:.4} }
    .animate-pulse-dot { animation: pulse-dot 1.5s infinite; }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-slate-900 flex flex-col transition-colors duration-300">

{{-- Navbar Publik --}}
@include('layouts.partials.navbar-main')
@include('layouts.partials.navbar-sub')

{{-- Konten Utama --}}
<div class="max-w-7xl mx-auto px-4 py-6 flex-grow" x-data="petaApp()">

    <!-- Header -->
    <div class="mb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">🗺️ Peta Bencana Indonesia</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-0.5">Informasi bencana aktif secara real-time</p>
        </div>
        <div class="flex items-center gap-2">
            <!-- Tombol Lokasi Saya -->
            <button @click="keLokasiSaya()"
                    class="flex items-center gap-2 px-4 py-2 bg-[#1F4E79] hover:bg-[#163859] text-white text-sm font-medium rounded-xl transition shadow">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Lokasi Saya
            </button>
        </div>
    </div>

    <!-- ─── PANEL FILTER (REQ-11) ─── -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 shadow-sm p-4 mb-5 transition-colors">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <!-- Cari lokasi -->
            <input x-model="filter.lokasi" @input.debounce.300ms="applyFilter()"
                   type="text" placeholder="🔍 Cari lokasi..."
                   class="px-3 py-2 border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-900 dark:text-white placeholder-gray-450 dark:placeholder-gray-500 rounded-xl text-sm focus:outline-none focus:border-[#1F4E79] focus:ring-2 focus:ring-[#1F4E79]/20 transition">

            <!-- Jenis bencana -->
            <select x-model="filter.jenis" @change="applyFilter()"
                    class="px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:border-[#1F4E79] transition bg-white dark:bg-slate-700 text-gray-900 dark:text-white">
                <option value="">Semua Jenis</option>
                <option value="banjir">🌊 Banjir</option>
                <option value="gempa">⚡ Gempa</option>
                <option value="tsunami">🌊 Tsunami</option>
                <option value="erupsi">🌋 Erupsi</option>
                <option value="tanah_longsor">⛰️ Tanah Longsor</option>
                <option value="lainnya">Lainnya</option>
            </select>

            <!-- Status siaga -->
            <select x-model="filter.siaga" @change="applyFilter()"
                    class="px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:border-[#1F4E79] transition bg-white dark:bg-slate-700 text-gray-900 dark:text-white">
                <option value="">Semua Status</option>
                <option value="awas">🔴 Awas</option>
                <option value="siaga">🟠 Siaga</option>
                <option value="waspada">🟡 Waspada</option>
            </select>

            <!-- Tanggal dari -->
            <input x-model="filter.dari" @change="applyFilter()"
                   type="date"
                   class="px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:border-[#1F4E79] transition bg-white dark:bg-slate-700 text-gray-900 dark:text-white">

            <!-- Tombol reset -->
            <button @click="resetFilter()"
                    class="px-4 py-2 bg-gray-100 dark:bg-slate-700 hover:bg-gray-200 dark:hover:bg-slate-650 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-xl transition">
                ↺ Reset Filter
            </button>
        </div>

        <!-- Counter -->
        <div class="mt-3 text-sm text-gray-500 dark:text-gray-400 flex items-center gap-2">
            <span class="animate-pulse-dot inline-block w-2 h-2 rounded-full bg-green-500"></span>
            Menampilkan <span class="font-semibold text-gray-800 dark:text-white mx-1" x-text="tampil"></span>
            dari <span class="font-semibold text-gray-800 dark:text-white mx-1" x-text="total"></span> bencana aktif
        </div>
    </div>

    <!-- ─── PETA LEAFLET (REQ-08) ─── -->
    <div class="relative mb-6">
        <div id="map" class="shadow-md border border-gray-200 dark:border-slate-700"></div>
        <!-- Loading overlay -->
        <div x-show="loading" x-cloak
             class="absolute inset-0 flex items-center justify-center bg-white/80 dark:bg-slate-900/80 rounded-xl z-10">
            <div class="flex flex-col items-center gap-3 text-[#1F4E79] dark:text-blue-450">
                <svg class="w-8 h-8 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                </svg>
                <span class="text-sm font-medium">Memuat data bencana...</span>
            </div>
        </div>
    </div>

    <!-- ─── LIST CARD BENCANA (REQ-11) ─── -->
    <div class="mb-4">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-3">
            Daftar Bencana Aktif
            <span class="ml-2 text-sm font-normal text-gray-500 dark:text-gray-400">
                (klik card untuk zoom ke peta)
            </span>
        </h2>

        <!-- Empty state -->
        <div x-show="tampil === 0" x-cloak class="text-center py-12 text-gray-400 dark:text-gray-500">
            <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
            </svg>
            <p class="font-medium">Tidak ada bencana yang cocok dengan filter.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <template x-for="b in bencanaVisible" :key="b.id">
                <div class="card-bencana bg-white dark:bg-slate-800 rounded-2xl border-2 border-gray-100 dark:border-slate-700/60 p-4 shadow-sm"
                     :id="'card-' + b.id"
                     @click="zoomToMarker(b)">

                    <!-- Header card -->
                    <div class="flex items-start justify-between gap-2 mb-3">
                        <h3 class="font-semibold text-gray-900 dark:text-white text-sm leading-tight" x-text="b.nama_bencana"></h3>
                        <!-- Badge status -->
                        <span class="shrink-0 px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wide text-white"
                              :style="'background-color:' + b.warna_siaga"
                              x-text="b.status_siaga"></span>
                    </div>

                    <!-- Info rows -->
                    <div class="space-y-1.5 text-xs text-gray-500 dark:text-gray-400">
                        <div class="flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 text-[#1F4E79] dark:text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            </svg>
                            <span x-text="b.lokasi" class="truncate"></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 text-[#1F4E79] dark:text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span x-text="b.tanggal_kejadian"></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 text-[#1F4E79] dark:text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="capitalize" x-text="b.jenis_bencana.replace('_',' ')"></span>
                        </div>
                    </div>

                    <!-- Deskripsi singkat -->
                    <p class="mt-2 text-xs text-gray-400 dark:text-gray-500 line-clamp-2" x-text="b.deskripsi"></p>

                    <a :href="'/donasi/' + b.id"
                       @click.stop
                       class="mt-3 inline-flex items-center gap-1 text-xs font-semibold text-[#1F4E79] dark:text-blue-400 hover:underline">
                        Lihat Detail &rarr;
                    </a>
                </div>
            </template>
        </div>
    </div>
    <footer class="bg-white dark:bg-slate-900 border-t border-gray-200 dark:border-slate-800 py-6 text-center text-xs text-gray-400 dark:text-gray-500 transition-colors duration-300">
        <p>&copy; 2026 KitaTanggap Kelompok 11 RPL. All rights reserved.</p>
    </footer>
</div>

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
function petaApp() {
    return {
        map: null,
        markers: [],       // { id, data, circle, popup }
        bencanaAll: [],    // semua data dari API
        bencanaVisible: [], // yang tampil di card list
        loading: true,
        total: 0,
        tampil: 0,
        filter: { lokasi: '', jenis: '', siaga: '', dari: '' },

        async init() {
            this.initMap();
            await this.fetchData();
            this.loading = false;
        },

        initMap() {
            this.map = L.map('map', {
                center: [-2.5489, 118.0149],   // Pusat Indonesia
                zoom: 5,
                zoomControl: true,
            });

            // Tile OpenStreetMap
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© <a href="https://openstreetmap.org">OpenStreetMap</a> contributors',
                maxZoom: 18,
            }).addTo(this.map);

            // ─── Legenda (REQ-09) ───────────────────────────────────
            const legend = L.control({ position: 'bottomright' });
            legend.onAdd = () => {
                const div = L.DomUtil.create('div', 'legend');
                div.innerHTML = `
                    <div style="font-weight:700;margin-bottom:6px;color:#1F4E79;font-size:13px;">
                        🚨 Status Siaga
                    </div>
                    <div><span class="legend-dot" style="background:#EF4444"></span> Awas</div>
                    <div><span class="legend-dot" style="background:#F97316"></span> Siaga</div>
                    <div><span class="legend-dot" style="background:#EAB308"></span> Waspada</div>
                `;
                return div;
            };
            legend.addTo(this.map);
        },

        async fetchData() {
            try {
                const res  = await fetch('/api/bencana/peta');
                const data = await res.json();
                this.bencanaAll = data;
                this.total      = data.length;
                this.renderMarkers(data);
                this.bencanaVisible = data;
                this.tampil = data.length;
            } catch (e) {
                console.error('Gagal memuat data bencana:', e);
            }
        },

        renderMarkers(data) {
            // Bersihkan marker lama
            this.markers.forEach(m => this.map.removeLayer(m.circle));
            this.markers = [];

            data.forEach(b => {
                if (!b.latitude || !b.longitude) return;

                const circle = L.circleMarker([b.latitude, b.longitude], {
                    radius:      10,
                    fillColor:   b.warna_siaga,
                    color:       '#ffffff',
                    weight:      2,
                    fillOpacity: 0.85,
                });

                const statusBadge = `<span style="
                    display:inline-block;padding:2px 8px;border-radius:999px;
                    background:${b.warna_siaga};color:#fff;font-size:11px;font-weight:700;
                    text-transform:uppercase;">${b.status_siaga}</span>`;

                const popupHtml = `
                    <div style="font-family:Inter,sans-serif;">
                        <div style="font-size:14px;font-weight:700;color:#1F4E79;margin-bottom:6px;">
                            ${b.nama_bencana}
                        </div>
                        <table style="font-size:12px;color:#4B5563;border-collapse:collapse;width:100%;">
                            <tr><td style="padding:2px 0;color:#9CA3AF;width:60px;">Jenis</td>
                                <td style="text-transform:capitalize">${b.jenis_bencana.replace('_',' ')}</td></tr>
                            <tr><td style="padding:2px 0;color:#9CA3AF;">Lokasi</td>
                                <td>${b.lokasi}</td></tr>
                            <tr><td style="padding:2px 0;color:#9CA3AF;">Status</td>
                                <td>${statusBadge}</td></tr>
                            <tr><td style="padding:2px 0;color:#9CA3AF;">Tanggal</td>
                                <td>${b.tanggal_kejadian}</td></tr>
                        </table>
                        <p style="font-size:11px;color:#6B7280;margin:8px 0 6px;line-height:1.5;">
                            ${(b.deskripsi || '').substring(0, 120)}${b.deskripsi?.length > 120 ? '...' : ''}
                        </p>
                        <a href="/bencana/${b.id}"
                           style="font-size:12px;color:#1F4E79;font-weight:600;text-decoration:none;">
                            Lihat Detail →
                        </a>
                    </div>`;

                circle.bindPopup(popupHtml, { maxWidth: 280 });

                // Klik marker → scroll & highlight card
                circle.on('click', () => {
                    this.highlightCard(b.id);
                });

                circle.addTo(this.map);
                this.markers.push({ id: b.id, data: b, circle });
            });
        },

        applyFilter() {
            const { lokasi, jenis, siaga, dari } = this.filter;

            const filtered = this.bencanaAll.filter(b => {
                const matchLokasi = !lokasi || b.lokasi.toLowerCase().includes(lokasi.toLowerCase())
                                             || b.nama_bencana.toLowerCase().includes(lokasi.toLowerCase());
                const matchJenis  = !jenis  || b.jenis_bencana === jenis;
                const matchSiaga  = !siaga  || b.status_siaga  === siaga;
                const matchDari   = !dari   || b.tanggal_kejadian >= dari;
                return matchLokasi && matchJenis && matchSiaga && matchDari;
            });

            // Update marker visibilitas
            const filteredIds = new Set(filtered.map(b => b.id));
            this.markers.forEach(m => {
                if (filteredIds.has(m.id)) {
                    if (!this.map.hasLayer(m.circle)) m.circle.addTo(this.map);
                } else {
                    if (this.map.hasLayer(m.circle)) this.map.removeLayer(m.circle);
                }
            });

            this.bencanaVisible = filtered;
            this.tampil = filtered.length;
        },

        resetFilter() {
            this.filter = { lokasi: '', jenis: '', siaga: '', dari: '' };
            this.applyFilter();
        },

        zoomToMarker(b) {
            this.map.setView([b.latitude, b.longitude], 10, { animate: true });
            const marker = this.markers.find(m => m.id === b.id);
            if (marker) marker.circle.openPopup();
            this.highlightCard(b.id);
        },

        highlightCard(id) {
            // Hapus semua highlight
            document.querySelectorAll('.card-bencana').forEach(c => c.classList.remove('active'));
            const card = document.getElementById('card-' + id);
            if (card) {
                card.classList.add('active');
                card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        },

        keLokasiSaya() {
            if (!navigator.geolocation) {
                alert('Browser Anda tidak mendukung geolokasi.');
                return;
            }
            navigator.geolocation.getCurrentPosition(
                pos => {
                    const { latitude: lat, longitude: lng } = pos.coords;
                    this.map.setView([lat, lng], 10, { animate: true });
                    L.marker([lat, lng])
                        .addTo(this.map)
                        .bindPopup('<b>📍 Lokasi Anda</b>').openPopup();
                },
                () => alert('Gagal mendapatkan lokasi. Pastikan izin lokasi diaktifkan.')
            );
        },
    };
}
</script>
@endpush
@endsection
