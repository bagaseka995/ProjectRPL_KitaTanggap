@auth
<div x-data
     x-init="
        // Watch store state to toggle body class
        $watch('$store.sidebar.isOpen', val => {
            document.body.classList.toggle('sidebar-open', val);
            document.body.classList.toggle('sidebar-collapsed', !val);
        });
        // Initial body class setup
        document.body.classList.toggle('sidebar-open', $store.sidebar.isOpen);
        document.body.classList.toggle('sidebar-collapsed', !$store.sidebar.isOpen);
     "
     class="m-0 p-0">

    <!-- Mobile Backdrop -->
    <div x-show="$store.sidebar.isMobileOpen"
         x-cloak
         @click="$store.sidebar.closeMobile()"
         class="fixed inset-0 bg-black/50 z-40 md:hidden transition-opacity duration-300">
    </div>

    <aside :class="[
                ($store.sidebar.isMobile || $store.sidebar.isOpen) ? 'w-64' : 'w-20',
                $store.sidebar.isMobile ? ($store.sidebar.isMobileOpen ? 'translate-x-0' : '-translate-x-full') : 'translate-x-0'
            ]"
           class="fixed inset-y-0 left-0 md:top-16 z-50 md:z-40 bg-[#1A4166] dark:bg-slate-900 border-r border-[#122e4a] dark:border-slate-800 text-white flex flex-col justify-between transition-all duration-300 shadow-xl md:h-[calc(100vh-4rem)]">
        
        <!-- Top Section: Sidebar Toggle & Header -->
        <div class="p-4 flex items-center justify-between border-b border-[#122e4a] dark:border-slate-800 h-16"
             :class="{ 'justify-between': $store.sidebar.isOpen || $store.sidebar.isMobile, 'justify-center': !$store.sidebar.isOpen && !$store.sidebar.isMobile }">
            <!-- Header Text (Only when open on desktop) -->
            <span x-show="$store.sidebar.isOpen && !$store.sidebar.isMobile"
                  x-transition:enter="transition ease-out duration-200"
                  x-transition:enter-start="opacity-0 transform -translate-x-2"
                  x-transition:enter-end="opacity-100 transform translate-x-0"
                  class="hidden md:inline text-xs font-semibold uppercase tracking-wider text-white/40 select-none">
                Menu Panel
            </span>

            <!-- Logo (Only on Mobile drawer) -->
            <div x-show="$store.sidebar.isMobile" class="flex items-center gap-2 overflow-hidden">
                <div class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </div>
                <span class="font-bold text-base tracking-tight text-white whitespace-nowrap">
                    Kita<span class="text-orange-500">Tanggap</span>
                </span>
            </div>

            <!-- Toggle Button (Desktop only) -->
            <button @click="$store.sidebar.toggle()"
                    class="hidden md:flex p-1.5 rounded-lg hover:bg-white/10 transition text-white/80 hover:text-white focus:outline-none">
                <svg :class="{ 'rotate-180': !$store.sidebar.isOpen }" class="w-4 h-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                </svg>
            </button>
        </div>

        <!-- Middle Section: Navigation links -->
        <nav class="flex-grow py-6 px-3 space-y-1 overflow-y-auto">
            @if(auth()->user()->peran === 'admin')
                <!-- Admin Links -->
                @include('layouts.partials.sidebar-link', [
                    'route' => 'admin.dashboard',
                    'label' => 'Dashboard',
                    'icon' => 'home'
                ])
                @include('layouts.partials.sidebar-link', [
                    'route' => 'admin.relawan.index',
                    'label' => 'Relawan',
                    'icon' => 'users'
                ])
                @include('layouts.partials.sidebar-link', [
                    'route' => 'admin.penugasan.index',
                    'label' => 'Penugasan',
                    'icon' => 'clipboard'
                ])
                @include('layouts.partials.sidebar-link', [
                    'route' => 'admin.laporan-distribusi.index',
                    'label' => 'Laporan',
                    'icon' => 'chart'
                ])
                @include('layouts.partials.sidebar-link', [
                    'route' => 'admin.bencana.index',
                    'label' => 'Bencana',
                    'icon' => 'alert'
                ])
                @include('layouts.partials.sidebar-link', [
                    'route' => 'pengaturan.notifikasi',
                    'label' => 'Pengaturan',
                    'icon' => 'cog'
                ])
            @elseif(auth()->user()->peran === 'relawan')
                <!-- Relawan Links -->
                @include('layouts.partials.sidebar-link', [
                    'route' => 'relawan.dashboard',
                    'label' => 'Dashboard',
                    'icon' => 'home'
                ])
                @include('layouts.partials.sidebar-link', [
                    'route' => 'relawan.profil',
                    'label' => 'Profil',
                    'icon' => 'user'
                ])
                @include('layouts.partials.sidebar-link', [
                    'route' => 'relawan.riwayat.index',
                    'label' => 'Riwayat Misi',
                    'icon' => 'archive'
                ])
                @include('layouts.partials.sidebar-link', [
                    'route' => 'pengaturan.notifikasi',
                    'label' => 'Pengaturan',
                    'icon' => 'cog'
                ])
            @elseif(auth()->user()->peran === 'donatur')
                <!-- Donatur Links -->
                @include('layouts.partials.sidebar-link', [
                    'route' => 'donatur.dashboard',
                    'label' => 'Dashboard',
                    'icon' => 'home'
                ])
                @include('layouts.partials.sidebar-link', [
                    'route' => 'donatur.riwayat',
                    'label' => 'Riwayat Donasi',
                    'icon' => 'credit-card'
                ])
                @include('layouts.partials.sidebar-link', [
                    'route' => 'pengaturan.notifikasi',
                    'label' => 'Pengaturan',
                    'icon' => 'cog'
                ])
            @endif
        </nav>

        <!-- Bottom Section: User Info & Logout -->
        <div class="p-3 border-t border-[#122e4a] dark:border-slate-800 space-y-2">
            <!-- User summary (Expanded only) -->
            <div x-show="$store.sidebar.isOpen || $store.sidebar.isMobile" class="px-3 py-2 bg-white/5 rounded-xl flex items-center gap-3 overflow-hidden">
                <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-xs font-bold uppercase text-white flex-shrink-0">
                    {{ substr(auth()->user()->nama_lengkap, 0, 2) }}
                </div>
                <div class="truncate">
                    <div class="text-xs font-bold text-white truncate text-left">{{ auth()->user()->nama_lengkap }}</div>
                    <div class="text-[10px] text-white/50 uppercase tracking-wider font-semibold text-left">{{ auth()->user()->peran }}</div>
                </div>
            </div>

            <!-- Logout Form -->
            <form method="POST" action="{{ route('logout') }}" class="m-0 p-0">
                @csrf
                <button type="submit"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold tracking-wide transition bg-red-600/10 hover:bg-red-600 text-red-200 hover:text-white border border-red-500/10 hover:border-red-600 focus:outline-none"
                        :class="{ 'justify-center': !$store.sidebar.isOpen && !$store.sidebar.isMobile }"
                        title="Keluar">
                    <!-- Logout Icon -->
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span x-show="$store.sidebar.isOpen || $store.sidebar.isMobile" class="truncate">Keluar</span>
                </button>
            </form>
        </div>
    </aside>
</div>

<!-- Styles for Sidebar Page Transitions -->
<style>
    @media (min-width: 768px) {
        body.sidebar-open {
            padding-left: 16rem !important; /* w-64 */
        }
        body.sidebar-collapsed {
            padding-left: 5rem !important; /* w-20 */
        }
        
        /* Memastikan sidebar berada di bawah top navbar pada desktop */
        aside {
            top: 4rem !important;
            height: calc(100vh - 4rem) !important;
            z-index: 40 !important;
        }
    }
    
    /* Memastikan top navbar selalu lebar penuh dan tidak tergeser oleh padding body */
    nav.fixed {
        left: 0 !important;
        width: 100vw !important;
    }
    
    body {
        transition: padding-left 0.3s ease-in-out;
    }
</style>
@endauth
