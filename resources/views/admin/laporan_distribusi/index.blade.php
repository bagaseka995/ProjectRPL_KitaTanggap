@extends('layouts.auth')
@section('title', 'Dashboard Admin — Laporan Distribusi')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-slate-900 flex flex-col justify-between transition-colors duration-300">
    @include('layouts.partials.navbar-main')
    @include('layouts.partials.navbar-sub')

    {{-- Main Container --}}
    <div class="max-w-6xl w-full mx-auto px-4 py-8 flex-grow">

        {{-- Success Flash Alert --}}
        @if(session('success'))
            <div class="mb-6 flex items-start gap-3 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800/50 text-green-800 dark:text-green-400 rounded-xl px-4 py-3 text-sm animate-fade-up">
                <svg class="w-5 h-5 mt-0.5 shrink-0 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold text-gray-950 dark:text-white tracking-tight">Laporan Distribusi Bantuan & Penggunaan Dana</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Catat dan kelola riwayat penggunaan dana untuk transparansi donasi kepada donatur.</p>
            </div>
            <a href="{{ route('admin.laporan-distribusi.create') }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-[#1F4E79] hover:bg-[#163859] text-white text-sm font-semibold rounded-xl transition shadow-sm gap-1.5 self-start sm:self-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Laporan Baru
            </a>
        </div>

        {{-- Table Card --}}
        @if($laporanList->isEmpty())
            <div class="text-center py-16 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-2xl p-6 shadow-sm transition-colors">
                <div class="w-16 h-16 bg-blue-50 dark:bg-blue-900/30 text-[#1F4E79] dark:text-blue-400 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="font-bold text-gray-800 dark:text-gray-200 text-lg">Belum Ada Laporan Distribusi</h3>
                <p class="text-sm text-gray-400 mt-1 max-w-md mx-auto">
                    Mulai catat laporan penggunaan dana donasi kebencanaan agar donatur dapat memantau transparansi penyaluran bantuan.
                </p>
                <div class="mt-6 flex justify-center gap-3">
                    <a href="{{ route('admin.laporan-distribusi.create') }}" class="px-5 py-2.5 bg-[#1F4E79] hover:bg-[#163859] text-white text-sm font-semibold rounded-xl transition shadow-sm">
                        Buat Laporan Pertama
                    </a>
                </div>
            </div>
        @else
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden mb-6 transition-colors">
                {{-- Desktop View Table --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-slate-700/50 border-b border-gray-100 dark:border-slate-700 text-xs font-bold text-gray-400 dark:text-gray-400 uppercase tracking-wider">
                                <th class="p-4 pl-6 w-16">No</th>
                                <th class="p-4">Bencana</th>
                                <th class="p-4">Tanggal</th>
                                <th class="p-4 text-right">Jumlah Disalurkan</th>
                                <th class="p-4">Rincian Penggunaan</th>
                                <th class="p-4 pr-6 w-36">Bukti</th>
                                <th class="p-4 pr-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-slate-700 text-sm text-gray-700 dark:text-gray-300">
                            @foreach($laporanList as $idx => $laporan)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-slate-700/50 transition">
                                    <td class="p-4 pl-6 text-gray-400 dark:text-gray-500 font-semibold">
                                        {{ $laporanList->firstItem() + $idx }}
                                    </td>
                                    <td class="p-4">
                                        <div class="font-bold text-gray-900 dark:text-white leading-tight">
                                            {{ $laporan->bencana->nama_bencana }}
                                        </div>
                                        <div class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                                            {{ $laporan->bencana->lokasi }}
                                        </div>
                                    </td>
                                    <td class="p-4 text-xs font-medium text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                        {{ $laporan->tanggal_laporan?->format('d M Y') ?? $laporan->created_at->format('d M Y') }}
                                    </td>
                                    <td class="p-4 text-right font-extrabold text-[#1F4E79] dark:text-blue-400">
                                        {{ $laporan->jumlah_formatted }}
                                    </td>
                                    <td class="p-4 max-w-xs text-gray-650 dark:text-gray-300 truncate-cell" title="{{ $laporan->rincian_penggunaan }}">
                                        <div class="line-clamp-2 text-xs leading-relaxed">
                                            {{ $laporan->rincian_penggunaan }}
                                        </div>
                                    </td>
                                    <td class="p-4 pr-6 whitespace-nowrap">
                                        @if($laporan->bukti_distribusi)
                                            <a href="{{ $laporan->bukti_url }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1 bg-blue-50 dark:bg-blue-900/30 text-[#1F4E79] dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/50 text-xs font-bold rounded-lg border border-blue-200 dark:border-blue-800/50 transition">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                </svg>
                                                Lihat Bukti
                                            </a>
                                        @else
                                            <span class="text-xs text-gray-450 dark:text-gray-500 italic">Tidak ada bukti</span>
                                        @endif
                                    </td>
                                    <td class="p-4 pr-6 whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('admin.laporan-distribusi.edit', $laporan->id) }}"
                                               class="inline-flex items-center gap-1 px-3 py-1.5 bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 hover:bg-amber-100 rounded-lg text-xs font-semibold transition">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                Edit
                                            </a>
                                            <button onclick="hapusLaporan({{ $laporan->id }})"
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 hover:bg-red-100 rounded-lg text-xs font-semibold transition">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Mobile View Cards --}}
                <div class="md:hidden divide-y divide-gray-100 dark:divide-slate-700">
                    @foreach($laporanList as $laporan)
                        <div class="p-5 hover:bg-gray-50/50 dark:hover:bg-slate-700/50 transition">
                            <div class="flex items-center justify-between gap-3 mb-2">
                                <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">
                                    {{ $laporan->tanggal_laporan?->format('d M Y') ?? $laporan->created_at->format('d M Y') }}
                                </span>
                                @if($laporan->bukti_distribusi)
                                    <a href="{{ $laporan->bukti_url }}" target="_blank" class="inline-flex items-center gap-1 text-[10px] font-bold text-[#1F4E79] dark:text-blue-400 hover:underline">
                                        Lihat Bukti →
                                    </a>
                                @endif
                            </div>
                            <h3 class="text-sm font-bold text-gray-900 dark:text-white leading-tight mb-2">
                                {{ $laporan->bencana->nama_bencana }}
                            </h3>
                            <p class="text-xs text-gray-650 dark:text-gray-300 mb-3 bg-gray-50 dark:bg-slate-700/50 p-2.5 rounded-lg border border-gray-100 dark:border-slate-600 leading-relaxed line-clamp-3">
                                {{ $laporan->rincian_penggunaan }}
                            </p>
                            <div class="flex items-center justify-between gap-2 mt-3">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.laporan-distribusi.edit', $laporan->id) }}"
                                       class="text-xs font-semibold text-amber-600 dark:text-amber-400 hover:underline">Edit</a>
                                    <button onclick="hapusLaporan({{ $laporan->id }})" class="text-xs font-semibold text-red-600 dark:text-red-400 hover:underline">Hapus</button>
                                </div>
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-gray-400 dark:text-gray-500 mr-2">Disalurkan:</span>
                                    <span class="font-extrabold text-[#1F4E79] dark:text-blue-400">{{ $laporan->jumlah_formatted }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Pagination --}}
            <div class="mt-4 px-2">
                {{ $laporanList->links() }}
            </div>
        @endif
    </div>

    {{-- Footer --}}
    <footer class="bg-white dark:bg-slate-900 border-t border-gray-200 dark:border-slate-800 py-6 text-center text-xs text-gray-400 dark:text-gray-500 mt-8 transition-colors duration-300">
        <p>&copy; 2026 KitaTanggap Kelompok 11 RPL. All rights reserved.</p>
    </footer>
</div>
@endsection

<form id="form-hapus-laporan" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

<script>
function hapusLaporan(id) {
    if (!confirm('Hapus laporan distribusi ini? Tindakan tidak dapat dibatalkan.')) return;
    const form = document.getElementById('form-hapus-laporan');
    form.action = `/admin/laporan-distribusi/${id}`;
    form.submit();
}
</script>
