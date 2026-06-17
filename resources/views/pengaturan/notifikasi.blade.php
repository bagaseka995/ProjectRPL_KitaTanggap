@extends('layouts.auth')
@section('title', 'Pengaturan Notifikasi')
@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-slate-900 flex flex-col justify-between transition-colors duration-300">
    @include('layouts.partials.navbar-main')
    @include('layouts.partials.navbar-sub')

    {{-- Main Container --}}
    <div class="max-w-3xl w-full mx-auto px-4 py-8 flex-grow flex flex-col justify-start">
        <div class="mb-8 animate-fade-in stagger-1">
            <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Preferensi Notifikasi</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2 text-sm leading-relaxed">Atur bagaimana Anda ingin menerima peringatan dini terkait bencana di sekitar Anda.</p>
        </div>

        <div class="bg-white dark:bg-slate-800 p-8 rounded-3xl border border-gray-100 dark:border-slate-700 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] animate-fade-in stagger-2 transition-colors">
            <form id="notification-form" class="space-y-6">
                {{-- Lokasi Domisili --}}
                <div class="group">
                    <label for="lokasi_domisili" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 group-focus-within:text-primary transition-colors">Lokasi Domisili (Wilayah Terdampak)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="w-4.5 h-4.5 text-gray-400 dark:text-gray-500 group-focus-within:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <input type="text" id="lokasi_domisili" name="lokasi_domisili" value="{{ $user->lokasi_domisili }}" 
                               class="input-brand w-full pl-11 pr-4 py-3 border border-gray-200 dark:border-slate-600 bg-gray-50/50 dark:bg-slate-700 rounded-xl text-sm transition focus:bg-white dark:focus:bg-slate-600 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 dark:focus:border-blue-500" 
                               placeholder="Misal: Jakarta Selatan" />
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 leading-relaxed">Kami menggunakan lokasi ini untuk mencocokkan peringatan dini jika ada bencana di wilayah tersebut.</p>
                </div>

                <hr class="border-gray-100 dark:border-slate-700 my-6">

                {{-- Email Notification Toggle --}}
                <div class="flex items-center justify-between p-4 rounded-2xl hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Notifikasi Email</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 max-w-xs">Terima peringatan dini bencana melalui email ({{ $user->email }}).</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="notif_aktif" name="notif_aktif" class="sr-only peer" {{ $user->notif_aktif ? 'checked' : '' }}>
                        <div class="w-12 h-6 bg-gray-200 dark:bg-slate-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-100 dark:peer-focus:ring-blue-900/50 rounded-full peer peer-checked:after:translate-x-6 peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 dark:after:border-slate-500 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary dark:peer-checked:bg-blue-600"></div>
                    </label>
                </div>

                {{-- Push Notification Toggle --}}
                <div class="flex items-center justify-between p-4 rounded-2xl hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Push Notification</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 max-w-xs">Terima notifikasi real-time langsung di perangkat browser Anda.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="fcm_aktif" name="fcm_aktif" class="sr-only peer" {{ $user->fcmTokens->count() > 0 ? 'checked' : '' }}>
                        <div class="w-12 h-6 bg-gray-200 dark:bg-slate-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-100 dark:peer-focus:ring-blue-900/50 rounded-full peer peer-checked:after:translate-x-6 peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 dark:after:border-slate-500 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary dark:peer-checked:bg-blue-600"></div>
                    </label>
                </div>

                <div class="pt-6 mt-6 border-t border-gray-100 dark:border-slate-700 flex justify-end">
                    <button type="submit" class="px-6 py-3 bg-gradient-to-r from-[#1F4E79] to-[#2E75B6] hover:from-[#163859] hover:to-[#1F4E79] text-white text-sm font-semibold rounded-xl shadow-lg shadow-primary/20 hover:shadow-xl hover:shadow-primary/30 transition-all duration-200 flex items-center gap-2 active:scale-[0.98]">
                        <span id="btn-text">Simpan Preferensi</span>
                        <svg id="btn-spinner" class="animate-spin h-4 w-4 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </div>

                {{-- Alert Box --}}
                <div id="alert-box" class="hidden p-4 rounded-xl text-sm font-medium mt-4 flex items-start gap-3"></div>
            </form>
        </div>
    </div>

    {{-- Footer --}}
    <footer class="bg-white dark:bg-slate-900 border-t border-gray-200 dark:border-slate-800 py-6 text-center text-xs text-gray-400 dark:text-gray-500 transition-colors duration-300">
        <p>&copy; 2026 KitaTanggap Kelompok 11 RPL. All rights reserved.</p>
    </footer>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('notification-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const btnText = document.getElementById('btn-text');
        const btnSpinner = document.getElementById('btn-spinner');
        const alertBox = document.getElementById('alert-box');
        
        btnText.classList.add('opacity-0');
        btnSpinner.classList.remove('hidden');
        btnSpinner.classList.add('absolute');
        alertBox.classList.add('hidden');
        alertBox.className = 'p-4 rounded-xl text-sm font-medium mt-4 hidden flex items-start gap-3 animate-fade-in';

        const payload = {
            lokasi_domisili: document.getElementById('lokasi_domisili').value,
            notif_aktif: document.getElementById('notif_aktif').checked,
            fcm_aktif: document.getElementById('fcm_aktif').checked
        };

        try {
            const response = await fetch('{{ route('pengaturan.notifikasi.update') }}', {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(payload)
            });

            const data = await response.json();

            if (response.ok) {
                alertBox.classList.remove('hidden');
                alertBox.classList.add('bg-emerald-50', 'dark:bg-emerald-900/30', 'text-emerald-800', 'dark:text-emerald-400', 'border', 'border-emerald-200', 'dark:border-emerald-800/50');
                alertBox.innerHTML = `
                    <svg class="w-5 h-5 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <div>${data.message || 'Preferensi berhasil diperbarui!'}</div>
                `;

                // Handle Push Notification Logic
                if (payload.fcm_aktif) {
                    if (typeof requestNotificationPermission === 'function') {
                        requestNotificationPermission(); // Trigger registration via firebase-messaging.js
                    }
                }
            } else {
                throw new Error(data.message || 'Terjadi kesalahan');
            }
        } catch (error) {
            alertBox.classList.remove('hidden');
            alertBox.classList.add('bg-red-50', 'dark:bg-red-900/30', 'text-red-800', 'dark:text-red-400', 'border', 'border-red-200', 'dark:border-red-800/50');
            alertBox.innerHTML = `
                <svg class="w-5 h-5 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <div>${error.message}</div>
            `;
        } finally {
            btnText.classList.remove('opacity-0');
            btnSpinner.classList.add('hidden');
            btnSpinner.classList.remove('absolute');
        }
    });
</script>
@endpush
