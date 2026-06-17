@extends('layouts.auth')
@section('title', 'Lupa Password')
@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-10">
    
    {{-- Latar dekorasi --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-[500px] h-[500px] bg-gradient-to-br from-primary/8 to-secondary/5 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-[500px] h-[500px] bg-gradient-to-tr from-accent/6 to-primary/4 rounded-full blur-3xl"></div>
    </div>

    <div class="relative w-full max-w-md animate-fade-up">
        <div class="text-center mb-8">
            <a href="/" class="inline-block">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-[#1F4E79] to-[#2E75B6] rounded-2xl shadow-lg shadow-primary/20 mb-4 transition hover:scale-105">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                </div>
            </a>
            <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white">Lupa Password?</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1.5">Masukkan email Anda untuk mendapatkan link reset password</p>
        </div>
        
        <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl shadow-xl shadow-gray-200/50 dark:shadow-none border border-gray-100/80 dark:border-slate-700/80 p-8">
            @if (session('success'))
                <div class="mb-5 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3 text-sm animate-fade-in">{{ session('success') }}</div>
            @endif
            <form method="POST" action="{{ route('password.email') }}" novalidate>
                @csrf
                <div class="mb-5">
                    <label for="email" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Alamat Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="w-4.5 h-4.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                               placeholder="nama@email.com"
                               class="input-brand w-full pl-11 pr-4 py-3 border rounded-xl text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500
                                      {{ $errors->has('email') ? 'border-red-400 bg-red-50/50 dark:bg-red-900/20' : 'border-gray-200 dark:border-slate-600 bg-gray-50/50 dark:bg-slate-700/50' }} transition focus:bg-white dark:focus:bg-slate-700">
                    </div>
                    @error('email')
                        <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                <button type="submit"
                        class="w-full py-3 bg-gradient-to-r from-[#1F4E79] to-[#2E75B6] hover:from-[#163859] hover:to-[#1F4E79] active:scale-[0.98] text-white font-semibold rounded-xl transition duration-200 shadow-lg shadow-primary/20 hover:shadow-xl hover:shadow-primary/30 text-sm tracking-wide">
                    Kirim Link Reset Password
                </button>
            </form>
            <p class="mt-6 text-center text-sm text-gray-500 dark:text-gray-400">
                <a href="{{ route('login') }}" class="text-primary dark:text-blue-400 font-semibold hover:underline transition">← Kembali ke halaman masuk</a>
            </p>
        </div>
        
        <p class="mt-6 text-center text-xs text-gray-400 dark:text-gray-500">
            © {{ date('Y') }} KitaTanggap — Universitas Jenderal Soedirman
        </p>
    </div>
</div>
@endsection
