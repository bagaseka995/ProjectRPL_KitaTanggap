@extends('layouts.auth')
@section('title', 'Dashboard Relawan')
@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-slate-900 flex flex-col justify-between transition-colors duration-300">
    @include('layouts.partials.navbar-main')
    @include('layouts.partials.navbar-sub')

    {{-- Main Container --}}
    <div class="max-w-4xl w-full mx-auto px-4 py-8 flex-grow flex flex-col justify-start">
        
        {{-- Welcome Header --}}
        <div class="bg-gradient-to-r from-[#1F4E79] to-[#2E75B6] dark:from-slate-800 dark:to-slate-700 rounded-3xl p-8 text-white shadow-md mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-6 animate-fade-in stagger-1">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight">Portal Relawan</h1>
                <p class="mt-2 text-blue-100 max-w-xl text-sm leading-relaxed">
                    Selamat datang kembali, <strong class="text-white">{{ auth()->user()->nama_lengkap }}</strong>. Kelola profil keahlian Anda dan pantau riwayat penugasan misi penyelamatan.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-14 h-14 rounded-2xl bg-white/10 flex items-center justify-center backdrop-blur-md shadow-inner">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full animate-fade-in stagger-2">
            {{-- Card 1: Profil Relawan Saya --}}
            <a href="{{ route('relawan.profil') }}" class="group bg-white dark:bg-slate-800 p-8 rounded-3xl border border-gray-100 dark:border-slate-700 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] hover:shadow-[0_8px_30px_-4px_rgba(31,78,121,0.15)] dark:shadow-none dark:hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.5)] hover:border-[#2E75B6]/30 dark:hover:border-slate-600 transition-all duration-300 text-left flex flex-col justify-between hover:-translate-y-1">
                <div>
                    <div class="w-14 h-14 rounded-2xl bg-blue-50 text-[#1F4E79] flex items-center justify-center mb-6 group-hover:bg-gradient-to-br group-hover:from-[#1F4E79] group-hover:to-[#2E75B6] group-hover:text-white transition-all duration-300 shadow-sm">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white group-hover:text-[#1F4E79] dark:group-hover:text-blue-400 transition-colors">Profil Relawan Saya</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2.5 leading-relaxed">Lengkapi keahlian, pengalaman penanganan bencana, domisili, dan atur ketersediaan status tugas Anda.</p>
                </div>
                <div class="mt-8 text-sm font-semibold text-[#1F4E79] dark:text-blue-400 flex items-center gap-1 group-hover:gap-2 transition-all">
                    Buka Profil Saya <span aria-hidden="true">&rarr;</span>
                </div>
            </a>

            {{-- Card 2: Riwayat Misi Saya --}}
            <a href="{{ route('relawan.riwayat.index') }}" class="group bg-white dark:bg-slate-800 p-8 rounded-3xl border border-gray-100 dark:border-slate-700 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] hover:shadow-[0_8px_30px_-4px_rgba(34,197,94,0.15)] dark:shadow-none dark:hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.5)] hover:border-green-500/30 dark:hover:border-slate-600 transition-all duration-300 text-left flex flex-col justify-between hover:-translate-y-1">
                <div>
                    <div class="w-14 h-14 rounded-2xl bg-green-50 text-green-600 flex items-center justify-center mb-6 group-hover:bg-gradient-to-br group-hover:from-green-500 group-hover:to-emerald-500 group-hover:text-white transition-all duration-300 shadow-sm">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white group-hover:text-green-600 dark:group-hover:text-green-400 transition-colors">Riwayat Misi Saya</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2.5 leading-relaxed">Lihat misi penanganan bencana yang pernah Anda ikuti, status penugasan terkini, dan unduh sertifikat penghargaan digital.</p>
                </div>
                <div class="mt-8 text-sm font-semibold text-green-600 dark:text-green-400 flex items-center gap-1 group-hover:gap-2 transition-all">
                    Buka Riwayat Misi <span aria-hidden="true">&rarr;</span>
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
