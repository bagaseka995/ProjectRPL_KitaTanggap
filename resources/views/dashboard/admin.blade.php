@extends('layouts.auth')
@section('title', 'Dashboard Admin')
@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-slate-900 flex flex-col justify-between transition-colors duration-300">
    @include('layouts.partials.navbar-main')
    @include('layouts.partials.navbar-sub')

    {{-- Main Container --}}
    <div class="max-w-5xl w-full mx-auto px-4 py-8 flex-grow flex flex-col justify-start">
        
        {{-- Welcome Header --}}
        <div class="bg-gradient-to-r from-[#1F4E79] to-[#2E75B6] dark:from-slate-800 dark:to-slate-700 rounded-3xl p-8 text-white shadow-md mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-6 animate-fade-in stagger-1">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight">Portal Administrasi</h1>
                <p class="mt-2 text-blue-100 max-w-xl text-sm leading-relaxed">
                    Selamat datang kembali, <strong class="text-white">{{ auth()->user()->nama_lengkap }}</strong>. Kelola relawan, tugaskan personel ke lokasi bencana, dan laporkan transparansi distribusi bantuan.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-14 h-14 rounded-2xl bg-white/10 flex items-center justify-center backdrop-blur-md shadow-inner">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 w-full animate-fade-in stagger-2">
            {{-- Card 1: Manajemen Relawan --}}
            <a href="{{ route('admin.relawan.index') }}" class="group bg-white dark:bg-slate-800 p-8 rounded-3xl border border-gray-100 dark:border-slate-700 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] hover:shadow-[0_8px_30px_-4px_rgba(31,78,121,0.15)] dark:shadow-none dark:hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.5)] hover:border-[#2E75B6]/30 dark:hover:border-slate-600 transition-all duration-300 text-left flex flex-col justify-between hover:-translate-y-1">
                <div>
                    <div class="w-14 h-14 rounded-2xl bg-blue-50 text-[#1F4E79] flex items-center justify-center mb-6 group-hover:bg-gradient-to-br group-hover:from-[#1F4E79] group-hover:to-[#2E75B6] group-hover:text-white transition-all duration-300 shadow-sm">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white group-hover:text-[#1F4E79] dark:group-hover:text-blue-400 transition-colors">Manajemen Relawan</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2.5 leading-relaxed">Verifikasi atau tolak pendaftaran relawan baru, dan pantau daftar profil keahlian serta domisili relawan aktif.</p>
                </div>
                <div class="mt-8 text-sm font-semibold text-[#1F4E79] dark:text-blue-400 flex items-center gap-1 group-hover:gap-2 transition-all">
                    Buka Dashboard <span aria-hidden="true">&rarr;</span>
                </div>
            </a>

            {{-- Card 2: Penugasan Relawan --}}
            <a href="{{ route('admin.penugasan.index') }}" class="group bg-white dark:bg-slate-800 p-8 rounded-3xl border border-gray-100 dark:border-slate-700 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] hover:shadow-[0_8px_30px_-4px_rgba(197,90,17,0.15)] dark:shadow-none dark:hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.5)] hover:border-[#C55A11]/30 dark:hover:border-slate-600 transition-all duration-300 text-left flex flex-col justify-between hover:-translate-y-1">
                <div>
                    <div class="w-14 h-14 rounded-2xl bg-orange-50 text-[#C55A11] flex items-center justify-center mb-6 group-hover:bg-gradient-to-br group-hover:from-[#C55A11] group-hover:to-orange-500 group-hover:text-white transition-all duration-300 shadow-sm">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white group-hover:text-[#C55A11] dark:group-hover:text-orange-400 transition-colors">Penugasan Relawan</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2.5 leading-relaxed">Tugaskan relawan terverifikasi ke bencana aktif, pantau misi berjalan, dan selesaikan misi untuk menerbitkan sertifikat.</p>
                </div>
                <div class="mt-8 text-sm font-semibold text-[#C55A11] dark:text-orange-400 flex items-center gap-1 group-hover:gap-2 transition-all">
                    Kelola Penugasan <span aria-hidden="true">&rarr;</span>
                </div>
            </a>

            {{-- Card 3: Laporan Distribusi --}}
            <a href="{{ route('admin.laporan-distribusi.index') }}" class="group bg-white dark:bg-slate-800 p-8 rounded-3xl border border-gray-100 dark:border-slate-700 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] hover:shadow-[0_8px_30px_-4px_rgba(34,197,94,0.15)] dark:shadow-none dark:hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.5)] hover:border-green-500/30 dark:hover:border-slate-600 transition-all duration-300 text-left flex flex-col justify-between hover:-translate-y-1">
                <div>
                    <div class="w-14 h-14 rounded-2xl bg-green-50 text-green-600 flex items-center justify-center mb-6 group-hover:bg-gradient-to-br group-hover:from-green-500 group-hover:to-emerald-500 group-hover:text-white transition-all duration-300 shadow-sm">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white group-hover:text-green-600 dark:group-hover:text-green-400 transition-colors">Laporan Distribusi</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2.5 leading-relaxed">Catat penggunaan dana donasi, rincian logistik, dan unggah bukti penyaluran bantuan ke halaman transparansi publik.</p>
                </div>
                <div class="mt-8 text-sm font-semibold text-green-600 dark:text-green-400 flex items-center gap-1 group-hover:gap-2 transition-all">
                    Buat Laporan <span aria-hidden="true">&rarr;</span>
                </div>
            </a>

            {{-- Card 4: Manajemen Bencana --}}
            <a href="{{ route('admin.bencana.index') }}" class="group bg-white dark:bg-slate-800 p-8 rounded-3xl border border-gray-100 dark:border-slate-700 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] hover:shadow-[0_8px_30px_-4px_rgba(239,68,68,0.15)] dark:shadow-none dark:hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.5)] hover:border-red-500/30 dark:hover:border-slate-600 transition-all duration-300 text-left flex flex-col justify-between hover:-translate-y-1">
                <div>
                    <div class="w-14 h-14 rounded-2xl bg-red-50 text-red-600 flex items-center justify-center mb-6 group-hover:bg-gradient-to-br group-hover:from-red-500 group-hover:to-rose-500 group-hover:text-white transition-all duration-300 shadow-sm">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors">Manajemen Bencana</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2.5 leading-relaxed">Tambah, edit, dan kelola data bencana aktif. Atur status siaga, target donasi, dan kirim notifikasi ke warga terdampak.</p>
                </div>
                <div class="mt-8 text-sm font-semibold text-red-600 dark:text-red-400 flex items-center gap-1 group-hover:gap-2 transition-all">
                    Kelola Bencana <span aria-hidden="true">&rarr;</span>
                </div>
            </a>

            {{-- Card 5: Verifikasi Pengguna --}}
            <a href="{{ route('admin.users.index') }}" class="group bg-white dark:bg-slate-800 p-8 rounded-3xl border border-gray-100 dark:border-slate-700 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] hover:shadow-[0_8px_30px_-4px_rgba(112,48,160,0.15)] dark:shadow-none dark:hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.5)] hover:border-[#7030A0]/30 dark:hover:border-slate-600 transition-all duration-300 text-left flex flex-col justify-between hover:-translate-y-1">
                <div>
                    <div class="w-14 h-14 rounded-2xl bg-purple-50 text-[#7030A0] flex items-center justify-center mb-6 group-hover:bg-gradient-to-br group-hover:from-[#7030A0] group-hover:to-purple-500 group-hover:text-white transition-all duration-300 shadow-sm">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white group-hover:text-[#7030A0] dark:group-hover:text-purple-400 transition-colors">Verifikasi Pengguna</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2.5 leading-relaxed">Setujui (ACC) pendaftaran akun relawan dan donatur baru agar mereka dapat login dan beraktivitas.</p>
                </div>
                <div class="mt-8 text-sm font-semibold text-[#7030A0] dark:text-purple-400 flex items-center gap-1 group-hover:gap-2 transition-all">
                    Verifikasi Akun <span aria-hidden="true">&rarr;</span>
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
