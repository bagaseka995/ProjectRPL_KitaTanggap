@extends('layouts.auth')
@section('title', 'Riwayat Misi Saya')
@section('content')
<div class="min-h-screen bg-gray-50">
    {{-- Navbar --}}
    <nav class="bg-[#1F4E79] text-white shadow">
        <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('dashboard') }}" class="font-bold text-lg hover:opacity-90 transition">
                    KitaTanggap — Relawan
                </a>
                <div class="hidden md:flex gap-3 text-sm">
                    <a href="{{ route('dashboard') }}" class="px-3 py-1 rounded hover:bg-[#2E75B6]/50 transition">Beranda</a>
                    <a href="{{ route('relawan.profil') }}" class="px-3 py-1 rounded hover:bg-[#2E75B6]/50 transition">Profil Saya</a>
                    <a href="{{ route('relawan.riwayat.index') }}" class="px-3 py-1 rounded bg-[#2E75B6] font-medium transition">Riwayat Misi</a>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('pengaturan.notifikasi') }}" class="text-sm opacity-75 hover:opacity-100 transition hidden md:inline-block">Pengaturan</a>
                <form method="POST" action="{{ route('logout') }}">@csrf
                    <button class="text-sm opacity-75 hover:opacity-100 transition">Keluar</button>
                </form>
            </div>
        </div>
    </nav>

    {{-- Main Content --}}
    <div class="max-w-4xl mx-auto px-4 py-8" x-data="riwayatMisi()" x-init="loadData()">
        {{-- Navigation for Mobile --}}
        <div class="flex md:hidden gap-2 mb-6 text-sm">
            <a href="{{ route('dashboard') }}" class="flex-1 text-center py-2 rounded-lg bg-white border text-gray-700 hover:bg-gray-50">Beranda</a>
            <a href="{{ route('relawan.profil') }}" class="flex-1 text-center py-2 rounded-lg bg-white border text-gray-700 hover:bg-gray-50">Profil</a>
            <a href="{{ route('relawan.riwayat.index') }}" class="flex-1 text-center py-2 rounded-lg bg-[#1F4E79] text-white font-medium shadow-sm">Misi</a>
        </div>

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Riwayat Misi Kemanusiaan</h1>
            <p class="text-sm text-gray-500 mt-1">Daftar penugasan penanganan bencana Anda baik yang aktif maupun yang sudah selesai.</p>
        </div>

        {{-- Loading --}}
        <div x-show="loading" class="text-center py-16 text-gray-400 bg-white border rounded-2xl shadow-sm">
            <svg class="w-6 h-6 animate-spin mx-auto mb-2 text-[#1F4E79]" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
            </svg>
            Memuat data riwayat misi...
        </div>

        {{-- Main list container --}}
        <div x-show="!loading" class="space-y-4">
            <template x-if="riwayat.length === 0">
                <div class="text-center py-16 bg-white border rounded-2xl p-6 shadow-sm">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                    <h3 class="font-bold text-gray-700 text-base">Belum Ada Penugasan</h3>
                    <p class="text-xs text-gray-400 mt-1 max-w-sm mx-auto">Profil Anda harus terverifikasi oleh admin terlebih dahulu sebelum mendapatkan misi kebencanaan.</p>
                    <a href="{{ route('relawan.profil') }}" class="mt-4 inline-block px-4 py-2 bg-[#1F4E79] hover:bg-[#1F4E79]/90 text-white text-xs font-semibold rounded-lg transition shadow-sm">
                        Cek Profil Saya
                    </a>
                </div>
            </template>

            {{-- Cards/Timeline List --}}
            <div class="relative pl-6 border-l-2 border-gray-200 ml-4 space-y-6">
                <template x-for="misi in riwayat" :key="misi.id">
                    <div class="relative">
                        {{-- Timeline Dot icon --}}
                        <span class="absolute -left-[35px] top-1.5 flex items-center justify-center w-6 h-6 rounded-full ring-4 ring-gray-50 border text-white font-bold"
                              :style="'background-color:' + misi.warna_status">
                            <span class="text-[9px]" x-text="misi.status_tugas.substring(0,1).toUpperCase()"></span>
                        </span>
                        
                        {{-- Card content --}}
                        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 hover:shadow transition duration-200">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 mb-3">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900" x-text="misi.bencana.nama_bencana"></h3>
                                    <p class="text-xs text-gray-500 flex items-center gap-1.5 mt-0.5">
                                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <span x-text="misi.bencana.lokasi"></span>
                                    </p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-0.5 rounded text-[10px] font-bold text-white uppercase tracking-wider"
                                          :style="'background-color:' + misi.warna_status"
                                          x-text="misi.status_tugas"></span>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border-t border-b border-gray-100 py-4 mb-4">
                                <div>
                                    <p class="text-xs text-gray-400 font-semibold uppercase">Tanggal Tugas</p>
                                    <p class="text-sm font-semibold text-gray-700 mt-0.5" x-text="misi.tanggal_tugas.substring(0,10)"></p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400 font-semibold uppercase">Catatan Misi</p>
                                    <p class="text-sm text-gray-600 italic mt-0.5" x-text="misi.catatan || 'Tidak ada catatan khusus.'"></p>
                                </div>
                            </div>

                            {{-- Certificate download section --}}
                            <template x-if="misi.status_tugas === 'selesai'">
                                <div class="flex items-center justify-between bg-gray-50 rounded-xl p-3 border border-gray-200 text-sm">
                                    <div class="flex items-center gap-2 text-gray-700">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                        </svg>
                                        <span class="font-medium">Sertifikat Penghargaan Misi</span>
                                    </div>
                                    <div>
                                        <template x-if="misi.sertifikat">
                                            <a :href="'/' + misi.sertifikat.file_path" target="_blank"
                                               class="inline-flex items-center gap-1.5 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-xs font-bold rounded-lg transition shadow-sm">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                </svg>
                                                Unduh Sertifikat
                                            </a>
                                        </template>
                                        <template x-if="!misi.sertifikat">
                                            <span class="text-xs text-yellow-600 font-semibold italic flex items-center gap-1 bg-yellow-50 px-3 py-1.5 border border-yellow-200 rounded-lg">
                                                <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                                                </svg>
                                                Sertifikat sedang diproses
                                            </span>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function riwayatMisi() {
    return {
        riwayat: [],
        loading: true,

        async loadData() {
            this.loading = true;
            const res = await fetch('/api/relawan/riwayat', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (res.ok) {
                const json = await res.json();
                this.riwayat = json.data;
            }
            this.loading = false;
        }
    };
}
</script>
@endpush
