@extends('layouts.auth')
@section('title', 'Verifikasi Sertifikat Digital — KitaTanggap')
@section('content')
<div class="min-h-screen bg-gray-50 flex flex-col justify-between">
    {{-- Header --}}
    <nav class="bg-[#1F4E79] text-white shadow">
        <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
            <span class="font-bold text-lg">KitaTanggap</span>
            <div class="flex gap-4 text-sm font-medium">
                <a href="/" class="hover:text-gray-200 transition">Beranda</a>
                <a href="{{ route('peta') }}" class="hover:text-gray-200 transition">Peta Bencana</a>
            </div>
        </div>
    </nav>

    {{-- Main content area --}}
    <div class="max-w-2xl w-full mx-auto px-4 py-12 flex-grow flex items-center justify-center">
        <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-8 w-full max-w-lg text-center animate-fade-up">
            @if($sertifikat)
                {{-- Success / Verified --}}
                <div class="mb-6 flex justify-center">
                    <span class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-50 text-green-600 ring-8 ring-green-100/50">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </span>
                </div>

                <div class="inline-block px-4 py-1.5 bg-green-100 border border-green-300 text-green-800 rounded-full text-xs font-bold uppercase tracking-wider mb-6">
                    Sertifikat Valid ✓
                </div>

                <h2 class="text-xl font-bold text-gray-800 mb-1">Verifikasi Keaslian Berhasil</h2>
                <p class="text-xs text-gray-400 mb-6 font-mono">Kode: {{ $kode }}</p>

                {{-- Detail Table --}}
                <div class="border border-gray-100 rounded-xl bg-gray-50 p-5 text-left space-y-4 text-sm mb-6">
                    <div>
                        <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Nama Relawan</p>
                        <p class="font-bold text-gray-800 mt-0.5">{{ $sertifikat->penugasan->relawan->user->nama_lengkap }}</p>
                    </div>
                    
                    <div class="border-t border-gray-200/60 pt-3">
                        <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Misi Penugasan Bencana</p>
                        <p class="font-semibold text-gray-800 mt-0.5">{{ $sertifikat->penugasan->bencana->nama_bencana }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $sertifikat->penugasan->bencana->lokasi }}</p>
                    </div>

                    <div class="border-t border-gray-200/60 pt-3">
                        <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Tanggal Terbit</p>
                        <p class="text-gray-700 mt-0.5">{{ $sertifikat->tanggal_terbit->format('d-m-Y') }}</p>
                    </div>
                </div>

                <a href="{{ route('sertifikat.unduh', $sertifikat->kode_sertifikat) }}" target="_blank"
                   class="inline-flex items-center justify-center gap-2 w-full py-3 bg-[#1F4E79] hover:bg-[#1F4E79]/90 text-white rounded-xl text-sm font-bold shadow transition hover:shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Lihat / Unduh Dokumen PDF
                </a>
            @else
                {{-- Failed / Invalid --}}
                <div class="mb-6 flex justify-center">
                    <span class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-50 text-red-600 ring-8 ring-red-100/50">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </span>
                </div>

                <div class="inline-block px-4 py-1.5 bg-red-100 border border-red-300 text-red-800 rounded-full text-xs font-bold uppercase tracking-wider mb-6">
                    Sertifikat Tidak Valid ✗
                </div>

                <h2 class="text-xl font-bold text-gray-800 mb-2">Verifikasi Gagal</h2>
                <p class="text-sm text-gray-500 mb-6">Kode sertifikat <code class="font-mono bg-gray-100 px-2 py-1 rounded text-red-600 font-semibold text-xs">{{ $kode }}</code> tidak terdaftar di sistem KitaTanggap.</p>

                <div class="border border-gray-100 rounded-xl bg-red-50/50 p-4 text-xs text-red-700 text-left mb-6 leading-relaxed">
                    Mohon pastikan kembali kode verifikasi yang Anda masukkan sudah benar. Sertifikat resmi KitaTanggap selalu terdaftar secara otomatis saat misi diselesaikan oleh tim admin.
                </div>

                <a href="{{ route('home') }}"
                   class="inline-block w-full py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-semibold transition border">
                    Kembali ke Beranda
                </a>
            @endif
        </div>
    </div>

    {{-- Footer --}}
    <footer class="bg-white border-t border-gray-200 py-6 text-center text-xs text-gray-400">
        <p>&copy; 2026 KitaTanggap Kelompok 11 RPL. All rights reserved.</p>
    </footer>
</div>
@endsection
