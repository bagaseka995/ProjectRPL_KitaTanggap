@extends('layouts.auth')
@section('title', 'Manajemen Bencana — Admin')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-slate-900 flex flex-col transition-colors duration-300">
    @include('layouts.partials.navbar-main')
    @include('layouts.partials.navbar-sub')

    <div class="max-w-7xl w-full mx-auto px-4 py-8 flex-grow">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white tracking-tight">Manajemen Bencana</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kelola data kejadian bencana, pantau status siaga, dan notifikasi warga terdampak.</p>
            </div>
            <a href="{{ route('admin.bencana.create') }}"
               id="btn-tambah-bencana"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-[#1F4E79] to-[#2E75B6] text-white text-sm font-semibold rounded-xl shadow hover:shadow-lg hover:scale-105 transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Bencana
            </a>
        </div>

        {{-- Alert --}}
        @if(session('success'))
        <div class="mb-6 flex items-center gap-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-2xl px-5 py-4">
            <svg class="w-5 h-5 text-green-600 dark:text-green-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <p class="text-sm font-medium text-green-800 dark:text-green-300">{{ session('success') }}</p>
        </div>
        @endif

        {{-- Summary Cards --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-100 dark:border-slate-700 p-5 text-center shadow-sm">
                <div class="text-3xl font-extrabold text-gray-900 dark:text-white">{{ $summary['total'] }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 font-medium">Total Bencana</div>
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-green-200 dark:border-green-800/40 p-5 text-center shadow-sm">
                <div class="text-3xl font-extrabold text-green-600 dark:text-green-400">{{ $summary['aktif'] }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 font-medium">Aktif</div>
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-orange-200 dark:border-orange-800/40 p-5 text-center shadow-sm">
                <div class="text-3xl font-extrabold text-orange-500 dark:text-orange-400">{{ $summary['siaga'] }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 font-medium">Siaga</div>
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-red-200 dark:border-red-800/40 p-5 text-center shadow-sm">
                <div class="text-3xl font-extrabold text-red-600 dark:text-red-400">{{ $summary['awas'] }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 font-medium">Status Awas</div>
            </div>
        </div>

        {{-- Filter --}}
        <form method="GET" action="{{ route('admin.bencana.index') }}" class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-100 dark:border-slate-700 p-5 mb-6 flex flex-wrap gap-3 items-end shadow-sm">
            <div class="flex-1 min-w-[160px]">
                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1.5">Cari</label>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Nama / Lokasi…"
                       class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-slate-700 border border-gray-200 dark:border-slate-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2E75B6] dark:text-white">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1.5">Jenis</label>
                <select name="jenis" class="px-3 py-2 text-sm bg-gray-50 dark:bg-slate-700 border border-gray-200 dark:border-slate-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2E75B6] dark:text-white">
                    <option value="">Semua Jenis</option>
                    @foreach(['Banjir','Gempa Bumi','Tsunami','Tanah Longsor','Kebakaran','Kekeringan','Angin Puting Beliung','Gunung Meletus','Lainnya'] as $jenis)
                    <option value="{{ $jenis }}" @selected(request('jenis') === $jenis)>{{ $jenis }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1.5">Siaga</label>
                <select name="siaga" class="px-3 py-2 text-sm bg-gray-50 dark:bg-slate-700 border border-gray-200 dark:border-slate-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2E75B6] dark:text-white">
                    <option value="">Semua Status</option>
                    <option value="waspada" @selected(request('siaga') === 'waspada')>Waspada</option>
                    <option value="siaga"   @selected(request('siaga') === 'siaga')>Siaga</option>
                    <option value="awas"    @selected(request('siaga') === 'awas')>Awas</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1.5">Status</label>
                <select name="status" class="px-3 py-2 text-sm bg-gray-50 dark:bg-slate-700 border border-gray-200 dark:border-slate-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2E75B6] dark:text-white">
                    <option value="">Semua</option>
                    <option value="aktif"    @selected(request('status') === 'aktif')>Aktif</option>
                    <option value="nonaktif" @selected(request('status') === 'nonaktif')>Nonaktif</option>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-[#1F4E79] text-white text-sm font-semibold rounded-lg hover:bg-[#163859] transition">Filter</button>
            @if(request()->hasAny(['q','jenis','siaga','status']))
            <a href="{{ route('admin.bencana.index') }}" class="px-4 py-2 bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-gray-300 text-sm font-semibold rounded-lg hover:bg-gray-200 dark:hover:bg-slate-600 transition">Reset</a>
            @endif
        </form>

        {{-- Table --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-100 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-slate-700/50 text-left">
                            <th class="px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Bencana</th>
                            <th class="px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Lokasi</th>
                            <th class="px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Siaga</th>
                            <th class="px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tanggal</th>
                            <th class="px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                        @forelse($bencanaList as $bencana)
                        <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/30 transition" id="bencana-row-{{ $bencana->id }}">
                            <td class="px-5 py-4">
                                <div class="font-semibold text-gray-900 dark:text-white">{{ $bencana->nama_bencana }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $bencana->jenis_bencana }}</div>
                            </td>
                            <td class="px-5 py-4 text-gray-600 dark:text-gray-300">{{ $bencana->lokasi }}</td>
                            <td class="px-5 py-4">
                                @php
                                    $siagaColor = match($bencana->status_siaga) {
                                        'awas'    => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                        'siaga'   => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400',
                                        'waspada' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
                                        default   => 'bg-gray-100 text-gray-600',
                                    };
                                @endphp
                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold {{ $siagaColor }}">
                                    {{ ucfirst($bencana->status_siaga) }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-gray-600 dark:text-gray-300">
                                {{ $bencana->tanggal_kejadian?->format('d M Y') ?? '-' }}
                            </td>
                            <td class="px-5 py-4">
                                <button onclick="toggleAktif({{ $bencana->id }}, this)"
                                        data-aktif="{{ $bencana->status_aktif ? '1' : '0' }}"
                                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold transition
                                            {{ $bencana->status_aktif
                                                ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 hover:bg-green-200'
                                                : 'bg-gray-100 text-gray-500 dark:bg-slate-700 dark:text-gray-400 hover:bg-gray-200' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $bencana->status_aktif ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                                    <span class="label-aktif">{{ $bencana->status_aktif ? 'Aktif' : 'Nonaktif' }}</span>
                                </button>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.bencana.edit', $bencana->id) }}"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/40 rounded-lg text-xs font-semibold transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Edit
                                    </a>
                                    <a href="{{ route('donasi.show', $bencana->id) }}" target="_blank"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-900/40 rounded-lg text-xs font-semibold transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Donasi
                                    </a>
                                    <button onclick="hapusBencana({{ $bencana->id }}, '{{ addslashes($bencana->nama_bencana) }}')"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/40 rounded-lg text-xs font-semibold transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-gray-500 dark:text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="font-medium">Tidak ada data bencana.</p>
                                <a href="{{ route('admin.bencana.create') }}" class="text-[#2E75B6] hover:underline text-sm mt-1 block">+ Tambah bencana pertama</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($bencanaList->hasPages())
            <div class="px-5 py-4 border-t border-gray-100 dark:border-slate-700">
                {{ $bencanaList->links() }}
            </div>
            @endif
        </div>
    </div>

    {{-- Footer --}}
    <footer class="bg-white dark:bg-slate-900 border-t border-gray-200 dark:border-slate-800 py-6 text-center text-xs text-gray-400 dark:text-gray-500 transition-colors duration-300">
        <p>&copy; 2026 KitaTanggap Kelompok 11 RPL. All rights reserved.</p>
    </footer>
</div>

{{-- Hidden delete form --}}
<form id="form-hapus" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

<script>
async function toggleAktif(id, btn) {
    const aktif = btn.dataset.aktif === '1';
    const label = btn.querySelector('.label-aktif');
    const dot   = btn.querySelector('span:first-child');

    try {
        const res = await fetch(`/admin/bencana/${id}/toggle`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            }
        });
        const data = await res.json();
        if (data.status === 'success') {
            const isAktif = data.status_aktif;
            btn.dataset.aktif = isAktif ? '1' : '0';
            label.textContent = isAktif ? 'Aktif' : 'Nonaktif';
            // Update classes
            btn.className = `inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold transition ${
                isAktif
                    ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 hover:bg-green-200'
                    : 'bg-gray-100 text-gray-500 dark:bg-slate-700 dark:text-gray-400 hover:bg-gray-200'
            }`;
            dot.className = `w-1.5 h-1.5 rounded-full ${isAktif ? 'bg-green-500' : 'bg-gray-400'}`;
        }
    } catch(e) {
        alert('Gagal mengubah status bencana. Silakan coba lagi.');
    }
}

function hapusBencana(id, nama) {
    if (!confirm(`Hapus bencana "${nama}"?\n\nTindakan ini tidak dapat dibatalkan.`)) return;
    const form = document.getElementById('form-hapus');
    form.action = `/admin/bencana/${id}`;
    form.submit();
}
</script>
@endsection
