@extends('layouts.auth')
@section('title', 'Profil Relawan')
@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-slate-900 transition-colors duration-300">
    @include('layouts.partials.navbar-main')
    @include('layouts.partials.navbar-sub')

    {{-- Main Content --}}
    <div class="max-w-3xl mx-auto px-4 py-8">

        {{-- Welcome header --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 p-6 shadow-sm mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4 transition-colors">
            <div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-white">Halo, {{ auth()->user()->nama_lengkap }}</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Lengkapi data diri Anda untuk dapat ditugaskan sebagai relawan kebencanaan.</p>
            </div>
            
            {{-- Status Badge --}}
            <div class="text-left md:text-right">
                <p class="text-xs text-gray-400 dark:text-gray-500 font-semibold uppercase tracking-wider">Status Verifikasi</p>
                <div class="mt-1">
                    @if(!$relawan)
                        <span class="px-3 py-1 bg-gray-100 dark:bg-slate-700 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 rounded-full text-xs font-bold uppercase">Belum Terdaftar</span>
                    @elseif($relawan->status_verifikasi === 'pending')
                        <span class="px-3 py-1 bg-yellow-100 dark:bg-yellow-900/30 border border-yellow-300 dark:border-yellow-800/50 text-yellow-800 dark:text-yellow-500 rounded-full text-xs font-bold uppercase">Menunggu Verifikasi</span>
                    @elseif($relawan->status_verifikasi === 'terverifikasi')
                        <span class="px-3 py-1 bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-800/50 text-green-800 dark:text-green-400 rounded-full text-xs font-bold uppercase">Terverifikasi</span>
                    @elseif($relawan->status_verifikasi === 'ditolak')
                        <span class="px-3 py-1 bg-red-100 dark:bg-red-900/30 border border-red-300 dark:border-red-800/50 text-red-800 dark:text-red-400 rounded-full text-xs font-bold uppercase">Ditolak</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Alerts --}}
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/30 border-l-4 border-green-500 rounded-r-xl text-green-700 dark:text-green-400 text-sm flex items-start shadow-sm">
                <svg class="w-5 h-5 mr-3 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->has('umum'))
            <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/30 border-l-4 border-red-500 rounded-r-xl text-red-700 dark:text-red-400 text-sm flex items-start shadow-sm">
                <svg class="w-5 h-5 mr-3 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                <span>{{ $errors->first('umum') }}</span>
            </div>
        @endif

        {{-- Profile Form Card --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden transition-colors" x-data="{
            ketersediaan: {{ old('ketersediaan', $relawan ? $relawan->ketersediaan : true) ? 'true' : 'false' }}
        }">
            <div class="border-b border-gray-100 dark:border-slate-700 bg-gray-50 dark:bg-slate-700/50 px-6 py-4">
                <h2 class="font-bold text-gray-800 dark:text-gray-200 text-lg">Data Profil Relawan</h2>
            </div>
            
            <form action="{{ $relawan ? route('relawan.profil.update') : route('relawan.profil.store') }}" method="POST" class="p-6 space-y-6">
                @csrf
                @if($relawan)
                    @method('PUT')
                @endif

                {{-- Keahlian --}}
                <div>
                    <label for="keahlian" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Keahlian <span class="text-red-500">*</span></label>
                    <input type="text" name="keahlian" id="keahlian" 
                           value="{{ old('keahlian', $relawan ? $relawan->keahlian : '') }}" 
                           placeholder="Contoh: Medis, SAR, Logistik, Dapur Umum, Trauma Healing"
                           class="w-full px-4 py-2.5 bg-white dark:bg-slate-700 rounded-xl border @error('keahlian') border-red-300 dark:border-red-500/50 @else border-gray-300 dark:border-slate-600 @enderror focus:outline-none focus:ring-2 focus:ring-[#1F4E79]/20 dark:focus:ring-blue-500/20 focus:border-[#1F4E79] dark:focus:border-blue-500 transition text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500">
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Pisahkan keahlian Anda menggunakan tanda koma (,).</p>
                    @error('keahlian')
                        <p class="text-xs text-red-500 dark:text-red-400 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Lokasi Domisili --}}
                <div>
                    <label for="lokasi_domisili" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Lokasi Domisili <span class="text-red-500">*</span></label>
                    <input type="text" name="lokasi_domisili" id="lokasi_domisili" 
                           value="{{ old('lokasi_domisili', $relawan ? $relawan->lokasi_domisili : '') }}" 
                           placeholder="Contoh: Purwokerto, Banyumas, Jawa Tengah"
                           class="w-full px-4 py-2.5 bg-white dark:bg-slate-700 rounded-xl border @error('lokasi_domisili') border-red-300 dark:border-red-500/50 @else border-gray-300 dark:border-slate-600 @enderror focus:outline-none focus:ring-2 focus:ring-[#1F4E79]/20 dark:focus:ring-blue-500/20 focus:border-[#1F4E79] dark:focus:border-blue-500 transition text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500">
                    @error('lokasi_domisili')
                        <p class="text-xs text-red-500 dark:text-red-400 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Pengalaman --}}
                <div>
                    <label for="pengalaman" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Pengalaman Kebencanaan (Opsional)</label>
                    <textarea name="pengalaman" id="pengalaman" rows="4" 
                              placeholder="Deskripsikan pengalaman kerelawanan Anda sebelumnya atau pelatihan SAR/medis yang pernah diikuti..."
                              class="w-full px-4 py-2.5 bg-white dark:bg-slate-700 rounded-xl border @error('pengalaman') border-red-300 dark:border-red-500/50 @else border-gray-300 dark:border-slate-600 @enderror focus:outline-none focus:ring-2 focus:ring-[#1F4E79]/20 dark:focus:ring-blue-500/20 focus:border-[#1F4E79] dark:focus:border-blue-500 transition text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500">{{ old('pengalaman', $relawan ? $relawan->pengalaman : '') }}</textarea>
                    @error('pengalaman')
                        <p class="text-xs text-red-500 dark:text-red-400 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Ketersediaan (Toggle) --}}
                <div class="flex items-center justify-between py-3 border-t border-b border-gray-100 dark:border-slate-700">
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 dark:text-gray-200">Status Ketersediaan</label>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Aktifkan jika Anda siap dipanggil untuk penugasan bencana darurat.</p>
                    </div>
                    <div>
                        <input type="hidden" name="ketersediaan" :value="ketersediaan ? 1 : 0">
                        <button type="button" 
                                @click="ketersediaan = !ketersediaan"
                                class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                                :class="ketersediaan ? 'bg-[#1F4E79] dark:bg-blue-600' : 'bg-gray-200 dark:bg-slate-600'">
                            <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                  :class="ketersediaan ? 'translate-x-5' : 'translate-x-0'"></span>
                        </button>
                    </div>
                </div>

                {{-- Action --}}
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="submit" 
                            class="px-6 py-2.5 bg-[#1F4E79] dark:bg-blue-600 hover:bg-[#1F4E79]/90 dark:hover:bg-blue-700 text-white font-semibold rounded-xl text-sm transition shadow-sm hover:shadow">
                        {{ $relawan ? 'Perbarui Profil' : 'Simpan Profil' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
