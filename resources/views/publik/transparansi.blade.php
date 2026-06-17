<!DOCTYPE html>
<html lang="id" x-data="themeHandler()" :class="{ 'dark': isDark }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transparansi Donasi — KitaTanggap</title>
    <meta name="description" content="Laporan distribusi donasi publik KitaTanggap — lihat bagaimana dana Anda disalurkan untuk membantu korban bencana di Indonesia.">

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
                },
                fontFamily: { sans: ['Inter','Arial','sans-serif'] }
            }}
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Inter', Arial, sans-serif; }
        [x-cloak] { display: none !important; }

        @keyframes fillBar { from { width: 0%; } }
        .progress-fill {
            animation: fillBar 1s ease-out forwards;
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
            background: linear-gradient(90deg, transparent, rgba(255,255,255,.25), transparent);
            background-size: 200% 100%;
            animation: shimmer 2.5s infinite;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .animate-in { animation: fadeInUp .5s ease both; }
        .stagger-1 { animation-delay: .05s; }
        .stagger-2 { animation-delay: .1s; }
        .stagger-3 { animation-delay: .15s; }
        .stagger-4 { animation-delay: .2s; }

        .card-hover { transition: all .3s ease; }
        .card-hover:hover { transform: translateY(-3px); box-shadow: 0 12px 40px rgba(31,78,121,.12); }

        .timeline-line {
            position: absolute; left: 15px; top: 32px; bottom: 0;
            width: 2px; background: linear-gradient(to bottom, #2E75B6, #E5E7EB);
        }
        .timeline-dot {
            position: absolute; left: 8px; top: 6px;
            width: 16px; height: 16px; border-radius: 50%;
            background: #2E75B6; border: 3px solid #ffffff;
            box-shadow: 0 0 0 2px #2E75B6;
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

{{-- ═══ HERO HEADER ═══ --}}
<div class="bg-gradient-to-r from-[#1F4E79] via-[#2E75B6] to-[#3B82F6] text-white py-12 relative overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-0 right-0 w-96 h-96 bg-white rounded-full -translate-y-1/2 translate-x-1/3"></div>
        <div class="absolute bottom-0 left-0 w-72 h-72 bg-white rounded-full translate-y-1/2 -translate-x-1/4"></div>
    </div>
    <div class="max-w-7xl mx-auto px-4 relative z-10">
        <div class="text-center">
            <h1 class="text-3xl md:text-4xl font-extrabold mb-3 animate-in">Transparansi Donasi</h1>
            <p class="text-white/80 text-lg max-w-2xl mx-auto animate-in stagger-1">
                Kami berkomitmen untuk transparansi penuh. Lihat bagaimana setiap rupiah donasi Anda disalurkan untuk membantu korban bencana.
            </p>
        </div>

        {{-- Statistik Global --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-8">
            <div class="animate-in stagger-1 bg-white/10 backdrop-blur-sm rounded-2xl p-5 text-center border border-white/10">
                <p class="text-white/60 text-xs font-medium uppercase tracking-wider mb-1">Total Dana Masuk</p>
                <p class="text-2xl font-extrabold">Rp {{ number_format($totalDanaGlobal, 0, ',', '.') }}</p>
            </div>
            <div class="animate-in stagger-2 bg-white/10 backdrop-blur-sm rounded-2xl p-5 text-center border border-white/10">
                <p class="text-white/60 text-xs font-medium uppercase tracking-wider mb-1">Bencana Tercatat</p>
                <p class="text-2xl font-extrabold">{{ $totalBencana }}</p>
            </div>
            <div class="animate-in stagger-3 bg-white/10 backdrop-blur-sm rounded-2xl p-5 text-center border border-white/10">
                <p class="text-white/60 text-xs font-medium uppercase tracking-wider mb-1">Total Donatur</p>
                <p class="text-2xl font-extrabold">{{ $totalDonaturGlobal }}</p>
            </div>
            <div class="animate-in stagger-4 bg-white/10 backdrop-blur-sm rounded-2xl p-5 text-center border border-white/10">
                <p class="text-white/60 text-xs font-medium uppercase tracking-wider mb-1">Laporan Distribusi</p>
                <p class="text-2xl font-extrabold">{{ $totalLaporan }}</p>
            </div>
        </div>
    </div>
</div>

{{-- ═══ KONTEN UTAMA ═══ --}}
<div class="max-w-7xl mx-auto px-4 py-10" x-data="{ openId: null }">

    {{-- Empty State --}}
    @if($bencanaList->isEmpty())
    <div class="text-center py-20 text-gray-400">
        <svg class="w-16 h-16 mx-auto mb-4 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <p class="text-lg font-medium">Belum ada data bencana.</p>
        <p class="text-sm mt-1">Data akan muncul setelah admin mencatat kejadian bencana.</p>
    </div>
    @endif

    {{-- Daftar Bencana --}}
    <div class="space-y-6">
        @foreach($bencanaList as $idx => $b)
        <div class="animate-in bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden card-hover"
             style="animation-delay: {{ $idx * 0.05 }}s">

            {{-- Card Header: klik untuk expand --}}
            <div class="cursor-pointer" @click="openId = openId === {{ $b->id }} ? null : {{ $b->id }}">
                <div class="p-6">
                    <div class="flex flex-col lg:flex-row lg:items-center gap-5">

                        {{-- Info Bencana --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-2 flex-wrap">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wide text-white"
                                      style="background-color: {{ $b->warna_siaga }}">
                                    {{ $b->label_siaga }}
                                </span>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600 capitalize">
                                    {{ str_replace('_', ' ', $b->jenis_bencana) }}
                                </span>
                                @if(!$b->status_aktif)
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-800 text-white">
                                    Selesai
                                </span>
                                @else
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                    Aktif
                                </span>
                                @endif
                            </div>
                            <h2 class="text-lg font-bold text-gray-900">{{ $b->nama_bencana }}</h2>
                            <div class="flex items-center gap-4 mt-1 text-sm text-gray-500">
                                <span class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    </svg>
                                    {{ $b->lokasi }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $b->tanggal_kejadian?->format('d M Y') }}
                                </span>
                            </div>
                        </div>

                        {{-- Progress + Stats --}}
                        <div class="lg:w-80 shrink-0">
                            {{-- Progress Bar --}}
                            <div class="mb-3">
                                <div class="flex justify-between text-xs text-gray-500 mb-1.5">
                                    <span class="font-semibold text-primary">{{ $b->persentase }}% tercapai</span>
                                    <span>Target: Rp {{ number_format($b->target_dana, 0, ',', '.') }}</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">
                                    <div class="progress-fill h-full rounded-full relative" style="width: {{ $b->persentase }}%"></div>
                                </div>
                            </div>

                            {{-- Mini Stats --}}
                            <div class="grid grid-cols-3 gap-2">
                                <div class="bg-blue-50 rounded-lg p-2 text-center">
                                    <p class="text-[10px] text-gray-500 uppercase tracking-wider font-medium">Terkumpul</p>
                                    <p class="text-xs font-bold text-primary mt-0.5">Rp {{ number_format($b->total_terkumpul, 0, ',', '.') }}</p>
                                </div>
                                <div class="bg-emerald-50 rounded-lg p-2 text-center">
                                    <p class="text-[10px] text-gray-500 uppercase tracking-wider font-medium">Disalurkan</p>
                                    <p class="text-xs font-bold text-emerald-700 mt-0.5">Rp {{ number_format($b->total_disalurkan, 0, ',', '.') }}</p>
                                </div>
                                <div class="bg-amber-50 rounded-lg p-2 text-center">
                                    <p class="text-[10px] text-gray-500 uppercase tracking-wider font-medium">Donatur</p>
                                    <p class="text-xs font-bold text-amber-700 mt-0.5">{{ $b->jumlah_donatur }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Expand Arrow --}}
                        <div class="hidden lg:flex items-center">
                            <svg class="w-5 h-5 text-gray-400 transition-transform duration-300"
                                 :class="{ 'rotate-180': openId === {{ $b->id }} }"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Expanded: Detail Distribusi --}}
            <div x-show="openId === {{ $b->id }}" x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="border-t border-gray-100">

                <div class="p-6 bg-gradient-to-b from-slate-50/50 to-white">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-8 h-8 bg-primary/10 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800">Laporan Distribusi Dana</h3>
                            <p class="text-xs text-gray-500">Rincian penggunaan dana donasi yang telah disalurkan</p>
                        </div>
                    </div>

                    @if($b->laporan->isEmpty())
                        <div class="text-center py-8 text-gray-400">
                            <svg class="w-10 h-10 mx-auto mb-2 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-sm font-medium">Belum ada laporan distribusi</p>
                            <p class="text-xs mt-0.5">Laporan akan diperbarui setelah dana didistribusikan</p>
                        </div>
                    @else
                        {{-- Timeline Laporan --}}
                        <div class="space-y-6 relative ml-4">
                            @foreach($b->laporan as $lap)
                            <div class="relative pl-10">
                                {{-- Timeline Elements --}}
                                @if(!$loop->last)
                                <div class="timeline-line"></div>
                                @endif
                                <div class="timeline-dot"></div>

                                {{-- Laporan Card --}}
                                <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm">
                                    <div class="flex items-center justify-between mb-3">
                                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                            {{ $lap->tanggal_laporan?->format('d M Y') }}
                                        </span>
                                        <span class="text-sm font-bold text-primary">
                                            {{ $lap->jumlah_formatted }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-700 leading-relaxed">
                                        {{ $lap->rincian_penggunaan }}
                                    </p>
                                    @if($lap->bukti_distribusi)
                                    <div class="mt-3 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <a href="{{ $lap->bukti_url }}" target="_blank"
                                           class="text-xs font-semibold text-primary hover:underline">
                                            Lihat Bukti Distribusi →
                                        </a>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Tombol Donasi --}}
                    @if($b->status_aktif)
                    <div class="mt-6 text-center">
                        <a href="{{ route('donasi.show', $b->id) }}"
                           class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-[#1F4E79] to-[#2E75B6] text-white font-semibold rounded-xl hover:from-[#163859] hover:to-[#1F4E79] transition-all shadow-md hover:shadow-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                            Donasi Sekarang
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- ═══ FOOTER ═══ --}}
<footer class="bg-[#0F2B46] text-white/60 py-8 mt-8">
    <div class="max-w-7xl mx-auto px-4 text-center text-sm">
        <p>&copy; {{ date('Y') }} <strong class="text-white/90">KitaTanggap</strong> — Platform Terpadu Penanganan Bencana Indonesia</p>
        <p class="mt-1">Transparansi adalah komitmen kami 🤝</p>
    </div>
</footer>

</body>
</html>
