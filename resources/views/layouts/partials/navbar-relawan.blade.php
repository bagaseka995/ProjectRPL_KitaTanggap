<nav x-data="{ mobileMenuOpen: false }" class="bg-[#1F4E79] dark:bg-slate-900 text-white shadow-lg sticky top-0 z-50 transition-colors duration-300 border-b border-transparent dark:border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex-shrink-0 flex items-center gap-3">
                <a href="/" class="flex items-center gap-2 group">
                    <div class="w-10 h-10 bg-white/10 dark:bg-slate-800/50 rounded-xl flex items-center justify-center backdrop-blur-sm group-hover:bg-white/20 dark:group-hover:bg-slate-700/50 transition">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    </div>
                    <span class="font-bold text-xl tracking-tight hidden sm:block">Kita<span class="text-blue-300">Tanggap</span></span>
                </a>
                <span class="px-2.5 py-1 bg-blue-800/50 dark:bg-blue-900/40 rounded-lg text-xs font-semibold tracking-widest text-blue-200 border border-blue-700 dark:border-blue-800/50">RELAWAN</span>
            </div>

            <!-- Desktop Menu -->
            <div class="hidden md:flex items-center space-x-1">
                <a href="/" class="px-3 py-2 rounded-lg text-sm font-medium hover:bg-white/10 dark:hover:bg-slate-800 transition">Beranda</a>
                <a href="{{ route('relawan.dashboard') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('relawan.dashboard') ? 'bg-white/10 dark:bg-slate-800' : 'hover:bg-white/10 dark:hover:bg-slate-800' }} transition">Dashboard</a>
                @if(!request()->routeIs('relawan.dashboard'))
                    <a href="{{ route('relawan.profil') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('relawan.profil') ? 'bg-white/10 dark:bg-slate-800' : 'hover:bg-white/10 dark:hover:bg-slate-800' }} transition">Profil</a>
                    <a href="{{ route('relawan.riwayat.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('relawan.riwayat.*') ? 'bg-white/10 dark:bg-slate-800' : 'hover:bg-white/10 dark:hover:bg-slate-800' }} transition">Riwayat Misi</a>
                @endif
                <div class="h-5 w-px bg-white/20 mx-2"></div>
                <a href="{{ route('pengaturan.notifikasi') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('pengaturan.*') ? 'bg-white/10 dark:bg-slate-800' : 'hover:bg-white/10 dark:hover:bg-slate-800' }} transition">Pengaturan</a>
                
                {{-- Theme Toggle Button --}}
                <button @click="toggleTheme()" type="button" class="ml-1 p-2 rounded-lg text-white/80 hover:text-white hover:bg-white/10 dark:hover:bg-slate-800 transition focus:outline-none" title="Toggle Dark Mode">
                    <svg x-show="isDark" x-cloak class="w-5 h-5 text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <svg x-show="!isDark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </button>

                <form method="POST" action="{{ route('logout') }}" class="m-0 p-0 ml-2">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-red-500/20 hover:bg-red-500 dark:bg-red-500/10 dark:hover:bg-red-600 hover:text-white text-red-100 rounded-lg text-sm font-semibold transition border border-red-500/30 dark:border-red-500/20">
                        Keluar
                    </button>
                </form>
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
            <a href="{{ route('relawan.dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('relawan.dashboard') ? 'bg-white/10 dark:bg-slate-800' : 'hover:bg-white/10 dark:hover:bg-slate-800' }} transition">Dashboard</a>
            @if(!request()->routeIs('relawan.dashboard'))
                <a href="{{ route('relawan.profil') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('relawan.profil') ? 'bg-white/10 dark:bg-slate-800' : 'hover:bg-white/10 dark:hover:bg-slate-800' }} transition">Profil</a>
                <a href="{{ route('relawan.riwayat.index') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('relawan.riwayat.*') ? 'bg-white/10 dark:bg-slate-800' : 'hover:bg-white/10 dark:hover:bg-slate-800' }} transition">Riwayat Misi</a>
            @endif
            <a href="{{ route('pengaturan.notifikasi') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('pengaturan.*') ? 'bg-white/10 dark:bg-slate-800' : 'hover:bg-white/10 dark:hover:bg-slate-800' }} transition">Pengaturan</a>
            <form method="POST" action="{{ route('logout') }}" class="block w-full">
                @csrf
                <button type="submit" class="w-full text-left px-3 py-2 rounded-md text-base font-medium text-red-300 hover:bg-red-500/20 transition">
                    Keluar
                </button>
            </form>
        </div>
    </div>
</nav>
