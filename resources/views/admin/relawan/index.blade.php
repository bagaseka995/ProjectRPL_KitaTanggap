@extends('layouts.auth')
@section('title','Dashboard Admin — Relawan')
@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-slate-900 transition-colors duration-300">
    @include('layouts.partials.navbar-main')
    @include('layouts.partials.navbar-sub')
<div class="max-w-7xl mx-auto px-4 py-8" x-data="relawanAdmin()" x-init="loadData()">

    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Manajemen Relawan</h1>

    {{-- Summary cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-4 text-center shadow-sm">
            <p class="text-3xl font-bold text-gray-800 dark:text-white" x-text="summary.total ?? '{{ $summary['total'] }}'"></p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Total</p>
        </div>
        <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-xl border border-yellow-200 dark:border-yellow-800/50 p-4 text-center shadow-sm">
            <p class="text-3xl font-bold text-yellow-700 dark:text-yellow-500" x-text="summary.pending ?? '{{ $summary['pending'] }}'"></p>
            <p class="text-xs text-yellow-600 dark:text-yellow-400 mt-1">Pending</p>
        </div>
        <div class="bg-green-50 dark:bg-green-900/20 rounded-xl border border-green-200 dark:border-green-800/50 p-4 text-center shadow-sm">
            <p class="text-3xl font-bold text-green-700 dark:text-green-500" x-text="summary.terverifikasi ?? '{{ $summary['terverifikasi'] }}'"></p>
            <p class="text-xs text-green-600 dark:text-green-400 mt-1">Terverifikasi</p>
        </div>
        <div class="bg-red-50 dark:bg-red-900/20 rounded-xl border border-red-200 dark:border-red-800/50 p-4 text-center shadow-sm">
            <p class="text-3xl font-bold text-red-700 dark:text-red-500" x-text="summary.ditolak ?? '{{ $summary['ditolak'] }}'"></p>
            <p class="text-xs text-red-600 dark:text-red-400 mt-1">Ditolak</p>
        </div>
    </div>

    {{-- Filter --}}
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-4 mb-5 flex flex-wrap gap-3">
        <select x-model="filter.status" @change="loadData()"
                class="px-3 py-2 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 rounded-lg text-sm focus:outline-none focus:border-[#1F4E79] dark:focus:border-blue-500 text-gray-900 dark:text-white">
            <option value="">Semua Status</option>
            <option value="pending">Pending</option>
            <option value="terverifikasi">Terverifikasi</option>
            <option value="ditolak">Ditolak</option>
        </select>
        <input x-model="filter.keahlian" @input.debounce.400ms="loadData()"
               type="text" placeholder="Filter keahlian..."
               class="px-3 py-2 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 rounded-lg text-sm focus:outline-none focus:border-[#1F4E79] dark:focus:border-blue-500 flex-1 min-w-32 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500">
        <input x-model="filter.lokasi" @input.debounce.400ms="loadData()"
               type="text" placeholder="Filter lokasi..."
               class="px-3 py-2 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 rounded-lg text-sm focus:outline-none focus:border-[#1F4E79] dark:focus:border-blue-500 flex-1 min-w-32 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500">
        <button @click="filter={status:'',keahlian:'',lokasi:''};loadData()"
                class="px-4 py-2 bg-gray-100 dark:bg-slate-700 hover:bg-gray-200 dark:hover:bg-slate-600 text-gray-600 dark:text-gray-300 rounded-lg text-sm transition">
            ↺ Reset
        </button>
    </div>

    {{-- Loading --}}
    <div x-show="loading" class="text-center py-12 text-gray-400">
        <svg class="w-6 h-6 animate-spin mx-auto mb-2 text-[#1F4E79]" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
        </svg>
        Memuat data...
    </div>

    {{-- Tabel --}}
    <div x-show="!loading" class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-slate-700/50 border-b border-gray-200 dark:border-slate-700">
                    <tr class="text-left text-gray-600 dark:text-gray-300 text-xs uppercase tracking-wide">
                        <th class="px-4 py-3 font-semibold">Nama</th>
                        <th class="px-4 py-3 font-semibold">Keahlian</th>
                        <th class="px-4 py-3 font-semibold">Lokasi</th>
                        <th class="px-4 py-3 font-semibold">Status</th>
                        <th class="px-4 py-3 font-semibold">Daftar</th>
                        <th class="px-4 py-3 font-semibold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="relawan.length === 0">
                        <tr><td colspan="6" class="px-4 py-10 text-center text-gray-400 dark:text-gray-500">Tidak ada data relawan.</td></tr>
                    </template>
                    <template x-for="r in relawan" :key="r.id">
                        <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-50 dark:hover:bg-slate-700/50 transition">
                            <td class="px-4 py-3">
                                <p class="font-semibold text-gray-900 dark:text-white" x-text="r.user.nama_lengkap"></p>
                                <p class="text-xs text-gray-400 dark:text-gray-500" x-text="r.user.email"></p>
                                <p class="text-xs text-gray-400 dark:text-gray-500" x-text="r.user.no_telepon"></p>
                            </td>
                            <td class="px-4 py-3 max-w-[180px]">
                                <div class="flex flex-wrap gap-1">
                                    <template x-for="k in r.keahlian_array" :key="k">
                                        <span class="px-2 py-0.5 bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded-full text-xs" x-text="k"></span>
                                    </template>
                                    <span x-show="!r.keahlian_array || r.keahlian_array.length===0" class="text-gray-400 dark:text-gray-500 text-xs">—</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300" x-text="r.lokasi_domisili || '—'"></td>
                            <td class="px-4 py-3">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold text-white uppercase"
                                      :style="'background-color:' + r.warna_status"
                                      x-text="r.status_verifikasi"></span>
                            </td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs" x-text="r.created_at ? r.created_at.substring(0,10) : '—'"></td>
                            <td class="px-4 py-3 text-center">
                                <template x-if="r.status_verifikasi === 'pending'">
                                    <div class="flex items-center justify-center gap-2">
                                        <button @click="aksiVerifikasi(r, 'terverifikasi')"
                                                class="px-3 py-1.5 bg-green-500 hover:bg-green-600 text-white text-xs font-semibold rounded-lg transition">
                                            ✓ Verifikasi
                                        </button>
                                        <button @click="konfirmasiTolak(r)"
                                                class="px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs font-semibold rounded-lg transition">
                                            ✗ Tolak
                                        </button>
                                    </div>
                                </template>
                                <template x-if="r.status_verifikasi !== 'pending'">
                                    <span class="text-xs text-gray-400 italic">Sudah diproses</span>
                                </template>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="px-4 py-3 flex items-center justify-between border-t border-gray-100 dark:border-slate-700 text-sm text-gray-500 dark:text-gray-400">
            <span>Halaman <span x-text="page"></span> dari <span x-text="lastPage"></span></span>
            <div class="flex gap-2">
                <button @click="page > 1 && (page--, loadData())"
                        :disabled="page <= 1"
                        class="px-3 py-1.5 border dark:border-slate-600 rounded-lg disabled:opacity-40 hover:bg-gray-50 dark:hover:bg-slate-700 transition">← Sebelumnya</button>
                <button @click="page < lastPage && (page++, loadData())"
                        :disabled="page >= lastPage"
                        class="px-3 py-1.5 border dark:border-slate-600 rounded-lg disabled:opacity-40 hover:bg-gray-50 dark:hover:bg-slate-700 transition">Berikutnya →</button>
            </div>
        </div>
    </div>

    {{-- Dialog konfirmasi tolak --}}
    <div x-show="dialog" x-cloak class="fixed inset-0 bg-black/50 dark:bg-black/70 flex items-center justify-center z-50 p-4">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl max-w-sm w-full p-6">
            <h3 class="font-bold text-gray-900 dark:text-white text-lg mb-2">Konfirmasi Penolakan</h3>
            <p class="text-gray-600 dark:text-gray-300 text-sm mb-5">
                Yakin menolak pendaftaran <strong x-text="dialogRelawan?.user?.nama_lengkap"></strong>?
                Email pemberitahuan akan dikirim ke relawan.
            </p>
            <div class="flex gap-3">
                <button @click="dialog=false" class="flex-1 py-2.5 border border-gray-300 dark:border-slate-600 rounded-xl text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700 transition">Batal</button>
                <button @click="aksiVerifikasi(dialogRelawan,'ditolak')"
                        class="flex-1 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-xl text-sm font-semibold transition">
                    Ya, Tolak & Kirim Email
                </button>
            </div>
        </div>
    </div>

    {{-- Toast notifikasi --}}
    <div x-show="toast" x-cloak x-transition
         class="fixed bottom-6 right-6 px-5 py-3.5 rounded-xl text-white text-sm font-medium shadow-lg z-50"
         :class="toastOk ? 'bg-green-600' : 'bg-red-600'"
         x-text="toastMsg"></div>
</div>
</div>
@endsection

@push('scripts')
<script>
function relawanAdmin() {
    return {
        relawan: [], summary: {}, loading: true,
        page: 1, lastPage: 1,
        filter: { status: '', keahlian: '', lokasi: '' },
        dialog: false, dialogRelawan: null,
        toast: false, toastMsg: '', toastOk: true,

        async loadData() {
            this.loading = true;
            const p = new URLSearchParams({
                page: this.page,
                ...Object.fromEntries(Object.entries(this.filter).filter(([,v])=>v))
            });
            const res = await fetch('/api/relawan?' + p, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const json = await res.json();
            this.relawan  = json.data.data;
            this.summary  = json.summary;
            this.lastPage = json.data.last_page;
            this.loading  = false;
        },

        konfirmasiTolak(r) {
            this.dialogRelawan = r;
            this.dialog = true;
        },

        async aksiVerifikasi(r, aksi) {
            this.dialog = false;
            const res = await fetch('/api/relawan/' + r.id + '/verifikasi', {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '',
                },
                body: JSON.stringify({ aksi }),
            });
            const json = await res.json();
            this.toastOk  = res.ok;
            this.toastMsg = json.message;
            this.toast    = true;
            setTimeout(() => this.toast = false, 3500);
            if (res.ok) this.loadData();
        },
    };
}
</script>
@endpush
