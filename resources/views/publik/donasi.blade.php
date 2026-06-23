<!DOCTYPE html>
<html lang="id" x-data="themeHandler()" :class="{ 'dark': isDark }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Donasi untuk {{ $bencana->nama_bencana }} — KitaTanggap</title>
    <meta name="description" content="Salurkan donasi untuk membantu korban {{ $bencana->nama_bencana }} di {{ $bencana->lokasi }} melalui platform KitaTanggap.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: { extend: {
                colors: {
                    primary:   { DEFAULT: '#1F4E79', light: '#2E75B6', dark: '#163859' },
                    secondary: '#2E75B6',
                    accent:    '#C55A11',
                    danger:    '#C0392B',
                    success:   '#16A34A',
                    warning:   '#F59E0B',
                },
                fontFamily: { sans: ['Inter','Arial','sans-serif'] }
            }}
        }
    </script>

    {{-- Midtrans Snap.js (REQ-20) --}}
    <script src="{{ $midtransSnapUrl }}" data-client-key="{{ $midtransClientKey }}"></script>

    <style>
        body { font-family: 'Inter', Arial, sans-serif; }
        [x-cloak] { display: none !important; }

        /* ─── Progress Bar Animation ─── */
        @keyframes fillBar {
            from { width: 0%; }
        }
        .progress-fill {
            animation: fillBar 1.2s ease-out forwards;
            background: linear-gradient(90deg, #1F4E79, #2E75B6, #3B82F6);
            background-size: 200% 100%;
        }
        @keyframes shimmer {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        .progress-fill::after {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,.3), transparent);
            background-size: 200% 100%;
            animation: shimmer 2s infinite;
        }

        /* ─── Card Hover Effects ─── */
        .card-hover { transition: all .3s ease; }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 12px 40px rgba(31,78,121,.15); }

        /* ─── Donation Amount Button ─── */
        .amount-btn {
            transition: all .2s ease;
            border: 2px solid #E5E7EB;
        }
        .amount-btn:hover {
            border-color: #2E75B6;
            background: #EFF6FF;
        }
        .amount-btn.active {
            border-color: #1F4E79;
            background: #1F4E79;
            color: white;
            box-shadow: 0 4px 12px rgba(31,78,121,.3);
        }
        .dark .amount-btn {
            border-color: #475569;
            color: #f1f5f9;
        }
        .dark .amount-btn:hover {
            border-color: #3b82f6;
            background: #1e293b;
        }
        .dark .amount-btn.active {
            border-color: #3b82f6;
            background: #3b82f6;
            color: white;
            box-shadow: 0 4px 12px rgba(59,130,246,.3);
        }

        /* ─── Input Focus ─── */
        .input-focus:focus {
            outline: none;
            border-color: #1F4E79;
            box-shadow: 0 0 0 3px rgba(31,78,121,.15);
        }

        /* ─── Pulse Animation ─── */
        @keyframes pulse-ring {
            0% { transform: scale(0.8); opacity: 1; }
            100% { transform: scale(2); opacity: 0; }
        }
        .pulse-ring::before {
            content: '';
            position: absolute;
            inset: -4px;
            border-radius: 50%;
            border: 2px solid currentColor;
            animation: pulse-ring 1.5s cubic-bezier(0.215, 0.61, 0.355, 1) infinite;
        }

        /* ─── Fade In Up ─── */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .animate-in { animation: fadeInUp .5s ease both; }
        .animate-in-delay-1 { animation: fadeInUp .5s ease .1s both; }
        .animate-in-delay-2 { animation: fadeInUp .5s ease .2s both; }
        .animate-in-delay-3 { animation: fadeInUp .5s ease .3s both; }

        /* ─── Success Checkmark ─── */
        @keyframes checkmark {
            0% { stroke-dashoffset: 100; }
            100% { stroke-dashoffset: 0; }
        }
        .checkmark-path {
            stroke-dasharray: 100;
            stroke-dashoffset: 100;
            animation: checkmark .6s ease .3s forwards;
        }
    </style>
    <script>
        function themeHandler() {
            return {
                isDark: false,
                init() {
                    if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                        this.isDark = true;
                    } else {
                        this.isDark = false;
                    }
                    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
                        if (!localStorage.getItem('theme')) {
                            this.isDark = e.matches;
                        }
                    });
                },
                toggleTheme() {
                    this.isDark = !this.isDark;
                    localStorage.setItem('theme', this.isDark ? 'dark' : 'light');
                }
            }
        }
    </script>
</head>
<body class="bg-gradient-to-br from-slate-50 via-blue-50/30 to-indigo-50/30 dark:from-slate-900 dark:via-slate-800/80 dark:to-indigo-950/40 text-gray-800 dark:text-gray-100 transition-colors duration-300 min-h-screen">

{{-- ═══ NAVBAR ═══ --}}
@include('layouts.partials.navbar-main')
@include('layouts.partials.navbar-sub')

{{-- ═══ BREADCRUMB ═══ --}}
<div class="max-w-7xl mx-auto px-4 py-3">
    <nav class="text-sm text-gray-500 dark:text-gray-400">
        <a href="/" class="hover:text-primary dark:hover:text-blue-400 transition">Beranda</a>
        <span class="mx-2">›</span>
        <a href="/peta" class="hover:text-primary dark:hover:text-blue-400 transition">Peta Bencana</a>
        <span class="mx-2">›</span>
        <span class="text-gray-800 dark:text-gray-200 font-medium">Donasi</span>
    </nav>
</div>

{{-- ═══ KONTEN UTAMA ═══ --}}
<div class="max-w-7xl mx-auto px-4 pb-12" x-data="donasiApp()">

    {{-- ─── BENCANA SUDAH TIDAK AKTIF ─── --}}
    @if(!$bencana->status_aktif)
    <div class="animate-in max-w-2xl mx-auto mt-8">
        <div class="bg-red-50 border-2 border-red-200 rounded-2xl p-8 text-center">
            <svg class="w-16 h-16 mx-auto text-red-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>
            <h2 class="text-xl font-bold text-red-800 mb-2">Donasi Telah Ditutup</h2>
            <p class="text-red-600">Donasi untuk bencana <strong>{{ $bencana->nama_bencana }}</strong> telah ditutup. Terima kasih atas kepedulian Anda.</p>
            <a href="/peta" class="inline-block mt-6 px-6 py-2.5 bg-[#1F4E79] text-white rounded-xl font-semibold hover:bg-[#163859] transition">
                ← Kembali ke Peta Bencana
            </a>
        </div>
    </div>
    @else

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mt-2">

        {{-- ═══ KOLOM KIRI: INFO BENCANA + PROGRESS ═══ --}}
        <div class="lg:col-span-1 space-y-6">

            {{-- Card: Detail Bencana --}}
            <div class="animate-in bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden card-hover transition-colors">
                {{-- Header Gradient --}}
                <div class="bg-gradient-to-r from-[#1F4E79] to-[#2E75B6] p-6 text-white">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wide
                              {{ $bencana->status_siaga === 'awas' ? 'bg-red-500' : ($bencana->status_siaga === 'siaga' ? 'bg-orange-500' : 'bg-yellow-500') }}">
                            {{ $bencana->label_siaga }}
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-white/20 capitalize">
                            {{ str_replace('_', ' ', $bencana->jenis_bencana) }}
                        </span>
                    </div>
                    <h1 class="text-xl font-bold leading-tight">{{ $bencana->nama_bencana }}</h1>
                </div>

                <div class="p-5 space-y-3">
                    <div class="flex items-center gap-3 text-sm text-gray-600 dark:text-gray-300">
                        <svg class="w-4 h-4 text-primary dark:text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        </svg>
                        <span>{{ $bencana->lokasi }}</span>
                    </div>
                    <div class="flex items-center gap-3 text-sm text-gray-600 dark:text-gray-300">
                        <svg class="w-4 h-4 text-primary dark:text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>{{ $bencana->tanggal_kejadian?->format('d F Y') }}</span>
                    </div>
                    @if($bencana->deskripsi)
                    <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed pt-2 border-t border-gray-100 dark:border-slate-700">
                        {{ Str::limit($bencana->deskripsi, 200) }}
                    </p>
                    @endif
                </div>
            </div>

            {{-- Card: Progress Donasi --}}
            <div class="animate-in-delay-1 bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 shadow-sm p-6 card-hover transition-colors">
                <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                    <span class="w-2 h-2 bg-primary dark:bg-blue-500 rounded-full relative pulse-ring text-primary dark:text-blue-500"></span>
                    Progress Donasi
                </h2>

                {{-- Progress Bar --}}
                <div class="relative">
                    <div class="w-full bg-gray-100 dark:bg-slate-700 rounded-full h-4 overflow-hidden">
                        <div class="progress-fill h-full rounded-full relative" :style="'width: ' + persentase + '%'"></div>
                    </div>
                    <div class="mt-2 flex justify-between text-xs text-gray-500 dark:text-gray-400">
                        <span x-text="persentase + '%'">{{ $persentase }}%</span>
                        <span>Target: Rp {{ number_format($targetDana, 0, ',', '.') }}</span>
                    </div>
                </div>

                {{-- Stats --}}
                <div class="grid grid-cols-2 gap-4 mt-5">
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-slate-700 dark:to-slate-600 rounded-xl p-4 text-center">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Terkumpul</p>
                        <p class="text-lg font-bold text-primary dark:text-blue-400" x-text="'Rp ' + formatRupiah(totalTerkumpul)">Rp {{ number_format($totalTerkumpul, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-gradient-to-br from-emerald-50 to-teal-50 dark:from-emerald-900/30 dark:to-teal-900/30 rounded-xl p-4 text-center">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Donatur</p>
                        <p class="text-lg font-bold text-emerald-700 dark:text-emerald-400" x-text="jumlahDonatur">{{ $jumlahDonatur }}</p>
                    </div>
                </div>
            </div>

            {{-- Card: Donasi Terbaru --}}
            @if($donasiTerbaru->count() > 0)
            <div class="animate-in-delay-2 bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 shadow-sm p-6 card-hover transition-colors">
                <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4">💝 Donasi Terbaru</h2>
                <div class="space-y-3">
                    @foreach($donasiTerbaru as $d)
                    <div class="flex items-center gap-3 py-2 {{ !$loop->last ? 'border-b border-gray-50 dark:border-slate-700' : '' }}">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-primary to-secondary flex items-center justify-center text-white text-xs font-bold shrink-0">
                            {{ strtoupper(substr($d->nama_donatur, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate">{{ $d->nama_donatur }}</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $d->created_at->diffForHumans() }}</p>
                        </div>
                        <span class="text-sm font-semibold text-primary dark:text-blue-400 whitespace-nowrap">{{ $d->nominal_formatted }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- ═══ KOLOM KANAN: FORM DONASI + MIDTRANS ═══ --}}
        <div class="lg:col-span-2">
            <div class="animate-in-delay-1 bg-white dark:bg-slate-800 rounded-2xl border border-gray-200 dark:border-slate-700 shadow-sm overflow-hidden transition-colors">

                {{-- Header Form --}}
                <div class="bg-gradient-to-r from-[#1F4E79] to-[#2E75B6] p-6">
                    <h2 class="text-xl font-bold text-white flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        Salurkan Donasi Anda
                    </h2>
                    <p class="text-white/70 text-sm mt-1">Setiap rupiah sangat berarti bagi korban bencana</p>
                </div>

                <div class="p-6 lg:p-8">

                    {{-- ═══ STATE: FORM ═══ --}}
                    <div x-show="state === 'form'" x-transition>
                        <form @submit.prevent="submitDonasi()" class="space-y-6">

                            {{-- Nominal Donasi --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Nominal Donasi</label>

                                {{-- Quick Amount Buttons --}}
                                <div class="grid grid-cols-3 sm:grid-cols-6 gap-2 mb-3">
                                    <template x-for="amount in quickAmounts" :key="amount">
                                        <button type="button"
                                                class="amount-btn rounded-xl py-2.5 text-sm font-semibold text-center bg-white dark:bg-slate-800"
                                                :class="{ 'active': nominal == amount }"
                                                @click="setNominal(amount)"
                                                x-text="formatShort(amount)">
                                        </button>
                                    </template>
                                </div>

                                {{-- Custom Amount Input --}}
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500 font-semibold text-sm">Rp</span>
                                    <input type="text" id="input-nominal"
                                           x-model="nominalDisplay"
                                           @input="onNominalInput($event)"
                                           @focus="$event.target.select()"
                                           class="input-focus w-full pl-11 pr-4 py-3.5 border-2 border-gray-200 dark:border-slate-600 bg-transparent rounded-xl text-lg font-semibold text-gray-800 dark:text-white transition"
                                           placeholder="Masukkan nominal lain">
                                </div>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1.5">Minimal donasi: Rp 10.000</p>
                            </div>

                            {{-- Nama & Email (2 columns) --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="input-nama" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Nama Donatur</label>
                                    <input type="text" id="input-nama" x-model="form.nama_donatur"
                                           class="input-focus w-full px-4 py-3 border-2 border-gray-200 dark:border-slate-600 bg-transparent rounded-xl text-sm transition text-gray-900 dark:text-white"
                                           placeholder="Nama lengkap Anda"
                                           @if(auth()->check()) value="{{ auth()->user()->nama_lengkap }}" @endif>
                                </div>
                                <div>
                                    <label for="input-email" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Email</label>
                                    <input type="email" id="input-email" x-model="form.email_donatur"
                                           class="input-focus w-full px-4 py-3 border-2 border-gray-200 dark:border-slate-600 bg-transparent rounded-xl text-sm transition text-gray-900 dark:text-white"
                                           placeholder="email@contoh.com"
                                           @if(auth()->check()) value="{{ auth()->user()->email }}" @endif>
                                </div>
                            </div>

                            {{-- Metode Pembayaran --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Metode Pembayaran</label>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    {{-- Transfer Bank --}}
                                    <label class="relative cursor-pointer">
                                        <input type="radio" name="metode" value="transfer_bank" x-model="form.metode_pembayaran" class="peer sr-only">
                                        <div class="peer-checked:border-primary peer-checked:bg-blue-50 dark:peer-checked:bg-slate-700/50 dark:peer-checked:border-blue-500 border-2 border-gray-200 dark:border-slate-600 rounded-xl p-4 text-center transition hover:border-primary/50 dark:hover:border-blue-500/50">
                                            <div class="w-10 h-10 mx-auto mb-2 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                                                <svg class="w-5 h-5 text-primary dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                                </svg>
                                            </div>
                                            <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Transfer Bank</p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">BCA, BNI, BRI, Permata</p>
                                        </div>
                                    </label>

                                    {{-- E-Wallet --}}
                                    <label class="relative cursor-pointer">
                                        <input type="radio" name="metode" value="e_wallet" x-model="form.metode_pembayaran" class="peer sr-only">
                                        <div class="peer-checked:border-primary peer-checked:bg-blue-50 dark:peer-checked:bg-slate-700/50 dark:peer-checked:border-blue-500 border-2 border-gray-200 dark:border-slate-600 rounded-xl p-4 text-center transition hover:border-primary/50 dark:hover:border-blue-500/50">
                                            <div class="w-10 h-10 mx-auto mb-2 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center">
                                                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                            <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">E-Wallet</p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">GoPay, ShopeePay</p>
                                        </div>
                                    </label>

                                    {{-- Kartu Kredit --}}
                                    <label class="relative cursor-pointer">
                                        <input type="radio" name="metode" value="kartu_kredit" x-model="form.metode_pembayaran" class="peer sr-only">
                                        <div class="peer-checked:border-primary peer-checked:bg-blue-50 dark:peer-checked:bg-slate-700/50 dark:peer-checked:border-blue-500 border-2 border-gray-200 dark:border-slate-600 rounded-xl p-4 text-center transition hover:border-primary/50 dark:hover:border-blue-500/50">
                                            <div class="w-10 h-10 mx-auto mb-2 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center">
                                                <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                                </svg>
                                            </div>
                                            <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Kartu Kredit</p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Visa, Mastercard</p>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            {{-- Pesan (opsional) --}}
                            <div>
                                <label for="input-pesan" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                                    Pesan <span class="font-normal text-gray-400 dark:text-gray-500">(opsional)</span>
                                </label>
                                <textarea id="input-pesan" x-model="form.pesan" rows="3"
                                          class="input-focus w-full px-4 py-3 border-2 border-gray-200 dark:border-slate-600 bg-transparent rounded-xl text-sm transition resize-none text-gray-900 dark:text-white"
                                          placeholder="Semoga lekas pulih..." maxlength="500"></textarea>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 text-right" x-text="(form.pesan || '').length + '/500'"></p>
                            </div>

                            {{-- Error Message --}}
                            <div x-show="errorMsg" x-cloak
                                 class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800/50 rounded-xl p-4 text-sm text-red-700 dark:text-red-400 flex items-start gap-3">
                                <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span x-text="errorMsg"></span>
                            </div>

                            {{-- Submit Button --}}
                            <button type="submit" id="btn-bayar"
                                    :disabled="loading"
                                    class="w-full py-4 bg-gradient-to-r from-[#1F4E79] to-[#2E75B6] hover:from-[#163859] hover:to-[#1F4E79]
                                           text-white font-bold text-lg rounded-xl transition-all duration-300
                                           shadow-lg shadow-primary/25 hover:shadow-xl hover:shadow-primary/30
                                           disabled:opacity-60 disabled:cursor-not-allowed
                                           flex items-center justify-center gap-3">
                                <template x-if="!loading">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Donasi Sekarang
                                    </span>
                                </template>
                                <template x-if="loading">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                                        </svg>
                                        Memproses...
                                    </span>
                                </template>
                            </button>

                            {{-- Info Keamanan --}}
                            <div class="flex items-center justify-center gap-2 text-xs text-gray-400 dark:text-gray-500 pt-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                Pembayaran aman & terenkripsi melalui <strong class="text-gray-600 dark:text-gray-400 ml-0.5">Midtrans</strong>
                            </div>
                        </form>
                    </div>

                    {{-- ═══ STATE: SUKSES ═══ --}}
                    <div x-show="state === 'success'" x-cloak x-transition class="text-center py-8">
                        <div class="w-20 h-20 mx-auto mb-6 bg-emerald-100 dark:bg-emerald-900/30 rounded-full flex items-center justify-center">
                            <svg class="w-10 h-10 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path class="checkmark-path" stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Terima Kasih! 🎉</h3>
                        <p class="text-gray-500 dark:text-gray-400 mb-2">Donasi Anda telah berhasil diproses.</p>
                        <p class="text-sm text-gray-400 dark:text-gray-500 mb-6">
                            Kode transaksi: <strong class="text-primary dark:text-blue-400" x-text="kodeTransaksi"></strong>
                        </p>
                        <div class="flex flex-col sm:flex-row gap-3 justify-center">
                            <button @click="resetForm()"
                                    class="px-6 py-2.5 bg-primary text-white font-semibold rounded-xl hover:bg-primary-dark transition">
                                Donasi Lagi
                            </button>
                            <a href="/peta"
                               class="px-6 py-2.5 border-2 border-gray-200 dark:border-slate-600 text-gray-700 dark:text-gray-300 font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-800 transition">
                                Kembali ke Peta
                            </a>
                        </div>
                    </div>

                    {{-- ═══ STATE: PENDING ═══ --}}
                    <div x-show="state === 'pending'" x-cloak x-transition class="text-center py-8">
                        <div class="w-20 h-20 mx-auto mb-6 bg-yellow-100 dark:bg-yellow-900/30 rounded-full flex items-center justify-center">
                            <svg class="w-10 h-10 text-yellow-500 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Menunggu Pembayaran</h3>
                        <p class="text-gray-500 dark:text-gray-400 mb-2">Silakan selesaikan pembayaran Anda sesuai instruksi.</p>
                        <p class="text-sm text-gray-400 dark:text-gray-500 mb-6">
                            Kode transaksi: <strong class="text-primary dark:text-blue-400" x-text="kodeTransaksi"></strong>
                        </p>
                            <button @click="checkStatus()"
                                    :disabled="loadingStatus"
                                    class="px-6 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-semibold rounded-xl transition flex items-center justify-center gap-2 shadow-md w-full sm:w-auto">
                                <svg x-show="loadingStatus" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                                </svg>
                                <svg x-show="!loadingStatus" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-2 5a2 2 0 11-4 0 2 2 0 014 0zM9 17h6"/>
                                </svg>
                                Cek Status Pembayaran
                            </button>
                        </div>
                        <div class="mt-2 mb-6">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                STATUS: PENDING
                            </span>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-3 justify-center border-t border-gray-100 dark:border-slate-700/50 pt-6">
                            <button @click="resetForm()"
                                    class="px-6 py-2.5 bg-primary text-white font-semibold rounded-xl hover:bg-primary-dark transition">
                                Donasi Baru
                            </button>
                            <a href="/peta"
                               class="px-6 py-2.5 border-2 border-gray-200 dark:border-slate-600 text-gray-700 dark:text-gray-300 font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-slate-800 transition">
                                Kembali ke Peta
                            </a>
                        </div>
                    </div>

                    {{-- ═══ STATE: ERROR ═══ --}}
                    <div x-show="state === 'error'" x-cloak x-transition class="text-center py-8">
                        <div class="w-20 h-20 mx-auto mb-6 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center">
                            <svg class="w-10 h-10 text-red-500 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Pembayaran Gagal</h3>
                        <p class="text-gray-500 dark:text-gray-400 mb-6">Terjadi kesalahan saat memproses pembayaran Anda. Silakan coba lagi.</p>
                        <button @click="resetForm()"
                                class="px-6 py-2.5 bg-primary text-white font-semibold rounded-xl hover:bg-primary-dark transition">
                            Coba Lagi
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>
    @endif
</div>

{{-- ═══ FOOTER ═══ --}}
<footer class="bg-[#0F2B46] text-white/60 py-8 mt-12">
    <div class="max-w-7xl mx-auto px-4 text-center text-sm">
        <p>&copy; {{ date('Y') }} <strong class="text-white/90">KitaTanggap</strong> — Platform Terpadu Penanganan Bencana Indonesia</p>
        <p class="mt-1">Dibuat dengan ❤️ untuk Indonesia</p>
    </div>
</footer>

{{-- Alpine.js --}}
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<script>
function donasiApp() {
    return {
        // State machine: 'form' | 'success' | 'pending' | 'error'
        state: 'form',

        loading: false,
        errorMsg: '',
        kodeTransaksi: '',

        totalTerkumpul: {{ $totalTerkumpul }},
        persentase: {{ $persentase }},
        jumlahDonatur: {{ $jumlahDonatur }},
        loadingStatus: false,
        loadingSuccess: false,

        // Quick amount options
        quickAmounts: [25000, 50000, 100000, 250000, 500000, 1000000],

        // Form data
        nominal: 0,
        nominalDisplay: '',
        form: {
            nama_donatur: '{{ auth()->check() ? auth()->user()->nama_lengkap : '' }}',
            email_donatur: '{{ auth()->check() ? auth()->user()->email : '' }}',
            metode_pembayaran: 'transfer_bank',
            pesan: '',
        },

        init() {
            // Pre-fill nominal 50k sebagai default
            this.setNominal(50000);
        },

        /**
         * Set nominal dari quick amount button
         */
        setNominal(amount) {
            this.nominal = amount;
            this.nominalDisplay = this.formatRupiah(amount);
        },

        /**
         * Handler saat user mengetik di input nominal
         */
        onNominalInput(event) {
            // Hapus semua non-digit
            let raw = event.target.value.replace(/\D/g, '');
            let num = parseInt(raw, 10) || 0;

            this.nominal = num;
            this.nominalDisplay = num > 0 ? this.formatRupiah(num) : '';
        },

        /**
         * Format angka ke format Rupiah (titik sebagai separator ribuan)
         */
        formatRupiah(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        },

        /**
         * Format angka pendek untuk button (25K, 100K, 1Jt)
         */
        formatShort(amount) {
            if (amount >= 1000000) return (amount / 1000000) + ' Jt';
            if (amount >= 1000) return (amount / 1000) + 'K';
            return amount;
        },

        /**
         * Submit donasi → create order → tampilkan Midtrans Snap popup
         */
        async submitDonasi() {
            this.errorMsg = '';

            // Validasi client-side
            if (this.nominal < 10000) {
                this.errorMsg = 'Nominal donasi minimal Rp 10.000.';
                return;
            }
            if (!this.form.nama_donatur.trim()) {
                this.errorMsg = 'Nama donatur wajib diisi.';
                return;
            }
            if (!this.form.email_donatur.trim()) {
                this.errorMsg = 'Email donatur wajib diisi.';
                return;
            }
            if (!this.form.metode_pembayaran) {
                this.errorMsg = 'Pilih metode pembayaran.';
                return;
            }

            this.loading = true;

            try {
                // 1. Kirim request ke backend untuk create order
                const res = await fetch('/api/donasi/create-order', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        bencana_id: {{ $bencana->id }},
                        nominal: this.nominal,
                        nama_donatur: this.form.nama_donatur,
                        email_donatur: this.form.email_donatur,
                        metode_pembayaran: this.form.metode_pembayaran,
                        pesan: this.form.pesan,
                    }),
                });

                const data = await res.json();

                if (!res.ok) {
                    this.errorMsg = data.message || 'Terjadi kesalahan. Silakan coba lagi.';
                    this.loading = false;
                    return;
                }

                this.kodeTransaksi = data.kode_transaksi;

                // 2. Buka Midtrans Snap popup
                this.loading = false;
                window.snap.pay(data.snap_token, {
                    onSuccess: (result) => {
                        console.log('Payment success:', result);
                        this.state = 'success';
                        // Refresh status untuk update progress bar secara instan
                        this.checkStatus();
                    },
                    onPending: (result) => {
                        console.log('Payment pending:', result);
                        this.state = 'pending';
                    },
                    onError: (result) => {
                        console.error('Payment error:', result);
                        this.state = 'error';
                    },
                    onClose: () => {
                        console.log('Snap popup closed by user');
                        this.state = 'pending';
                    },
                });

            } catch (err) {
                console.error('Create order error:', err);
                this.errorMsg = 'Gagal menghubungi server. Periksa koneksi internet Anda.';
                this.loading = false;
            }
        },

        /**
         * Cek status pembayaran ke server
         */
        async checkStatus() {
            if (!this.kodeTransaksi) return;
            this.loadingStatus = true;
            try {
                const res = await fetch(`/api/donasi/check-status/${this.kodeTransaksi}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                    }
                });
                const data = await res.json();
                if (res.ok) {
                    if (data.status_bayar === 'sukses') {
                        this.state = 'success';
                        this.totalTerkumpul = data.summary.total_terkumpul;
                        this.persentase = data.summary.persentase;
                        this.jumlahDonatur = data.summary.jumlah_donatur;
                    } else if (data.status_bayar === 'gagal') {
                        this.state = 'error';
                    } else {
                        alert('Pembayaran masih pending. Silakan selesaikan pembayaran.');
                    }
                } else {
                    alert(data.message || 'Gagal mengecek status pembayaran.');
                }
            } catch (err) {
                console.error(err);
                alert('Terjadi kesalahan koneksi.');
            } finally {
                this.loadingStatus = false;
            }
        },

        /**
         * Simulasikan pembayaran selesai
         */
        async simulateSuccess() {
            if (!this.kodeTransaksi) return;
            this.loadingSuccess = true;
            try {
                const res = await fetch(`/api/donasi/simulate-success/${this.kodeTransaksi}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                });
                const data = await res.json();
                if (res.ok) {
                    this.state = 'success';
                    this.totalTerkumpul = data.summary.total_terkumpul;
                    this.persentase = data.summary.persentase;
                    this.jumlahDonatur = data.summary.jumlah_donatur;
                } else {
                    alert(data.message || 'Gagal memproses simulasi pembayaran.');
                }
            } catch (err) {
                console.error(err);
                alert('Terjadi kesalahan koneksi.');
            } finally {
                this.loadingSuccess = false;
            }
        },

        /**
         * Reset form ke state awal
         */
        resetForm() {
            this.state = 'form';
            this.errorMsg = '';
            this.kodeTransaksi = '';
            this.setNominal(50000);
            this.form.pesan = '';
        },
    };
}
</script>
</body>
</html>
