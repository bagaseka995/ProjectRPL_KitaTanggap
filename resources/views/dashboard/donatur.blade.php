@extends('layouts.auth')
@section('title', 'Dashboard Donatur')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-slate-900 flex flex-col justify-between transition-colors duration-300">
    @include('layouts.partials.navbar-main')
    @include('layouts.partials.navbar-sub')

    <div class="max-w-5xl w-full mx-auto px-4 py-12 flex-grow flex flex-col justify-start">

        {{-- Welcome Header --}}
        <div class="bg-gradient-to-r from-[#1F4E79] to-[#2E75B6] dark:from-slate-800 dark:to-slate-700 rounded-3xl p-8 text-white shadow-md mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight">Portal Donatur KitaTanggap</h1>
                <p class="mt-2 text-blue-100 max-w-xl">
                    Selamat datang kembali, <strong>{{ auth()->user()->nama_lengkap }}</strong>! Terima kasih atas segala kebaikan dan donasi yang telah Anda salurkan untuk penanganan bencana.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center backdrop-blur-md">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Statistics Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-10">
            {{-- Stat 1: Total Donasi --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 shadow-sm p-6 flex items-center gap-4 transition-colors">
                <div class="w-12 h-12 bg-green-50 text-green-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">Total Donasi Sukses</p>
                    <p class="text-xl font-black text-gray-900 dark:text-white mt-1">
                        Rp {{ number_format($totalDonation, 0, ',', '.') }}
                    </p>
                </div>
            </div>

            {{-- Stat 2: Total Transaksi --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 shadow-sm p-6 flex items-center gap-4 transition-colors">
                <div class="w-12 h-12 bg-blue-50 text-[#1F4E79] rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">Total Transaksi</p>
                    <p class="text-xl font-black text-gray-900 dark:text-white mt-1">
                        {{ $totalTransactions }} Kali
                    </p>
                </div>
            </div>

            {{-- Stat 3: Bencana Aktif --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 shadow-sm p-6 flex items-center gap-4 transition-colors">
                <div class="w-12 h-12 bg-red-50 text-red-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">Bencana Aktif</p>
                    <p class="text-xl font-black text-gray-900 dark:text-white mt-1">
                        {{ $activeDisasters }} Bencana
                    </p>
                </div>
            </div>
        </div>

        {{-- Action Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-3xl mx-auto w-full">
            {{-- Card 1: Riwayat Donasi --}}
            <a href="{{ route('donatur.riwayat') }}" class="group bg-white dark:bg-slate-800 p-8 rounded-3xl border border-gray-200 dark:border-slate-700 shadow-sm hover:shadow-md hover:border-[#2E75B6] dark:hover:border-blue-500 transition duration-200 text-left flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 text-[#1F4E79] flex items-center justify-center mb-6 group-hover:bg-[#1F4E79] group-hover:text-white transition duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-950 dark:text-white group-hover:text-[#1F4E79] dark:group-hover:text-blue-400 transition">Riwayat Donasi Saya</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2 leading-relaxed">
                        Lihat daftar semua riwayat donasi Anda, baik yang berstatus sukses, pending, maupun gagal. Anda juga bisa menyaring berdasarkan rentang tanggal tertentu.
                    </p>
                </div>
                <div class="mt-8 text-sm font-semibold text-[#1F4E79] dark:text-blue-400 flex items-center gap-1">
                    Buka Riwayat Donasi →
                </div>
            </a>

            {{-- Card 2: Salurkan Donasi Baru --}}
            <a href="{{ route('transparansi') }}" class="group bg-white dark:bg-slate-800 p-8 rounded-3xl border border-gray-200 dark:border-slate-700 shadow-sm hover:shadow-md hover:border-[#2E75B6] dark:hover:border-blue-500 transition duration-200 text-left flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-2xl bg-green-50 text-green-600 flex items-center justify-center mb-6 group-hover:bg-green-600 group-hover:text-white transition duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-950 dark:text-white group-hover:text-green-600 dark:group-hover:text-green-400 transition">Salurkan Donasi Baru</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2 leading-relaxed">
                        Cari bencana aktif di daerah sekitar atau seluruh Indonesia dan kirimkan donasi untuk meringankan beban para korban bencana alam secara transparan.
                    </p>
                </div>
                <div class="mt-8 text-sm font-semibold text-green-600 dark:text-green-400 flex items-center gap-1">
                    Donasi Sekarang →
                </div>
            </a>
        </div>
    </div>

    {{-- Footer --}}
    <footer class="bg-white dark:bg-slate-900 border-t border-gray-200 dark:border-slate-800 py-6 text-center text-xs text-gray-400 dark:text-gray-500 transition-colors duration-300">
        <p>&copy; 2026 KitaTanggap Kelompok 11 RPL. All rights reserved.</p>
    </footer>
</div>
@endsection
