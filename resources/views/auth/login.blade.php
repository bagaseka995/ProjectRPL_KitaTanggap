@extends('layouts.auth')

@section('title', 'Masuk')
@section('meta_description', 'Masuk ke akun KitaTanggap Anda.')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-10">

    {{-- Latar dekorasi --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-[500px] h-[500px] bg-gradient-to-br from-primary/8 to-secondary/5 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-[500px] h-[500px] bg-gradient-to-tr from-accent/6 to-primary/4 rounded-full blur-3xl"></div>
    </div>

    <div class="relative w-full max-w-md animate-fade-up">

        {{-- Logo & heading --}}
        <div class="text-center mb-8">
            <a href="/" class="inline-block">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-[#1F4E79] to-[#2E75B6] rounded-2xl shadow-lg shadow-primary/20 mb-4 transition hover:scale-105">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </div>
            </a>
            <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white">Selamat Datang Kembali</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1.5">Masuk ke akun <span class="font-semibold text-primary">Kita<span class="text-accent">Tanggap</span></span> Anda</p>
        </div>

        <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl shadow-xl shadow-gray-200/50 dark:shadow-none border border-gray-100/80 dark:border-slate-700/80 p-8"
             x-data="loginForm()">

            {{-- Flash success --}}
            @if (session('success'))
                <div class="mb-5 flex items-start gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3 text-sm animate-fade-in">
                    <svg class="w-5 h-5 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            {{-- Peringatan akun dikunci --}}
            @if (session('locked'))
                <div class="mb-5 bg-red-50 border border-red-200 rounded-xl px-4 py-4 text-sm animate-fade-in"
                     x-data="lockoutTimer({{ session('remaining_seconds', 900) }})">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-red-500 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <p class="font-semibold text-red-800">Akun Dikunci Sementara</p>
                            <p class="text-red-600 mt-0.5">
                                Coba lagi dalam
                                <span class="font-mono font-bold" x-text="formatTime(remaining)"></span>
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Error umum --}}
            @if ($errors->any())
                <div class="mb-5 flex items-start gap-3 bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3 text-sm animate-fade-in">
                    <svg class="w-5 h-5 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" novalidate>
                @csrf

                {{-- Email --}}
                <div class="mb-5">
                    <label for="email" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Alamat Email
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="w-4.5 h-4.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <input id="email" name="email" type="email"
                               value="{{ old('email') }}"
                               autocomplete="email" required autofocus
                               placeholder="nama@email.com"
                               class="input-brand w-full pl-11 pr-4 py-3 border rounded-xl text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500
                                      {{ $errors->has('email') ? 'border-red-400 bg-red-50/50 dark:bg-red-900/20' : 'border-gray-200 dark:border-slate-600 bg-gray-50/50 dark:bg-slate-700/50' }}
                                      transition focus:bg-white dark:focus:bg-slate-700">
                    </div>
                    @error('email')
                        <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="mb-5">
                    <div class="flex items-center justify-between mb-2">
                        <label for="password" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Password</label>
                        <a href="{{ route('password.request') }}" class="text-xs text-primary hover:text-primary-dark hover:underline font-medium transition">
                            Lupa Password?
                        </a>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="w-4.5 h-4.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <input id="password" name="password"
                               :type="showPassword ? 'text' : 'password'"
                               autocomplete="current-password" required
                               placeholder="Masukkan password Anda"
                               class="input-brand w-full pl-11 pr-11 py-3 border border-gray-200 dark:border-slate-600 bg-gray-50/50 dark:bg-slate-700/50 rounded-xl text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 transition focus:bg-white dark:focus:bg-slate-700">
                        <button type="button" @click="showPassword = !showPassword"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition p-1">
                            <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showPassword" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Ingat Saya --}}
                <div class="flex items-center mb-6">
                    <input id="remember" name="remember" type="checkbox"
                           class="custom-checkbox w-4 h-4 rounded border-gray-300 text-primary cursor-pointer transition">
                    <label for="remember" class="ml-2.5 text-sm text-gray-600 dark:text-gray-400 cursor-pointer select-none">
                        Ingat Saya selama 30 hari
                    </label>
                </div>

                {{-- Tombol Masuk --}}
                <button type="submit"
                        class="w-full py-3 px-6 bg-gradient-to-r from-[#1F4E79] to-[#2E75B6] hover:from-[#163859] hover:to-[#1F4E79] active:scale-[0.98] text-white font-semibold rounded-xl
                               transition duration-200 shadow-lg shadow-primary/20 hover:shadow-xl hover:shadow-primary/30 text-sm tracking-wide">
                    Masuk
                </button>
            </form>

            {{-- Link daftar --}}
            <p class="mt-6 text-center text-sm text-gray-500 dark:text-gray-400">
                Belum punya akun?
                <a href="{{ route('register') }}" class="text-primary dark:text-blue-400 font-semibold hover:underline transition">Daftar Akun Baru</a>
            </p>
        </div>

        <p class="mt-6 text-center text-xs text-gray-400 dark:text-gray-500">
            © {{ date('Y') }} KitaTanggap — Universitas Jenderal Soedirman
        </p>
    </div>
</div>
@endsection

@push('scripts')
<script>
function loginForm() {
    return { showPassword: false };
}

function lockoutTimer(initialSeconds) {
    return {
        remaining: initialSeconds,
        interval: null,
        init() {
            this.interval = setInterval(() => {
                if (this.remaining > 0) {
                    this.remaining--;
                } else {
                    clearInterval(this.interval);
                    window.location.reload();
                }
            }, 1000);
        },
        formatTime(s) {
            const m = Math.floor(s / 60);
            const sec = s % 60;
            return `${String(m).padStart(2,'0')}:${String(sec).padStart(2,'0')}`;
        },
        destroy() { clearInterval(this.interval); }
    };
}
</script>
@endpush
