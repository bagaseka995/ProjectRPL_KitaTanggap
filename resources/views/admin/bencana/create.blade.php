@extends('layouts.auth')
@section('title', 'Tambah Bencana — Admin')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-slate-900 flex flex-col transition-colors duration-300">
    @include('layouts.partials.navbar-main')
    @include('layouts.partials.navbar-sub')

    <div class="max-w-3xl w-full mx-auto px-4 py-8 flex-grow">

        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-6">
            <a href="{{ route('admin.bencana.index') }}" class="hover:text-[#2E75B6] transition">Manajemen Bencana</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-gray-800 dark:text-white font-semibold">Tambah Bencana</span>
        </nav>

        <div class="bg-white dark:bg-slate-800 rounded-3xl border border-gray-100 dark:border-slate-700 shadow-sm p-8">
            <div class="mb-8">
                <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white">Tambah Data Bencana</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Isi semua informasi bencana. Setelah disimpan, notifikasi otomatis akan dikirim ke pengguna di wilayah terdampak.</p>
            </div>

            @if($errors->any())
            <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-2xl p-4">
                <ul class="text-sm text-red-700 dark:text-red-400 space-y-1">
                    @foreach($errors->all() as $e)<li>• {{ $e }}</li>@endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('admin.bencana.store') }}" id="form-tambah-bencana">
                @csrf

                <div class="space-y-6">
                    {{-- Nama Bencana --}}
                    <div>
                        <label for="nama_bencana" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Nama Bencana <span class="text-red-500">*</span></label>
                        <input type="text" id="nama_bencana" name="nama_bencana" value="{{ old('nama_bencana') }}"
                               placeholder="cth. Banjir Bandang Sungai Citarum"
                               class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700 border border-gray-200 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#2E75B6] dark:text-white @error('nama_bencana') border-red-500 @enderror">
                    </div>

                    {{-- Jenis & Status Siaga --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="jenis_bencana" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Jenis Bencana <span class="text-red-500">*</span></label>
                            <select id="jenis_bencana" name="jenis_bencana"
                                    class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700 border border-gray-200 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#2E75B6] dark:text-white @error('jenis_bencana') border-red-500 @enderror">
                                <option value="">-- Pilih Jenis --</option>
                                @foreach(['Banjir','Gempa Bumi','Tsunami','Tanah Longsor','Kebakaran','Kekeringan','Angin Puting Beliung','Gunung Meletus','Lainnya'] as $jenis)
                                <option value="{{ $jenis }}" @selected(old('jenis_bencana') === $jenis)>{{ $jenis }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="status_siaga" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Status Siaga <span class="text-red-500">*</span></label>
                            <select id="status_siaga" name="status_siaga"
                                    class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700 border border-gray-200 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#2E75B6] dark:text-white @error('status_siaga') border-red-500 @enderror">
                                <option value="">-- Pilih Status --</option>
                                <option value="waspada" @selected(old('status_siaga') === 'waspada')>🟡 Waspada</option>
                                <option value="siaga"   @selected(old('status_siaga') === 'siaga')>🟠 Siaga</option>
                                <option value="awas"    @selected(old('status_siaga') === 'awas')>🔴 Awas</option>
                            </select>
                        </div>
                    </div>

                    {{-- Lokasi --}}
                    <div>
                        <label for="lokasi" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Lokasi Bencana <span class="text-red-500">*</span></label>
                        <input type="text" id="lokasi" name="lokasi" value="{{ old('lokasi') }}"
                               placeholder="cth. Kabupaten Bandung, Jawa Barat"
                               class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700 border border-gray-200 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#2E75B6] dark:text-white @error('lokasi') border-red-500 @enderror">
                    </div>

                    {{-- Koordinat --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="latitude" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Latitude <span class="text-red-500">*</span></label>
                            <input type="number" id="latitude" name="latitude" value="{{ old('latitude') }}" step="0.0000001" min="-90" max="90"
                                   placeholder="cth. -6.917464"
                                   class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700 border border-gray-200 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#2E75B6] dark:text-white @error('latitude') border-red-500 @enderror">
                        </div>
                        <div>
                            <label for="longitude" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Longitude <span class="text-red-500">*</span></label>
                            <input type="number" id="longitude" name="longitude" value="{{ old('longitude') }}" step="0.0000001" min="-180" max="180"
                                   placeholder="cth. 107.619125"
                                   class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700 border border-gray-200 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#2E75B6] dark:text-white @error('longitude') border-red-500 @enderror">
                        </div>
                    </div>

                    {{-- Bantuan Koordinat --}}
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800/40 rounded-xl p-4 text-sm text-blue-700 dark:text-blue-300">
                        <p class="font-semibold mb-1">💡 Tips mendapatkan koordinat:</p>
                        <p>Buka <a href="https://maps.google.com" target="_blank" class="underline font-medium">Google Maps</a> → klik lokasi bencana → salin koordinat dari URL atau popup.</p>
                    </div>

                    {{-- Tanggal Kejadian & Target Dana --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="tanggal_kejadian" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Tanggal Kejadian <span class="text-red-500">*</span></label>
                            <input type="date" id="tanggal_kejadian" name="tanggal_kejadian" value="{{ old('tanggal_kejadian', date('Y-m-d')) }}"
                                   class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700 border border-gray-200 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#2E75B6] dark:text-white @error('tanggal_kejadian') border-red-500 @enderror">
                        </div>
                        <div>
                            <label for="target_dana" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Target Dana (Rp)</label>
                            <input type="number" id="target_dana" name="target_dana" value="{{ old('target_dana', 0) }}" min="0" step="100000"
                                   placeholder="cth. 50000000"
                                   class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700 border border-gray-200 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#2E75B6] dark:text-white @error('target_dana') border-red-500 @enderror">
                        </div>
                    </div>

                    {{-- Deskripsi --}}
                    <div>
                        <label for="deskripsi" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Deskripsi Bencana <span class="text-red-500">*</span></label>
                        <textarea id="deskripsi" name="deskripsi" rows="5"
                                  placeholder="Jelaskan kondisi bencana, jumlah korban, kebutuhan mendesak, dll."
                                  class="w-full px-4 py-2.5 text-sm bg-gray-50 dark:bg-slate-700 border border-gray-200 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#2E75B6] dark:text-white resize-none @error('deskripsi') border-red-500 @enderror">{{ old('deskripsi') }}</textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-100 dark:border-slate-700">
                    <a href="{{ route('admin.bencana.index') }}" class="px-5 py-2.5 bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-gray-300 text-sm font-semibold rounded-xl hover:bg-gray-200 dark:hover:bg-slate-600 transition">Batal</a>
                    <button type="submit" id="btn-simpan"
                            class="px-6 py-2.5 bg-gradient-to-r from-[#1F4E79] to-[#2E75B6] text-white text-sm font-semibold rounded-xl shadow hover:shadow-lg hover:scale-105 transition-all duration-200">
                        Simpan Bencana & Kirim Notifikasi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <footer class="bg-white dark:bg-slate-900 border-t border-gray-200 dark:border-slate-800 py-6 text-center text-xs text-gray-400 dark:text-gray-500 transition-colors duration-300">
        <p>&copy; 2026 KitaTanggap Kelompok 11 RPL. All rights reserved.</p>
    </footer>
</div>
@endsection
