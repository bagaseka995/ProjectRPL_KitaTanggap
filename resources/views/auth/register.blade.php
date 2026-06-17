@extends('layouts.auth')

@section('title', 'Daftar Akun')
@section('meta_description', 'Daftar sebagai relawan atau donatur di KitaTanggap.')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 py-10">

    {{-- Latar dekorasi --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-[500px] h-[500px] bg-gradient-to-br from-primary/8 to-secondary/5 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-[500px] h-[500px] bg-gradient-to-tr from-secondary/6 to-primary/4 rounded-full blur-3xl"></div>
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
            <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white">Bergabung dengan KitaTanggap</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1.5">Buat akun untuk mulai berkontribusi</p>
        </div>

        {{-- Kartu form --}}
        <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl shadow-xl shadow-gray-200/50 dark:shadow-none border border-gray-100/80 dark:border-slate-700/80 p-8"
             x-data="registerForm()">

            {{-- Flash success --}}
            @if (session('success'))
                <div class="mb-5 flex items-start gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3 text-sm animate-fade-in">
                    <svg class="w-5 h-5 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" novalidate>
                @csrf

                {{-- Nama Lengkap --}}
                <div class="mb-4">
                    <label for="nama_lengkap" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="w-4.5 h-4.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <input id="nama_lengkap" name="nama_lengkap" type="text"
                               value="{{ old('nama_lengkap') }}"
                               autocomplete="name" required
                               placeholder="Masukkan nama lengkap Anda"
                               class="input-brand w-full pl-11 pr-4 py-3 border rounded-xl text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500
                                      {{ $errors->has('nama_lengkap') ? 'border-red-400 bg-red-50/50 dark:bg-red-900/20' : 'border-gray-200 dark:border-slate-600 bg-gray-50/50 dark:bg-slate-700/50' }}
                                      transition focus:bg-white dark:focus:bg-slate-700">
                    </div>
                    @error('nama_lengkap')
                        <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="mb-4">
                    <label for="email" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Alamat Email <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="w-4.5 h-4.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <input id="email" name="email" type="email"
                               value="{{ old('email') }}"
                               autocomplete="email" required
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

                {{-- No Telepon --}}
                <div class="mb-4">
                    <label for="no_telepon" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        No. Telepon <span class="text-gray-400 dark:text-gray-500 font-normal text-xs">(opsional)</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="w-4.5 h-4.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </div>
                        <input id="no_telepon" name="no_telepon" type="tel"
                               value="{{ old('no_telepon') }}"
                               placeholder="08xxxxxxxxxx"
                               class="input-brand w-full pl-11 pr-4 py-3 border rounded-xl text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500
                                      {{ $errors->has('no_telepon') ? 'border-red-400 bg-red-50/50 dark:bg-red-900/20' : 'border-gray-200 dark:border-slate-600 bg-gray-50/50 dark:bg-slate-700/50' }}
                                      transition focus:bg-white dark:focus:bg-slate-700">
                    </div>
                    @error('no_telepon')
                        <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Peran --}}
                <div class="mb-4">
                    <label for="peran" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Daftar Sebagai <span class="text-red-500">*</span>
                    </label>
                    <select id="peran" name="peran" required
                            class="input-brand w-full px-4 py-3 border rounded-xl text-sm text-gray-900 dark:text-white
                                   {{ $errors->has('peran') ? 'border-red-400 bg-red-50/50 dark:bg-red-900/20' : 'border-gray-200 dark:border-slate-600 bg-gray-50/50 dark:bg-slate-700/50' }}
                                   transition focus:bg-white dark:focus:bg-slate-700 cursor-pointer">
                        <option value="" disabled {{ old('peran') ? '' : 'selected' }}>— Pilih peran —</option>
                        <option value="relawan" {{ old('peran') === 'relawan' ? 'selected' : '' }}>
                            🤝 Relawan — Saya ingin membantu di lapangan
                        </option>
                        <option value="donatur" {{ old('peran') === 'donatur' ? 'selected' : '' }}>
                            💙 Donatur — Saya ingin berdonasi
                        </option>
                    </select>
                    @error('peran')
                        <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="mb-4">
                    <label for="password" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="w-4.5 h-4.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <input id="password" name="password"
                               :type="showPassword ? 'text' : 'password'"
                               @input="checkStrength($event.target.value)"
                               autocomplete="new-password" required
                               placeholder="Min. 8 karakter, huruf & angka"
                               class="input-brand w-full pl-11 pr-11 py-3 border rounded-xl text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500
                                      {{ $errors->has('password') ? 'border-red-400 bg-red-50/50 dark:bg-red-900/20' : 'border-gray-200 dark:border-slate-600 bg-gray-50/50 dark:bg-slate-700/50' }}
                                      transition focus:bg-white dark:focus:bg-slate-700">
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

                    {{-- Password strength indicator --}}
                    <div class="mt-2.5" x-show="password.length > 0" x-cloak>
                        <div class="flex gap-1.5 mb-1.5">
                            <template x-for="i in 4" :key="i">
                                <div class="h-1.5 flex-1 rounded-full bg-gray-200 overflow-hidden">
                                    <div class="strength-bar h-full rounded-full"
                                         :style="{ width: strength >= i ? '100%' : '0%', backgroundColor: strengthColor }"></div>
                                </div>
                            </template>
                        </div>
                        <p class="text-xs font-medium" :style="{ color: strengthColor }" x-text="strengthLabel"></p>
                    </div>

                    @error('password')
                        <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Konfirmasi Password --}}
                <div class="mb-6">
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Konfirmasi Password <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="w-4.5 h-4.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <input id="password_confirmation" name="password_confirmation"
                               :type="showConfirm ? 'text' : 'password'"
                               @input="checkMatch($event.target.value)"
                               autocomplete="new-password" required
                               placeholder="Ulangi password Anda"
                               class="input-brand w-full pl-11 pr-11 py-3 border border-gray-200 dark:border-slate-600 bg-gray-50/50 dark:bg-slate-700/50 rounded-xl text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 transition focus:bg-white dark:focus:bg-slate-700">
                        <button type="button" @click="showConfirm = !showConfirm"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition p-1">
                            <svg x-show="!showConfirm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showConfirm" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                    <p x-show="matchMsg !== ''" x-cloak
                       class="mt-1.5 text-xs flex items-center gap-1 font-medium"
                       :class="matchOk ? 'text-emerald-600' : 'text-red-600'"
                       x-text="matchMsg"></p>
                </div>

                {{-- Tombol Daftar --}}
                <button type="submit"
                        class="w-full py-3 px-6 bg-gradient-to-r from-[#1F4E79] to-[#2E75B6] hover:from-[#163859] hover:to-[#1F4E79] active:scale-[0.98] text-white font-semibold rounded-xl
                               transition duration-200 shadow-lg shadow-primary/20 hover:shadow-xl hover:shadow-primary/30 text-sm tracking-wide">
                    Daftar Sekarang
                </button>
            </form>

            {{-- Link ke login --}}
            <p class="mt-6 text-center text-sm text-gray-500 dark:text-gray-400">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="text-primary dark:text-blue-400 font-semibold hover:underline transition">Masuk di sini</a>
            </p>
        </div>

        {{-- Footer note --}}
        <p class="mt-6 text-center text-xs text-gray-400 dark:text-gray-500">
            © {{ date('Y') }} KitaTanggap — Universitas Jenderal Soedirman
        </p>
    </div>
</div>
@endsection

@push('scripts')
<script>
function registerForm() {
    return {
        showPassword: false,
        showConfirm:  false,
        password:     '',
        strength:     0,
        strengthColor: '#9ca3af',
        strengthLabel: '',
        matchMsg:  '',
        matchOk:   false,

        checkStrength(val) {
            this.password = val;
            let score = 0;
            if (val.length >= 8)                        score++;
            if (/[a-zA-Z]/.test(val))                  score++;
            if (/[0-9]/.test(val))                      score++;
            if (/[^a-zA-Z0-9]/.test(val) && val.length >= 10) score++;
            this.strength = score;
            const map = {
                0: ['#ef4444', 'Sangat lemah'],
                1: ['#f97316', 'Lemah'],
                2: ['#eab308', 'Sedang'],
                3: ['#22c55e', 'Kuat'],
                4: ['#16a34a', 'Sangat kuat'],
            };
            [this.strengthColor, this.strengthLabel] = map[score];
        },

        checkMatch(val) {
            if (!val) { this.matchMsg = ''; return; }
            if (val === this.password) {
                this.matchOk  = true;
                this.matchMsg = '✓ Password cocok';
            } else {
                this.matchOk  = false;
                this.matchMsg = '✗ Password tidak cocok';
            }
        },
    };
}
</script>
@endpush
