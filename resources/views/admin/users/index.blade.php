@extends('layouts.auth')
@section('title', 'Dashboard Admin — Verifikasi Pengguna')
@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-slate-900 transition-colors duration-300">
    {{-- Navbar --}}
    @include('layouts.partials.navbar-admin')

    <div class="max-w-7xl mx-auto px-4 py-8" x-data="userAdmin()" x-init="loadData()">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Verifikasi & Manajemen Pengguna</h1>

        {{-- Summary cards --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-4 text-center shadow-sm transition-colors">
                <p class="text-3xl font-bold text-gray-800 dark:text-white" x-text="summary.total ?? '{{ $summary['total'] }}'"></p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Total Pengguna</p>
            </div>
            <div class="bg-yellow-50 dark:bg-yellow-950/30 rounded-xl border border-yellow-200 dark:border-yellow-900/50 p-4 text-center shadow-sm transition-colors">
                <p class="text-3xl font-bold text-yellow-700 dark:text-yellow-400" x-text="summary.pending ?? '{{ $summary['pending'] }}'"></p>
                <p class="text-xs text-yellow-600 dark:text-yellow-500 mt-1">Pending (Menunggu ACC)</p>
            </div>
            <div class="bg-green-50 dark:bg-green-950/30 rounded-xl border border-green-200 dark:border-green-900/50 p-4 text-center shadow-sm transition-colors">
                <p class="text-3xl font-bold text-green-700 dark:text-green-400" x-text="summary.aktif ?? '{{ $summary['aktif'] }}'"></p>
                <p class="text-xs text-green-600 dark:text-green-500 mt-1">Aktif (Telah ACC)</p>
            </div>
            <div class="bg-red-50 dark:bg-red-950/30 rounded-xl border border-red-200 dark:border-red-900/50 p-4 text-center shadow-sm transition-colors">
                <p class="text-3xl font-bold text-red-700 dark:text-red-400" x-text="summary.nonaktif ?? '{{ $summary['nonaktif'] }}'"></p>
                <p class="text-xs text-red-600 dark:text-red-500 mt-1">Nonaktif / Ditolak</p>
            </div>
        </div>

        {{-- Filter --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 p-4 mb-5 flex flex-wrap gap-3 items-center transition-colors">
            <select x-model="filter.status" @change="loadData()"
                    class="px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:border-[#1F4E79] dark:focus:border-blue-500 transition">
                <option value="">Semua Status</option>
                <option value="pending">Pending</option>
                <option value="aktif">Aktif</option>
                <option value="nonaktif">Nonaktif</option>
            </select>

            <select x-model="filter.peran" @change="loadData()"
                    class="px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:border-[#1F4E79] dark:focus:border-blue-500 transition">
                <option value="">Semua Peran</option>
                <option value="relawan">🤝 Relawan</option>
                <option value="donatur">💙 Donatur</option>
            </select>

            <input x-model="filter.cari" @input.debounce.400ms="loadData()"
                   type="text" placeholder="Cari nama atau email..."
                   class="px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:border-[#1F4E79] dark:focus:border-blue-500 transition flex-1 min-w-[200px]">

            <button @click="filter={status:'',peran:'',cari:''};loadData()"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-gray-600 dark:text-gray-300 rounded-lg text-sm transition">
                ↺ Reset
            </button>
        </div>

        {{-- Loading --}}
        <div x-show="loading" class="text-center py-12 text-gray-400">
            <svg class="w-6 h-6 animate-spin mx-auto mb-2 text-[#1F4E79] dark:text-blue-500" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
            </svg>
            Memuat data pendaftar...
        </div>

        {{-- Tabel --}}
        <div x-show="!loading" class="bg-white dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden transition-colors">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-slate-700/50 border-b border-gray-200 dark:border-slate-700">
                        <tr class="text-left text-gray-600 dark:text-gray-400 text-xs uppercase tracking-wide">
                            <th class="px-6 py-3.5 font-semibold">Profil Pengguna</th>
                            <th class="px-6 py-3.5 font-semibold">Peran</th>
                            <th class="px-6 py-3.5 font-semibold">Status Akun</th>
                            <th class="px-6 py-3.5 font-semibold">Tanggal Daftar</th>
                            <th class="px-6 py-3.5 font-semibold text-center">Aksi / Kontrol</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-if="users.length === 0">
                            <tr><td colspan="5" class="px-6 py-10 text-center text-gray-400 dark:text-gray-500">Tidak ada data pengguna.</td></tr>
                        </template>
                        <template x-for="u in users" :key="u.id">
                            <tr class="border-b border-gray-100 dark:border-slate-700/50 hover:bg-gray-50/50 dark:hover:bg-slate-700/50 transition">
                                <td class="px-6 py-4">
                                    <p class="font-semibold text-gray-900 dark:text-white" x-text="u.nama_lengkap"></p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500" x-text="u.email"></p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500" x-text="u.no_telepon || '—'"></p>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold capitalize"
                                          :class="u.peran === 'relawan' ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-400'"
                                          x-text="u.peran"></span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold text-white uppercase"
                                          :style="'background-color:' + u.warna_status"
                                          x-text="u.status_akun"></span>
                                </td>
                                <td class="px-6 py-4 text-gray-500 dark:text-gray-400 text-xs" x-text="u.created_at ? u.created_at.substring(0,10) + ' ' + u.created_at.substring(11,16) : '—'"></td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <template x-if="u.status_akun === 'pending'">
                                            <div class="flex gap-2">
                                                <button @click="aksiVerifikasi(u, 'aktif')"
                                                        class="px-3.5 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold rounded-lg transition shadow-sm hover:shadow">
                                                    ✓ Setujui (ACC)
                                                </button>
                                                <button @click="konfirmasiTolak(u)"
                                                        class="px-3.5 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-lg transition shadow-sm hover:shadow">
                                                    ✗ Tolak
                                                </button>
                                            </div>
                                        </template>
                                        <template x-if="u.status_akun === 'aktif'">
                                            <button @click="aksiVerifikasi(u, 'nonaktif')"
                                                    class="px-3 py-1 bg-gray-100 hover:bg-red-50 text-gray-600 hover:text-red-600 dark:bg-slate-700 dark:hover:bg-red-950/40 dark:text-gray-300 dark:hover:text-red-400 text-xs rounded-lg transition">
                                                ✗ Tangguhkan Akun
                                            </button>
                                        </template>
                                        <template x-if="u.status_akun === 'nonaktif'">
                                            <button @click="aksiVerifikasi(u, 'aktif')"
                                                    class="px-3 py-1 bg-green-50 hover:bg-green-100 dark:bg-green-950/30 dark:hover:bg-green-900/40 text-green-700 dark:text-green-400 text-xs font-semibold rounded-lg transition">
                                                ✓ Aktifkan Kembali
                                            </button>
                                        </template>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-6 py-4 flex items-center justify-between border-t border-gray-100 dark:border-slate-700 text-sm text-gray-500 dark:text-gray-400 bg-gray-50/50 dark:bg-slate-800/50">
                <span>Halaman <span class="font-bold text-gray-900 dark:text-white" x-text="page"></span> dari <span class="font-bold text-gray-900 dark:text-white" x-text="lastPage"></span></span>
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
        <div x-show="dialog" x-cloak class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4 backdrop-blur-sm">
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl max-w-sm w-full p-6 border dark:border-slate-700">
                <h3 class="font-bold text-gray-900 dark:text-white text-lg mb-2">Konfirmasi Penolakan Akun</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm mb-5">
                    Apakah Anda yakin menolak pendaftaran akun pendaftar bernama <strong class="text-gray-900 dark:text-white" x-text="dialogUser?.nama_lengkap"></strong>? Akun ini tidak akan bisa login ke sistem.
                </p>
                <div class="flex gap-3">
                    <button @click="dialog=false" class="flex-1 py-2.5 border border-gray-300 dark:border-slate-600 rounded-xl text-sm font-medium hover:bg-gray-50 dark:hover:bg-slate-700 text-gray-700 dark:text-gray-300 transition">Batal</button>
                    <button @click="aksiVerifikasi(dialogUser, 'nonaktif')"
                            class="flex-1 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm font-semibold transition">
                        Ya, Tolak Akun
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
function userAdmin() {
    return {
        users: [], summary: {}, loading: true,
        page: 1, lastPage: 1,
        filter: { status: '', peran: '', cari: '' },
        dialog: false, dialogUser: null,
        toast: false, toastMsg: '', toastOk: true,

        async loadData() {
            this.loading = true;
            const p = new URLSearchParams({
                page: this.page,
                ...Object.fromEntries(Object.entries(this.filter).filter(([,v])=>v))
            });
            const res = await fetch('/api/admin/users?' + p, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const json = await res.json();
            this.users    = json.data.data;
            this.summary  = json.summary;
            this.lastPage = json.data.last_page;
            this.loading  = false;
        },

        konfirmasiTolak(u) {
            this.dialogUser = u;
            this.dialog = true;
        },

        async aksiVerifikasi(u, aksi) {
            this.dialog = false;
            const res = await fetch('/api/admin/users/' + u.id + '/verifikasi', {
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
