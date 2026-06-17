@extends('layouts.auth')
@section('title', 'Riwayat Donasi Saya')

@section('content')
<div class="min-h-screen bg-gray-50 flex flex-col justify-between">
    {{-- Navbar --}}
    <nav class="bg-[#1F4E79] text-white shadow">
        <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('donatur.dashboard') }}" class="font-bold text-lg hover:opacity-90 transition">
                    KitaTanggap — Donatur
                </a>
                <div class="hidden md:flex gap-3 text-sm">
                    <a href="{{ route('donatur.dashboard') }}" class="px-3 py-1 rounded hover:bg-[#2E75B6]/50 transition">
                        Beranda
                    </a>
                    <a href="{{ route('donatur.riwayat') }}" class="px-3 py-1 rounded bg-[#2E75B6] font-medium transition">
                        Riwayat Donasi
                    </a>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm opacity-75 hover:opacity-100 transition">Keluar</button>
            </form>
        </div>
    </nav>

    {{-- Main Container --}}
    <div class="max-w-6xl w-full mx-auto px-4 py-8 flex-grow">
        {{-- Navigation for Mobile --}}
        <div class="flex md:hidden gap-2 mb-6 text-sm">
            <a href="{{ route('donatur.dashboard') }}" class="flex-1 text-center py-2 rounded-lg bg-white border text-gray-700 hover:bg-gray-50">
                Beranda
            </a>
            <a href="{{ route('donatur.riwayat') }}" class="flex-1 text-center py-2 rounded-lg bg-[#1F4E79] text-white font-medium shadow-sm">
                Riwayat Donasi
            </a>
        </div>

        <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold text-gray-950 tracking-tight">Riwayat Donasi Saya</h1>
                <p class="text-sm text-gray-500 mt-1">Lacak dan lihat semua donasi yang telah Anda berikan untuk membantu sesama.</p>
            </div>
            <a href="{{ route('transparansi') }}" class="inline-flex items-center justify-center px-4 py-2 bg-[#2E75B6] hover:bg-[#163859] text-white text-sm font-semibold rounded-xl transition shadow-sm gap-1.5 self-start sm:self-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Donasi Baru
            </a>
        </div>

        {{-- Filter Card --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 mb-6">
            <form method="GET" action="{{ route('donatur.riwayat') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                {{-- Status Filter --}}
                <div>
                    <label for="status_bayar" class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">
                        Status Pembayaran
                    </label>
                    <select id="status_bayar" name="status_bayar" class="w-full text-sm border-gray-200 rounded-xl focus:border-[#1F4E79] focus:ring focus:ring-[#1F4E79]/20 transition p-2.5 bg-gray-50 border">
                        <option value="">Semua Status</option>
                        <option value="sukses" {{ request('status_bayar') === 'sukses' ? 'selected' : '' }}>Sukses</option>
                        <option value="pending" {{ request('status_bayar') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="gagal" {{ request('status_bayar') === 'gagal' ? 'selected' : '' }}>Gagal</option>
                    </select>
                </div>

                {{-- Start Date Filter --}}
                <div>
                    <label for="tanggal_mulai" class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">
                        Dari Tanggal
                    </label>
                    <input type="date" id="tanggal_mulai" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}" class="w-full text-sm border-gray-200 rounded-xl focus:border-[#1F4E79] focus:ring focus:ring-[#1F4E79]/20 transition p-2.5 bg-gray-50 border">
                </div>

                {{-- End Date Filter --}}
                <div>
                    <label for="tanggal_selesai" class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">
                        Sampai Tanggal
                    </label>
                    <input type="date" id="tanggal_selesai" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}" class="w-full text-sm border-gray-200 rounded-xl focus:border-[#1F4E79] focus:ring focus:ring-[#1F4E79]/20 transition p-2.5 bg-gray-50 border">
                </div>

                {{-- Action Buttons --}}
                <div class="flex gap-2">
                    <button type="submit" class="flex-grow justify-center inline-flex items-center px-4 py-2.5 bg-[#1F4E79] hover:bg-[#163859] text-white text-sm font-semibold rounded-xl transition shadow-sm gap-1.5 h-[42px]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 8.293A1 1 0 013 7.586V4z"/>
                        </svg>
                        Filter
                    </button>
                    @if(request()->filled('status_bayar') || request()->filled('tanggal_mulai') || request()->filled('tanggal_selesai'))
                        <a href="{{ route('donatur.riwayat') }}" class="inline-flex items-center justify-center p-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl transition shadow-sm h-[42px] w-[42px]" title="Reset Filter">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 8H18.5"/>
                            </svg>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Donations Table / Cards List --}}
        @if($donations->isEmpty())
            <div class="text-center py-16 bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <div class="w-16 h-16 bg-blue-50 text-blue-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="font-bold text-gray-800 text-lg">Tidak Ada Data Donasi</h3>
                <p class="text-sm text-gray-400 mt-1 max-w-md mx-auto">
                    @if(request()->anyFilled(['status_bayar', 'tanggal_mulai', 'tanggal_selesai']))
                        Tidak ada riwayat donasi yang cocok dengan kriteria filter Anda. Coba atur ulang filter.
                    @else
                        Anda belum pernah melakukan donasi sebagai akun terdaftar. Mulai donasi sekarang untuk membantu penanganan bencana.
                    @endif
                </p>
                <div class="mt-6 flex justify-center gap-3">
                    @if(request()->anyFilled(['status_bayar', 'tanggal_mulai', 'tanggal_selesai']))
                        <a href="{{ route('donatur.riwayat') }}" class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition shadow-sm border border-gray-200">
                            Clear Filters
                        </a>
                    @endif
                    <a href="{{ route('transparansi') }}" class="px-5 py-2 bg-[#1F4E79] hover:bg-[#163859] text-white text-sm font-semibold rounded-xl transition shadow-sm">
                        Donasi Sekarang
                    </a>
                </div>
            </div>
        @else
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden mb-6">
                {{-- Desktop View Table --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100 text-xs font-bold text-gray-400 uppercase tracking-wider">
                                <th class="p-4 pl-6">Kode Transaksi</th>
                                <th class="p-4">Bencana</th>
                                <th class="p-4 text-right">Nominal</th>
                                <th class="p-4">Metode Bayar</th>
                                <th class="p-4">Status</th>
                                <th class="p-4 pr-6">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                            @foreach($donations as $donation)
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="p-4 pl-6 font-mono font-semibold text-gray-900 text-xs">
                                        {{ $donation->kode_transaksi ?? 'N/A' }}
                                    </td>
                                    <td class="p-4">
                                        <div class="font-bold text-gray-900 leading-tight">
                                            {{ $donation->bencana->nama_bencana }}
                                        </div>
                                        <div class="text-xs text-gray-400 mt-0.5">
                                            {{ $donation->bencana->lokasi }}
                                        </div>
                                    </td>
                                    <td class="p-4 text-right font-extrabold text-gray-900">
                                        {{ $donation->nominal_formatted }}
                                    </td>
                                    <td class="p-4 text-xs font-medium text-gray-600">
                                        {{ $donation->label_metode }}
                                    </td>
                                    <td class="p-4">
                                        @if($donation->status_bayar === 'sukses')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-50 text-green-700 border border-green-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span>
                                                Sukses
                                            </span>
                                        @elseif($donation->status_bayar === 'pending')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-yellow-50 text-yellow-700 border border-yellow-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 mr-1.5 animate-pulse"></span>
                                                Pending
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-50 text-red-700 border border-red-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-1.5"></span>
                                                Gagal
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-4 pr-6 text-xs text-gray-400 whitespace-nowrap">
                                        {{ $donation->created_at->format('d M Y H:i') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Mobile View Cards --}}
                <div class="md:hidden divide-y divide-gray-100">
                    @foreach($donations as $donation)
                        <div class="p-5 hover:bg-gray-50/50 transition">
                            <div class="flex items-center justify-between gap-3 mb-2">
                                <span class="font-mono text-xs font-bold text-gray-500">
                                    #{{ $donation->kode_transaksi ?? 'N/A' }}
                                </span>
                                @if($donation->status_bayar === 'sukses')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-50 text-green-700 border border-green-200">
                                        Sukses
                                    </span>
                                @elseif($donation->status_bayar === 'pending')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-yellow-50 text-yellow-700 border border-yellow-200">
                                        Pending
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-50 text-red-700 border border-red-200">
                                        Gagal
                                    </span>
                                @endif
                            </div>
                            <h3 class="text-sm font-bold text-gray-900 leading-tight">
                                {{ $donation->bencana->nama_bencana }}
                            </h3>
                            <div class="text-[11px] text-gray-400 mt-0.5 mb-3">
                                {{ $donation->bencana->lokasi }}
                            </div>
                            <div class="flex items-center justify-between border-t border-dashed border-gray-100 pt-3 text-xs">
                                <div>
                                    <p class="text-[10px] text-gray-400 font-semibold uppercase">Nominal</p>
                                    <p class="font-extrabold text-gray-900 mt-0.5">{{ $donation->nominal_formatted }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-[10px] text-gray-400 font-semibold uppercase">Tanggal</p>
                                    <p class="text-gray-600 mt-0.5 font-medium">{{ $donation->created_at->format('d M Y') }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Pagination Container --}}
            <div class="mt-4 px-2">
                {{ $donations->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

    {{-- Footer --}}
    <footer class="bg-white border-t border-gray-200 py-6 text-center text-xs text-gray-400 mt-8">
        <p>&copy; 2026 KitaTanggap Kelompok 11 RPL. All rights reserved.</p>
    </footer>
</div>
@endsection
