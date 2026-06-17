<!DOCTYPE html>
<html lang="id" class="scroll-smooth" x-data="themeHandler()" :class="{ 'dark': isDark }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KitaTanggap — Platform Sinergi Penanganan Bencana</title>
    <meta name="description" content="Platform terpadu penanganan bencana di Indonesia. Menyatukan donatur, relawan, dan pemerintah untuk aksi nyata.">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS (CDN for Development) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        heading: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f0f7ff',
                            100: '#e0f0fe',
                            200: '#b9e0fe',
                            300: '#7cc5fd',
                            400: '#36a7fa',
                            500: '#0c8de6',
                            600: '#2E75B6', // Secondary
                            700: '#1F4E79', // Primary
                            800: '#1a4166',
                            900: '#183755',
                        },
                        accent: {
                            500: '#E28743',
                            600: '#C55A11', // Main Accent
                        }
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'fade-up': 'fadeUp 0.8s ease-out forwards',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-20px)' },
                        },
                        fadeUp: {
                            '0%': { opacity: '0', transform: 'translateY(30px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        }
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        .glass-nav {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
        }
        .dark .glass-nav {
            background: rgba(15, 23, 42, 0.8);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .hero-gradient {
            background: linear-gradient(135deg, #1F4E79 0%, #2E75B6 50%, #1a4166 100%);
        }
        .text-gradient {
            background: linear-gradient(to right, #C55A11, #E28743);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>

    <!-- Firebase SDK (REQ-26) -->
    <script src="https://www.gstatic.com/firebasejs/9.6.1/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.6.1/firebase-messaging-compat.js"></script>
    <script src="{{ asset('js/firebase-messaging.js') }}" defer></script>
    
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
<body class="font-sans antialiased text-gray-800 dark:text-gray-100 bg-gray-50 dark:bg-slate-900 transition-colors duration-300 selection:bg-brand-500 selection:text-white">

    <!-- Navbar -->
    @include('layouts.partials.navbar-main')
    @include('layouts.partials.navbar-sub')

    <!-- Hero Section -->
    <section id="beranda" class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden hero-gradient">
        <!-- Abstract Shapes -->
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
            <div class="absolute -top-[10%] -right-[10%] w-[50%] h-[50%] rounded-full bg-brand-400/20 blur-3xl mix-blend-overlay"></div>
            <div class="absolute bottom-[10%] -left-[10%] w-[40%] h-[40%] rounded-full bg-accent-500/20 blur-3xl mix-blend-overlay"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-8 items-center">
                <!-- Text Content -->
                <div class="text-center lg:text-left opacity-0 animate-fade-up">
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/10 backdrop-blur-sm border border-white/20 text-white text-sm font-medium mb-6">
                        <span class="w-2 h-2 rounded-full bg-accent-500 animate-pulse"></span>
                        Bersama Membangun Harapan
                    </div>
                    <h1 class="font-heading text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white leading-tight mb-6">
                        Sinergi Nyata Untuk <br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-accent-500 to-yellow-400">Penanganan Bencana</span>
                    </h1>
                    <p class="text-lg text-blue-100 mb-10 max-w-2xl mx-auto lg:mx-0 leading-relaxed">
                        Platform terpadu yang menghubungkan donatur dermawan dan relawan tangguh langsung ke lokasi bencana dengan transparansi 100%. Setiap aksi Anda menyelamatkan kehidupan.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        <a href="{{ route('transparansi') }}" class="px-8 py-4 bg-accent-600 hover:bg-accent-500 text-white font-semibold rounded-2xl shadow-lg shadow-accent-600/30 transition duration-300 flex items-center justify-center gap-2">
                            Salurkan Donasi
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                        <a href="{{ route('register') }}" class="px-8 py-4 bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/20 text-white font-semibold rounded-2xl transition duration-300 flex items-center justify-center">
                            Gabung Relawan
                        </a>
                    </div>
                </div>

                <!-- Hero Image -->
                <div class="relative opacity-0 animate-fade-up" style="animation-delay: 0.2s;">
                    <div class="relative rounded-3xl overflow-hidden shadow-2xl shadow-black/40 aspect-[4/3] border border-white/10 transform transition hover:scale-[1.02] duration-500">
                        <img src="{{ asset('images/hero.png') }}" alt="Relawan KitaTanggap" class="object-cover w-full h-full">
                        <div class="absolute inset-0 bg-gradient-to-t from-brand-900/80 via-transparent to-transparent"></div>
                        
                        <!-- Floating Badge -->
                        <div class="absolute bottom-6 left-6 right-6 bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-4 flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-accent-500 rounded-full flex items-center justify-center text-white">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                </div>
                                <div>
                                    <p class="text-white font-bold text-lg">10.000+</p>
                                    <p class="text-blue-100 text-sm">Relawan Aktif</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Logo Cloud / Stats -->
    <section class="py-10 bg-white dark:bg-slate-900 border-b border-gray-100 dark:border-slate-800 transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center divide-x divide-gray-100 dark:divide-slate-800">
                <div class="p-4">
                    <p class="text-3xl font-heading font-bold text-brand-700 dark:text-brand-500 mb-1">Rp 12M+</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Donasi Tersalurkan</p>
                </div>
                <div class="p-4">
                    <p class="text-3xl font-heading font-bold text-brand-700 dark:text-brand-500 mb-1">50+</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Daerah Terbantu</p>
                </div>
                <div class="p-4">
                    <p class="text-3xl font-heading font-bold text-brand-700 dark:text-brand-500 mb-1">100%</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Transparansi Dana</p>
                </div>
                <div class="p-4">
                    <p class="text-3xl font-heading font-bold text-brand-700 dark:text-brand-500 mb-1">24/7</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Pemantauan Bencana</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-24 bg-gray-50 dark:bg-slate-900 transition-colors relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-brand-600 dark:text-brand-400 font-semibold tracking-wide uppercase text-sm mb-3">Kenapa KitaTanggap?</h2>
                <h3 class="font-heading text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-6">Satu Platform, Berjuta Kebaikan</h3>
                <p class="text-gray-600 dark:text-gray-400 text-lg">Kami mendesain ekosistem penanganan bencana yang aman, cepat, dan sepenuhnya transparan untuk memastikan bantuan Anda tepat sasaran.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 lg:gap-16 items-center mb-24">
                <div class="order-2 md:order-1 relative">
                    <div class="absolute -inset-4 bg-brand-100/50 dark:bg-brand-900/30 rounded-3xl transform -rotate-3 transition duration-500 hover:rotate-0"></div>
                    <img src="{{ asset('images/donation.png') }}" alt="Donasi Digital" class="relative rounded-3xl shadow-xl shadow-brand-900/10 dark:shadow-black/50 w-full object-cover aspect-square md:aspect-[4/3] bg-white dark:bg-slate-800">
                </div>
                <div class="order-1 md:order-2">
                    <div class="w-14 h-14 bg-brand-100 dark:bg-brand-900/50 rounded-2xl flex items-center justify-center text-brand-600 dark:text-brand-400 mb-6">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="font-heading text-3xl font-bold text-gray-900 dark:text-white mb-4">Donasi Digital Instan & Transparan</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-lg mb-6 leading-relaxed">
                        Kirimkan bantuan dana secara instan menggunakan berbagai metode pembayaran digital (Midtrans). Anda bisa melacak penggunaan donasi Anda secara publik melalui Laporan Distribusi kami.
                    </p>
                    <ul class="space-y-4">
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span class="text-gray-700 dark:text-gray-300">Dukungan E-Wallet (GoPay, OVO), VA, & Kartu Kredit.</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span class="text-gray-700 dark:text-gray-300">Laporan pertanggungjawaban dana publik real-time.</span>
                        </li>
                    </ul>
                    <a href="{{ route('transparansi') }}" class="mt-8 inline-flex items-center text-brand-600 dark:text-brand-400 font-semibold hover:text-brand-800 dark:hover:text-brand-300 transition">
                        Lihat Laporan Transparansi <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 lg:gap-16 items-center">
                <div>
                    <div class="w-14 h-14 bg-accent-100 dark:bg-accent-900/50 rounded-2xl flex items-center justify-center text-accent-600 dark:text-accent-400 mb-6">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <h3 class="font-heading text-3xl font-bold text-gray-900 dark:text-white mb-4">Manajemen Relawan Terpusat</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-lg mb-6 leading-relaxed">
                        Kami menjaring relawan berkompetensi, mengatur penugasan ke area terdampak yang tepat, dan menerbitkan e-sertifikat terverifikasi atas kontribusi nyata mereka.
                    </p>
                    <ul class="space-y-4">
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span class="text-gray-700 dark:text-gray-300">Verifikasi keterampilan (Medis, Logistik, Evakuasi).</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span class="text-gray-700 dark:text-gray-300">Sertifikat digital dengan QR Code yang valid secara publik.</span>
                        </li>
                    </ul>
                    <a href="{{ route('register') }}" class="mt-8 inline-flex items-center text-accent-600 dark:text-accent-400 font-semibold hover:text-accent-700 dark:hover:text-accent-300 transition">
                        Daftar Sebagai Relawan <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
                <div class="relative">
                    <div class="absolute -inset-4 bg-accent-100/50 dark:bg-accent-900/30 rounded-3xl transform rotate-3 transition duration-500 hover:rotate-0"></div>
                    <img src="{{ asset('images/volunteer.png') }}" alt="Relawan Komunitas" class="relative rounded-3xl shadow-xl shadow-accent-900/10 dark:shadow-black/50 w-full object-cover aspect-square md:aspect-[4/3] bg-white dark:bg-slate-800">
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 relative overflow-hidden">
        <div class="absolute inset-0 hero-gradient"></div>
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <h2 class="font-heading text-3xl md:text-5xl font-bold text-white mb-6">Satu Tindakan Kecil, <br>Perubahan Besar.</h2>
            <p class="text-xl text-blue-100 mb-10 max-w-2xl mx-auto">
                Bantu ringankan beban mereka yang tertimpa musibah hari ini. Donasi Anda dikelola dengan penuh tanggung jawab.
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="{{ route('transparansi') }}" class="px-8 py-4 bg-white text-brand-700 hover:text-brand-900 font-bold rounded-2xl shadow-xl hover:shadow-2xl transition duration-300 transform hover:-translate-y-1">
                    Bantu Sekarang
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300 py-12 border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12">
                <div class="md:col-span-2">
                    <div class="flex items-center gap-2 mb-6">
                        <div class="w-8 h-8 bg-brand-600 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </div>
                        <span class="font-heading font-bold text-xl text-white tracking-tight">Kita<span class="text-accent-500">Tanggap</span></span>
                    </div>
                    <p class="text-gray-400 max-w-md leading-relaxed">
                        Sistem Informasi Penanganan Bencana terpadu yang memfasilitasi penggalangan dana transparan dan manajemen relawan profesional di seluruh Indonesia.
                    </p>
                </div>
                
                <div>
                    <h4 class="text-white font-semibold mb-6">Pintasan Akses</h4>
                    <ul class="space-y-4">
                        <li><a href="{{ route('transparansi') }}" class="hover:text-brand-400 transition">Transparansi Dana</a></li>
                        <li><a href="{{ route('peta') }}" class="hover:text-brand-400 transition">Peta Bencana</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-brand-400 transition">Portal Login</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-white font-semibold mb-6">Bantuan & Kontak</h4>
                    <ul class="space-y-4">
                        <li class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            bantuan@kitatanggap.id
                        </li>
                        <li class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            1500-111 (Hotline 24/7)
                        </li>
                    </ul>
                </div>
            </div>
            <div class="mt-12 pt-8 border-t border-gray-800 text-sm text-gray-500 flex flex-col md:flex-row justify-between items-center gap-4">
                <p>&copy; {{ date('Y') }} KitaTanggap Kelompok 11 RPL. All rights reserved.</p>
                <div class="flex space-x-6">
                    <a href="#" class="hover:text-white transition">Kebijakan Privasi</a>
                    <a href="#" class="hover:text-white transition">Syarat & Ketentuan</a>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
