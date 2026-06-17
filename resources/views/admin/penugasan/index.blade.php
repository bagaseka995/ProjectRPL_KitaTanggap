@extends('layouts.auth')
@section('title', 'Dashboard Admin — Penugasan Relawan')
@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-slate-900 transition-colors duration-300">
    @include('layouts.partials.navbar-main')
    @include('layouts.partials.navbar-sub')

    {{-- Main Container --}}
    <div class="max-w-7xl mx-auto px-4 py-8" x-data="penugasanAdmin()" x-init="loadData()">

        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Penugasan Relawan Bencana</h1>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Form Penugasan (Left Panel - 1 col on lg, top on mobile) --}}
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 shadow-sm p-6 sticky top-6 transition-colors">
                    <h2 class="font-bold text-gray-800 dark:text-gray-200 text-lg mb-4 pb-2 border-b border-gray-100 dark:border-slate-700">Buat Penugasan Baru</h2>
                    
                    <form @submit.prevent="submitForm()" class="space-y-4">
                        {{-- Bencana --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Pilih Bencana <span class="text-red-500">*</span></label>
                            <select x-model="form.bencana_id" required
                                    class="w-full px-3 py-2 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:border-[#1F4E79] dark:focus:border-blue-500 focus:ring-2 focus:ring-[#1F4E79]/20 dark:focus:ring-blue-500/20 text-gray-900 dark:text-white transition">
                                <option value="">-- Pilih Bencana Aktif --</option>
                                @foreach($bencana as $b)
                                    <option value="{{ $b->id }}">{{ $b->nama_bencana }} ({{ $b->lokasi }})</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Relawan --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Pilih Relawan <span class="text-red-500">*</span></label>
                            <select x-model="form.relawan_id" required
                                    class="w-full px-3 py-2 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:border-[#1F4E79] dark:focus:border-blue-500 focus:ring-2 focus:ring-[#1F4E79]/20 dark:focus:ring-blue-500/20 text-gray-900 dark:text-white transition">
                                <option value="">-- Pilih Relawan Tersedia --</option>
                                @foreach($relawan as $r)
                                    <option value="{{ $r->id }}">
                                        {{ $r->user->nama_lengkap }} — {{ $r->keahlian }} ({{ $r->lokasi_domisili }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-1">Hanya menampilkan relawan terverifikasi yang siap bertugas (ketersediaan=true).</p>
                        </div>

                        {{-- Tanggal Tugas --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Tanggal Tugas <span class="text-red-500">*</span></label>
                            <input type="date" x-model="form.tanggal_tugas" required min="{{ date('Y-m-d') }}"
                                   class="w-full px-3 py-2 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:border-[#1F4E79] dark:focus:border-blue-500 focus:ring-2 focus:ring-[#1F4E79]/20 dark:focus:ring-blue-500/20 text-gray-900 dark:text-white transition">
                        </div>

                        {{-- Catatan --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Catatan Tugas (Opsional)</label>
                            <textarea x-model="form.catatan" rows="3" placeholder="Instruksi khusus, lokasi posko, atau kebutuhan logistik..."
                                      class="w-full px-3 py-2 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:border-[#1F4E79] dark:focus:border-blue-500 focus:ring-2 focus:ring-[#1F4E79]/20 dark:focus:ring-blue-500/20 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 transition"></textarea>
                        </div>

                        {{-- Submit --}}
                        <button type="submit" :disabled="submitting"
                                class="w-full py-2.5 bg-[#1F4E79] dark:bg-blue-600 hover:bg-[#1F4E79]/90 dark:hover:bg-blue-700 text-white rounded-xl text-sm font-bold shadow-sm hover:shadow transition disabled:opacity-50 flex items-center justify-center gap-2">
                            <svg x-show="submitting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                            </svg>
                            <span x-text="submitting ? 'Mengirim email...' : 'Tugaskan Relawan'"></span>
                        </button>
                    </form>
                </div>
            </div>

            {{-- List & Filters (Right Panel - 2 cols on lg) --}}
            <div class="lg:col-span-2">
                {{-- Filter bar --}}
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-4 mb-5 flex flex-wrap items-center gap-3 shadow-sm transition-colors">
                    <select x-model="filter.bencana_id" @change="page=1; loadData()"
                            class="px-3 py-2 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 rounded-lg text-sm focus:outline-none focus:border-[#1F4E79] dark:focus:border-blue-500 text-gray-900 dark:text-white flex-1 min-w-[150px]">
                        <option value="">Semua Bencana</option>
                        @foreach($bencana as $b)
                            <option value="{{ $b->id }}">{{ $b->nama_bencana }}</option>
                        @endforeach
                    </select>

                    <select x-model="filter.status" @change="page=1; loadData()"
                            class="px-3 py-2 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 rounded-lg text-sm focus:outline-none focus:border-[#1F4E79] dark:focus:border-blue-500 text-gray-900 dark:text-white flex-1 min-w-[120px]">
                        <option value="">Semua Status</option>
                        <option value="ditugaskan">Ditugaskan</option>
                        <option value="berlangsung">Berlangsung</option>
                        <option value="selesai">Selesai</option>
                        <option value="dibatalkan">Dibatalkan</option>
                    </select>

                    <button @click="resetFilter()"
                            class="px-4 py-2 bg-gray-100 dark:bg-slate-700 hover:bg-gray-200 dark:hover:bg-slate-600 text-gray-600 dark:text-gray-300 rounded-lg text-sm transition font-medium">
                        ↺ Reset
                    </button>
                </div>

                {{-- Loading state --}}
                <div x-show="loading" class="text-center py-12 text-gray-400 dark:text-gray-500 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-2xl shadow-sm transition-colors">
                    <svg class="w-6 h-6 animate-spin mx-auto mb-2 text-[#1F4E79]" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                    </svg>
                    Memuat data penugasan...
                </div>

                {{-- Assignments Table Card --}}
                <div x-show="!loading" class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden transition-colors">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-slate-700/50 border-b border-gray-200 dark:border-slate-700">
                                <tr class="text-left text-gray-600 dark:text-gray-300 text-xs uppercase tracking-wide">
                                    <th class="px-4 py-3 font-semibold">Relawan</th>
                                    <th class="px-4 py-3 font-semibold">Misi Bencana</th>
                                    <th class="px-4 py-3 font-semibold">Tanggal Tugas</th>
                                    <th class="px-4 py-3 font-semibold">Status</th>
                                    <th class="px-4 py-3 font-semibold">Catatan</th>
                                    <th class="px-4 py-3 font-semibold text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-if="penugasan.length === 0">
                                    <tr>
                                        <td colspan="6" class="px-4 py-12 text-center text-gray-400 dark:text-gray-500">
                                            Tidak ada data penugasan relawan ditemukan.
                                        </td>
                                    </tr>
                                </template>
                                <template x-for="p in penugasan" :key="p.id">
                                    <tr class="border-b border-gray-100 dark:border-slate-700 hover:bg-gray-50/50 dark:hover:bg-slate-700/50 transition">
                                        <td class="px-4 py-3">
                                            <p class="font-semibold text-gray-900 dark:text-white" x-text="p.relawan.user.nama_lengkap"></p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500" x-text="p.relawan.user.no_telepon"></p>
                                        </td>
                                        <td class="px-4 py-3">
                                            <p class="font-semibold text-gray-900 dark:text-white" x-text="p.bencana.nama_bencana"></p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500" x-text="p.bencana.lokasi"></p>
                                        </td>
                                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300 text-xs" x-text="p.tanggal_tugas.substring(0,10)"></td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold text-white uppercase"
                                                  :style="'background-color:' + p.warna_status"
                                                  x-text="p.status_tugas"></span>
                                        </td>
                                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs max-w-[150px] truncate" :title="p.catatan" x-text="p.catatan || '—'"></td>
                                        <td class="px-4 py-3 text-center whitespace-nowrap">
                                            <div class="flex items-center justify-center gap-1">
                                                {{-- Button start mission --}}
                                                <template x-if="p.status_tugas === 'ditugaskan'">
                                                    <button @click="updateStatus(p, 'berlangsung')" title="Mulai Misi"
                                                            class="p-1.5 bg-yellow-500 hover:bg-yellow-600 text-white rounded transition">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                                        </svg>
                                                    </button>
                                                </template>

                                                {{-- Button complete mission --}}
                                                <template x-if="p.status_tugas === 'berlangsung'">
                                                    <button @click="updateStatus(p, 'selesai')" title="Selesaikan Misi"
                                                            class="p-1.5 bg-green-500 hover:bg-green-600 text-white rounded transition">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                    </button>
                                                </template>

                                                {{-- Button cancel mission --}}
                                                <template x-if="p.status_tugas === 'ditugaskan' || p.status_tugas === 'berlangsung'">
                                                    <button @click="updateStatus(p, 'dibatalkan')" title="Batalkan Misi"
                                                            class="p-1.5 bg-red-500 hover:bg-red-600 text-white rounded transition">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                        </svg>
                                                    </button>
                                                </template>

                                                <template x-if="p.status_tugas === 'selesai' || p.status_tugas === 'dibatalkan'">
                                                    <span class="text-xs text-gray-400 dark:text-gray-500 italic">Selesai diproses</span>
                                                </template>
                                            </div>
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
            </div>
        </div>

        {{-- Toast alerts --}}
        <div x-show="toast" x-cloak x-transition
             class="fixed bottom-6 right-6 px-5 py-3.5 rounded-xl text-white text-sm font-semibold shadow-lg z-50 transition-all duration-300"
             :class="toastOk ? 'bg-green-600' : 'bg-red-600'"
             x-text="toastMsg"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function penugasanAdmin() {
    return {
        penugasan: [],
        page: 1,
        lastPage: 1,
        loading: true,
        submitting: false,
        filter: { bencana_id: '', status: '' },
        form: { bencana_id: '', relawan_id: '', tanggal_tugas: '', catatan: '' },
        toast: false,
        toastMsg: '',
        toastOk: true,

        async loadData() {
            this.loading = true;
            const params = new URLSearchParams({
                page: this.page,
                bencana_id: this.filter.bencana_id,
                status: this.filter.status,
            });
            const res = await fetch('/api/penugasan?' + params, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (res.ok) {
                const json = await res.json();
                this.penugasan = json.data;
                this.lastPage = json.last_page;
            }
            this.loading = false;
        },

        resetFilter() {
            this.filter.bencana_id = '';
            this.filter.status = '';
            this.page = 1;
            this.loadData();
        },

        showToast(msg, ok = true) {
            this.toastMsg = msg;
            this.toastOk = ok;
            this.toast = true;
            setTimeout(() => this.toast = false, 4000);
        },

        async submitForm() {
            this.submitting = true;
            const res = await fetch('/api/penugasan', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(this.form)
            });
            const json = await res.json();
            this.submitting = false;

            if (res.ok) {
                this.showToast(json.message, true);
                // Reset form
                this.form = { bencana_id: '', relawan_id: '', tanggal_tugas: '', catatan: '' };
                this.page = 1;
                this.loadData();
            } else {
                this.showToast(json.message || 'Terjadi kesalahan sistem.', false);
            }
        },

        async updateStatus(p, status) {
            let label = status === 'dibatalkan' ? 'membatalkan' : (status === 'selesai' ? 'menyelesaikan' : 'memulai');
            if (!confirm(`Apakah Anda yakin ingin ${label} misi relawan ${p.relawan.user.nama_lengkap}?`)) {
                return;
            }

            const url = status === 'selesai' 
                ? `/api/penugasan/${p.id}/selesai` 
                : `/api/penugasan/${p.id}/status`;

            const body = status === 'selesai' ? null : JSON.stringify({ status });

            const res = await fetch(url, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: body
            });
            const json = await res.json();

            if (res.ok) {
                this.showToast(json.message, true);
                this.loadData();
            } else {
                this.showToast(json.message || 'Gagal mengubah status misi.', false);
            }
        }
    };
}
</script>
@endpush
