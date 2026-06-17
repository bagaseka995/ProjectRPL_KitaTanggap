@extends('layouts.auth')
@section('title', 'Edit Laporan Distribusi — Admin')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-slate-900 flex flex-col justify-between transition-colors duration-300">
    @include('layouts.partials.navbar-main')
    @include('layouts.partials.navbar-sub')

    <div class="max-w-3xl w-full mx-auto px-4 py-8 flex-grow">

        {{-- Breadcrumbs / Back button --}}
        <div class="mb-6">
            <a href="{{ route('admin.laporan-distribusi.index') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-[#1F4E79] dark:text-blue-400 hover:underline">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Daftar Laporan
            </a>
        </div>

        {{-- Form Card --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 shadow-sm p-8 transition-colors">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Edit Laporan Distribusi</h2>
            <p class="text-sm text-gray-400 dark:text-gray-500 mb-6 border-b border-gray-100 dark:border-slate-700 pb-4">
                Perbarui rincian penggunaan dana distribusi untuk bencana <strong class="text-gray-700 dark:text-gray-300">{{ $laporan->bencana->nama_bencana ?? '—' }}</strong>.
            </p>

            @if($errors->any())
            <div class="mb-5 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-xl p-4">
                <ul class="text-sm text-red-700 dark:text-red-400 space-y-1">
                    @foreach($errors->all() as $e)<li>• {{ $e }}</li>@endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('admin.laporan-distribusi.update', $laporan->id) }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- Bencana --}}
                <div>
                    <label for="bencana_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                        Pilih Bencana <span class="text-red-500">*</span>
                    </label>
                    <select id="bencana_id" name="bencana_id" required
                            class="w-full px-4 py-2.5 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:border-[#1F4E79] dark:focus:border-blue-500 focus:ring-2 focus:ring-[#1F4E79]/20 dark:focus:ring-blue-500/20 text-gray-900 dark:text-white transition {{ $errors->has('bencana_id') ? 'border-red-400 bg-red-50 dark:bg-red-900/20' : '' }}">
                        <option value="" disabled>— Pilih Bencana —</option>
                        @foreach($bencanaList as $bencana)
                            <option value="{{ $bencana->id }}" {{ old('bencana_id', $laporan->bencana_id) == $bencana->id ? 'selected' : '' }}>
                                {{ $bencana->nama_bencana }} ({{ $bencana->lokasi }})
                            </option>
                        @endforeach
                    </select>
                    @error('bencana_id')
                        <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Jumlah Disalurkan --}}
                <div>
                    <label for="jumlah_disalurkan" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                        Jumlah Dana yang Disalurkan <span class="text-gray-400 dark:text-gray-500 font-normal">(opsional)</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500 text-sm font-bold">Rp</span>
                        <input id="jumlah_disalurkan" name="jumlah_disalurkan" type="number"
                               value="{{ old('jumlah_disalurkan', $laporan->jumlah_disalurkan) }}" min="0" placeholder="Contoh: 15000000"
                               class="w-full pl-10 pr-4 py-2.5 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:border-[#1F4E79] dark:focus:border-blue-500 focus:ring-2 focus:ring-[#1F4E79]/20 text-gray-900 dark:text-white transition {{ $errors->has('jumlah_disalurkan') ? 'border-red-400 bg-red-50 dark:bg-red-900/20' : '' }}">
                    </div>
                    @error('jumlah_disalurkan')
                        <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Rincian Penggunaan --}}
                <div>
                    <label for="rincian_penggunaan" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                        Rincian Penggunaan Dana <span class="text-red-500">*</span>
                    </label>
                    <textarea id="rincian_penggunaan" name="rincian_penggunaan" rows="5" required
                              class="w-full px-4 py-2.5 bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 rounded-xl text-sm focus:outline-none focus:border-[#1F4E79] dark:focus:border-blue-500 focus:ring-2 focus:ring-[#1F4E79]/20 text-gray-900 dark:text-white transition {{ $errors->has('rincian_penggunaan') ? 'border-red-400 bg-red-50 dark:bg-red-900/20' : '' }}">{{ old('rincian_penggunaan', $laporan->rincian_penggunaan) }}</textarea>
                    @error('rincian_penggunaan')
                        <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Upload Bukti --}}
                <div>
                    <label for="bukti_distribusi" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                        Ganti Bukti Distribusi <span class="text-gray-400 dark:text-gray-500 font-normal">(opsional — kosongkan jika tidak diubah)</span>
                    </label>

                    @if($laporan->bukti_distribusi)
                    <div class="mb-3 flex items-center gap-3 bg-gray-50 dark:bg-slate-700/50 rounded-xl p-3 border border-gray-200 dark:border-slate-600">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span class="text-sm text-gray-600 dark:text-gray-300 flex-1 truncate">Bukti saat ini tersimpan</span>
                        <a href="{{ asset($laporan->bukti_distribusi) }}" target="_blank" class="text-xs text-[#2E75B6] hover:underline font-medium">Lihat</a>
                    </div>
                    @endif

                    <input id="bukti_distribusi" name="bukti_distribusi" type="file" accept=".pdf,.jpg,.jpeg,.png"
                           class="w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 dark:file:bg-blue-900/30 file:text-[#1F4E79] dark:file:text-blue-400 hover:file:bg-blue-100 border border-gray-300 dark:border-slate-600 rounded-xl p-2 bg-gray-50 dark:bg-slate-700 transition {{ $errors->has('bukti_distribusi') ? 'border-red-400 bg-red-50 dark:bg-red-900/20' : '' }}">
                    <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-1">Format yang diizinkan: PDF, JPG, JPEG, PNG (Maks. 5MB).</p>
                    @error('bukti_distribusi')
                        <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Action buttons --}}
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-slate-700">
                    <a href="{{ route('admin.laporan-distribusi.index') }}"
                       class="px-5 py-2.5 border border-gray-300 dark:border-slate-600 hover:bg-gray-50 dark:hover:bg-slate-700 text-gray-700 dark:text-gray-300 font-semibold rounded-xl text-sm transition shadow-sm">
                        Batal
                    </a>
                    <button type="submit"
                            class="px-5 py-2.5 bg-[#1F4E79] dark:bg-blue-600 hover:bg-[#163859] dark:hover:bg-blue-700 text-white font-semibold rounded-xl text-sm transition shadow-sm">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <footer class="bg-white dark:bg-slate-900 border-t border-gray-200 dark:border-slate-800 py-6 text-center text-xs text-gray-400 dark:text-gray-500 mt-8 transition-colors duration-300">
        <p>&copy; 2026 KitaTanggap Kelompok 11 RPL. All rights reserved.</p>
    </footer>
</div>
@endsection
