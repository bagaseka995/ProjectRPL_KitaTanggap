@extends('layouts.auth')
@section('title', 'Dashboard Relawan')
@section('content')
<div class="min-h-screen bg-gray-50 flex flex-col justify-between">
    {{-- Navbar --}}
    <nav class="bg-[#1F4E79] text-white shadow">
        <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('dashboard') }}" class="font-bold text-lg hover:opacity-90 transition">
                    KitaTanggap — Relawan
                </a>
                <div class="hidden md:flex gap-3 text-sm">
                    <a href="{{ route('dashboard') }}" class="px-3 py-1 rounded bg-[#2E75B6] font-medium transition">Beranda</a>
                    <a href="{{ route('relawan.profil') }}" class="px-3 py-1 rounded hover:bg-[#2E75B6]/50 transition">Profil Saya</a>
                    <a href="{{ route('relawan.riwayat.index') }}" class="px-3 py-1 rounded hover:bg-[#2E75B6]/50 transition">Riwayat Misi</a>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('pengaturan.notifikasi') }}" class="text-sm opacity-75 hover:opacity-100 transition hidden md:inline-block">Pengaturan</a>
                <form method="POST" action="{{ route('logout') }}">@csrf
                    <button class="text-sm opacity-75 hover:opacity-100 transition">Keluar</button>
                </form>
            </div>
        </div>
    </nav>

    {{-- Main Container --}}
    <div class="max-w-5xl w-full mx-auto px-4 py-12 flex-grow flex flex-col justify-center items-center">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Portal Relawan KitaTanggap</h1>
            <p class="text-gray-500 mt-2">Selamat datang kembali, <strong>{{ auth()->user()->nama_lengkap }}</strong>. Kelola keanggotaan dan riwayat misi Anda.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full max-w-3xl">
            {{-- Card 1: Profil Relawan Saya --}}
            <a href="{{ route('relawan.profil') }}" class="group bg-white p-8 rounded-2xl border border-gray-200 shadow-sm hover:shadow-md hover:border-[#2E75B6] transition duration-200 text-left flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-[#1F4E79] flex items-center justify-center mb-5 group-hover:bg-[#1F4E79] group-hover:text-white transition duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-950 group-hover:text-[#1F4E79] transition">Profil Relawan Saya</h2>
                    <p class="text-sm text-gray-500 mt-2 leading-relaxed">Lengkapi keahlian, pengalaman penanganan bencana, domisili, dan atur ketersediaan status tugas Anda.</p>
                </div>
                <div class="mt-6 text-sm font-semibold text-[#1F4E79] flex items-center gap-1">
                    Buka Profil Saya →
                </div>
            </a>

            {{-- Card 2: Riwayat Misi Saya --}}
            <a href="{{ route('relawan.riwayat.index') }}" class="group bg-white p-8 rounded-2xl border border-gray-200 shadow-sm hover:shadow-md hover:border-[#2E75B6] transition duration-200 text-left flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-green-50 text-[#22C55E] flex items-center justify-center mb-5 group-hover:bg-[#22C55E] group-hover:text-white transition duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-950 group-hover:text-[#22C55E] transition">Riwayat Misi Saya</h2>
                    <p class="text-sm text-gray-500 mt-2 leading-relaxed">Lihat misi penanganan bencana yang pernah Anda ikuti, status penugasan terkini, dan unduh sertifikat penghargaan digital.</p>
                </div>
                <div class="mt-6 text-sm font-semibold text-[#22C55E] flex items-center gap-1">
                    Buka Riwayat Misi →
                </div>
            </a>
        </div>
    </div>

    {{-- Footer --}}
    <footer class="bg-white border-t border-gray-200 py-6 text-center text-xs text-gray-400">
        <p>&copy; 2026 KitaTanggap Kelompok 11 RPL. All rights reserved.</p>
    </footer>
</div>
@endsection
