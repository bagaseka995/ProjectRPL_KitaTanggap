@extends('layouts.auth')
@section('title', 'Verifikasi Email')
@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-10">
    
    {{-- Latar dekorasi --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-[500px] h-[500px] bg-gradient-to-br from-primary/8 to-secondary/5 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-[500px] h-[500px] bg-gradient-to-tr from-accent/6 to-primary/4 rounded-full blur-3xl"></div>
    </div>

    <div class="relative w-full max-w-md animate-fade-up">
        <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl shadow-xl shadow-gray-200/50 dark:shadow-none border border-gray-100/80 dark:border-slate-700/80 p-8 text-center">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-blue-50 dark:bg-slate-700 text-primary dark:text-blue-400 rounded-full mb-6">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white mb-3">Verifikasi Email Anda</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm mb-8 leading-relaxed">
                Kami telah mengirim link verifikasi ke email Anda. Silakan cek inbox atau folder spam untuk melanjutkan.
            </p>
            @if (session('success'))
                <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3 text-sm animate-fade-in">
                    {{ session('success') }}
                </div>
            @endif
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit"
                        class="w-full py-3 bg-gradient-to-r from-[#1F4E79] to-[#2E75B6] hover:from-[#163859] hover:to-[#1F4E79] active:scale-[0.98] text-white font-semibold rounded-xl transition duration-200 shadow-lg shadow-primary/20 hover:shadow-xl hover:shadow-primary/30 text-sm tracking-wide">
                    Kirim Ulang Link Verifikasi
                </button>
            </form>
            <form method="POST" action="{{ route('logout') }}" class="mt-4">
                @csrf
                <button type="submit" class="w-full py-2.5 text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white font-medium text-sm transition">
                    Keluar dari Akun
                </button>
            </form>
        </div>
        
        <p class="mt-6 text-center text-xs text-gray-400 dark:text-gray-500">
            © {{ date('Y') }} KitaTanggap — Universitas Jenderal Soedirman
        </p>
    </div>
</div>
@endsection
