<nav x-data="{ mobileMenuOpen: false }" class="bg-[#1F4E79] dark:bg-slate-900 text-white shadow-lg fixed top-0 left-0 w-full z-50 transition-colors duration-300 border-b border-transparent dark:border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex-shrink-0 flex items-center gap-3">
                @auth
                    <button @click="$store.sidebar.toggleMobile()" class="p-2 -ml-2 rounded-lg text-white hover:bg-white/10 md:hidden focus:outline-none" title="Toggle Menu">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <!-- Ikon layout panel untuk membedakan dari hamburger menu utama di kanan -->
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 01-1-1V5zm5 0v14" />
                        </svg>
                    </button>
                @endauth
                <a href="/" class="flex items-center gap-2 group">
                    <div class="w-10 h-10 bg-white/10 dark:bg-slate-800/50 rounded-xl flex items-center justify-center backdrop-blur-sm group-hover:bg-white/20 dark:group-hover:bg-slate-700/50 transition">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    </div>
                    <span class="font-bold text-xl tracking-tight hidden sm:block text-white">Kita<span class="text-orange-500">Tanggap</span></span>
                </a>
            </div>

            <!-- Desktop Menu -->
            <div class="hidden md:flex items-center space-x-6">
                <a href="/" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->is('/') ? 'text-white font-semibold' : 'text-white/80 hover:text-white' }} transition">Beranda</a>
                <a href="{{ route('transparansi') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('transparansi') ? 'text-white font-semibold' : 'text-white/80 hover:text-white' }} transition">Transparansi</a>
                <a href="{{ route('peta') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('peta') ? 'text-white font-semibold' : 'text-white/80 hover:text-white' }} transition">Peta Bencana</a>
                
                <div class="h-5 w-px bg-white/20 mx-1"></div>

                @auth
                    <a href="{{ route('dashboard') }}" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-full transition shadow-sm">
                        Dashboard Saya
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-white/80 hover:text-white font-medium text-sm transition">Masuk</a>
                    <a href="{{ route('register') }}" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-full transition shadow-sm text-sm">
                        Daftar
                    </a>
                @endauth
                
                {{-- Theme Toggle Button --}}
                <button @click="toggleTheme()" type="button" class="ml-1 p-2 rounded-lg text-white/80 hover:text-white hover:bg-white/10 dark:hover:bg-slate-800 transition focus:outline-none" title="Toggle Dark Mode">
                    <svg x-show="isDark" x-cloak class="w-5 h-5 text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <svg x-show="!isDark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </button>
            </div>

            <!-- Mobile menu button -->
            <div class="md:hidden flex items-center gap-2">
                <button @click="toggleTheme()" type="button" class="p-2 rounded-md text-blue-100 hover:text-white hover:bg-blue-800 dark:hover:bg-slate-800 focus:outline-none transition">
                    <svg x-show="isDark" x-cloak class="w-5 h-5 text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <svg x-show="!isDark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </button>
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2 rounded-md text-blue-100 hover:text-white hover:bg-blue-800 dark:hover:bg-slate-800 focus:outline-none transition">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path x-show="mobileMenuOpen" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div x-show="mobileMenuOpen" x-collapse x-cloak class="md:hidden border-t border-blue-800/50 dark:border-slate-800 bg-[#163859] dark:bg-slate-900">
        <div class="px-2 pt-2 pb-3 space-y-1">
            <a href="/" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-white/10 dark:hover:bg-slate-800 transition">Beranda</a>
            <a href="{{ route('transparansi') }}" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-white/10 dark:hover:bg-slate-800 transition">Transparansi</a>
            <a href="{{ route('peta') }}" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-white/10 dark:hover:bg-slate-800 transition">Peta Bencana</a>
            @auth
                <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-white/10 dark:hover:bg-slate-800 transition text-blue-300 font-semibold">Dashboard Saya</a>
                <form method="POST" action="{{ route('logout') }}" class="block w-full">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2 rounded-md text-base font-medium text-red-300 hover:bg-red-500/20 transition">
                        Keluar
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-white/10 dark:hover:bg-slate-800 transition">Masuk</a>
                <a href="{{ route('register') }}" class="block px-3 py-2 rounded-md text-base font-medium hover:bg-white/10 dark:hover:bg-slate-800 transition text-blue-300 font-semibold">Daftar</a>
            @endauth
        </div>
    </div>
</nav>

<script>
    document.body.style.paddingTop = '4rem';
    document.addEventListener('alpine:init', () => {
        if (!Alpine.store('sidebar')) {
            Alpine.store('sidebar', {
                isOpen: localStorage.getItem('sidebarOpen') !== 'false',
                isMobileOpen: false,
                isMobile: window.innerWidth < 768,
                toggle() {
                    this.isOpen = !this.isOpen;
                    localStorage.setItem('sidebarOpen', this.isOpen ? 'true' : 'false');
                },
                toggleMobile() {
                    this.isMobileOpen = !this.isMobileOpen;
                },
                closeMobile() {
                    this.isMobileOpen = false;
                }
            });

            window.addEventListener('resize', () => {
                Alpine.store('sidebar').isMobile = window.innerWidth < 768;
            });
        }
    });
</script>
