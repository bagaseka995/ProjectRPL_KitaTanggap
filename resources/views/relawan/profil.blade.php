@extends('layouts.auth')
@section('title', 'Profil Relawan')
@section('content')
<div class="min-h-screen bg-gray-50">
    {{-- Navbar --}}
    <nav class="bg-[#1F4E79] text-white shadow">
        <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('dashboard') }}" class="font-bold text-lg hover:opacity-90 transition">
                    KitaTanggap — Relawan
                </a>
                <div class="hidden md:flex gap-3 text-sm">
                    <a href="{{ route('dashboard') }}" class="px-3 py-1 rounded hover:bg-[#2E75B6]/50 transition">Beranda</a>
                    <a href="{{ route('relawan.profil') }}" class="px-3 py-1 rounded bg-[#2E75B6] font-medium transition">Profil Saya</a>
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

    {{-- Main Content --}}
    <div class="max-w-3xl mx-auto px-4 py-8">
        {{-- Navigation for Mobile --}}
        <div class="flex md:hidden gap-2 mb-6 text-sm">
            <a href="{{ route('dashboard') }}" class="flex-1 text-center py-2 rounded-lg bg-white border text-gray-700 hover:bg-gray-50">Beranda</a>
            <a href="{{ route('relawan.profil') }}" class="flex-1 text-center py-2 rounded-lg bg-[#1F4E79] text-white font-medium shadow-sm">Profil</a>
            <a href="{{ route('relawan.riwayat.index') }}" class="flex-1 text-center py-2 rounded-lg bg-white border text-gray-700 hover:bg-gray-50">Misi</a>
        </div>

        {{-- Welcome header --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-gray-900">Halo, {{ auth()->user()->nama_lengkap }}</h1>
                <p class="text-sm text-gray-500 mt-1">Lengkapi data diri Anda untuk dapat ditugaskan sebagai relawan kebencanaan.</p>
            </div>
            
            {{-- Status Badge --}}
            <div class="text-left md:text-right">
                <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Status Verifikasi</p>
                <div class="mt-1">
                    @if(!$relawan)
                        <span class="px-3 py-1 bg-gray-100 border border-gray-300 text-gray-700 rounded-full text-xs font-bold uppercase">Belum Terdaftar</span>
                    @elseif($relawan->status_verifikasi === 'pending')
                        <span class="px-3 py-1 bg-yellow-100 border border-yellow-300 text-yellow-800 rounded-full text-xs font-bold uppercase">Menunggu Verifikasi</span>
                    @elseif($relawan->status_verifikasi === 'terverifikasi')
                        <span class="px-3 py-1 bg-green-100 border border-green-300 text-green-800 rounded-full text-xs font-bold uppercase">Terverifikasi</span>
                    @elseif($relawan->status_verifikasi === 'ditolak')
                        <span class="px-3 py-1 bg-red-100 border border-red-300 text-red-800 rounded-full text-xs font-bold uppercase">Ditolak</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Alerts --}}
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-r-xl text-green-700 text-sm flex items-start shadow-sm">
                <svg class="w-5 h-5 mr-3 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->has('umum'))
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-xl text-red-700 text-sm flex items-start shadow-sm">
                <svg class="w-5 h-5 mr-3 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                <span>{{ $errors->first('umum') }}</span>
            </div>
        @endif

        {{-- Profile Form Card --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden" x-data="{
            ketersediaan: {{ old('ketersediaan', $relawan ? $relawan->ketersediaan : true) ? 'true' : 'false' }}
        }">
            <div class="border-b border-gray-100 bg-gray-50 px-6 py-4">
                <h2 class="font-bold text-gray-800 text-lg">Data Profil Relawan</h2>
            </div>
            
            <form action="{{ $relawan ? route('relawan.profil.update') : route('relawan.profil.store') }}" method="POST" class="p-6 space-y-6">
                @csrf
                @if($relawan)
                    @method('PUT')
                @endif

                {{-- Keahlian --}}
                <div>
                    <label for="keahlian" class="block text-sm font-semibold text-gray-700 mb-2">Keahlian <span class="text-red-500">*</span></label>
                    <input type="text" name="keahlian" id="keahlian" 
                           value="{{ old('keahlian', $relawan ? $relawan->keahlian : '') }}" 
                           placeholder="Contoh: Medis, SAR, Logistik, Dapur Umum, Trauma Healing"
                           class="w-full px-4 py-2.5 rounded-xl border @error('keahlian') border-red-300 @else border-gray-300 @enderror focus:outline-none focus:ring-2 focus:ring-[#1F4E79]/20 focus:border-[#1F4E79] transition text-sm">
                    <p class="text-xs text-gray-400 mt-1">Pisahkan keahlian Anda menggunakan tanda koma (,).</p>
                    @error('keahlian')
                        <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Lokasi Domisili --}}
                <div>
                    <label for="lokasi_domisili" class="block text-sm font-semibold text-gray-700 mb-2">Lokasi Domisili <span class="text-red-500">*</span></label>
                    <input type="text" name="lokasi_domisili" id="lokasi_domisili" 
                           value="{{ old('lokasi_domisili', $relawan ? $relawan->lokasi_domisili : '') }}" 
                           placeholder="Contoh: Purwokerto, Banyumas, Jawa Tengah"
                           class="w-full px-4 py-2.5 rounded-xl border @error('lokasi_domisili') border-red-300 @else border-gray-300 @enderror focus:outline-none focus:ring-2 focus:ring-[#1F4E79]/20 focus:border-[#1F4E79] transition text-sm">
                    @error('lokasi_domisili')
                        <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Pengalaman --}}
                <div>
                    <label for="pengalaman" class="block text-sm font-semibold text-gray-700 mb-2">Pengalaman Kebencanaan (Opsional)</label>
                    <textarea name="pengalaman" id="pengalaman" rows="4" 
                              placeholder="Deskripsikan pengalaman kerelawanan Anda sebelumnya atau pelatihan SAR/medis yang pernah diikuti..."
                              class="w-full px-4 py-2.5 rounded-xl border @error('pengalaman') border-red-300 @else border-gray-300 @enderror focus:outline-none focus:ring-2 focus:ring-[#1F4E79]/20 focus:border-[#1F4E79] transition text-sm">{{ old('pengalaman', $relawan ? $relawan->pengalaman : '') }}</textarea>
                    @error('pengalaman')
                        <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Ketersediaan (Toggle) --}}
                <div class="flex items-center justify-between py-3 border-t border-b border-gray-100">
                    <div>
                        <label class="block text-sm font-semibold text-gray-800">Status Ketersediaan</label>
                        <p class="text-xs text-gray-500 mt-0.5">Aktifkan jika Anda siap dipanggil untuk penugasan bencana darurat.</p>
                    </div>
                    <div>
                        <input type="hidden" name="ketersediaan" :value="ketersediaan ? 1 : 0">
                        <button type="button" 
                                @click="ketersediaan = !ketersediaan"
                                class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                                :class="ketersediaan ? 'bg-[#1F4E79]' : 'bg-gray-200'">
                            <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                  :class="ketersediaan ? 'translate-x-5' : 'translate-x-0'"></span>
                        </button>
                    </div>
                </div>

                {{-- Action --}}
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="submit" 
                            class="px-6 py-2.5 bg-[#1F4E79] hover:bg-[#1F4E79]/90 text-white font-semibold rounded-xl text-sm transition shadow-sm hover:shadow">
                        {{ $relawan ? 'Perbarui Profil' : 'Simpan Profil' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
