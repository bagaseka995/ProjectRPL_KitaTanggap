<!DOCTYPE html>
<html lang="id" x-data="themeHandler()" :class="{ 'dark': isDark }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'KitaTanggap') — Sistem Informasi Penanganan Bencana</title>
    <meta name="description" content="@yield('meta_description', 'Platform terpadu penanganan bencana di Indonesia — Informasi bencana real-time, manajemen relawan, dan donasi transparan.')">

    {{-- Google Fonts: Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Tailwind CSS CDN (dev) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary:   { DEFAULT: '#1F4E79', light: '#2E75B6', dark: '#163859' },
                        secondary: '#2E75B6',
                        accent:    '#C55A11',
                        danger:    '#C0392B',
                        warning:   '#F5C518',
                    },
                    fontFamily: {
                        sans: ['Inter', 'Arial', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', Arial, sans-serif; }

        /* ─── Animations ─── */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }
        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(20px); }
            to   { opacity: 1; transform: translateX(0); }
        }
        .animate-fade-up    { animation: fadeInUp .5s ease both; }
        .animate-fade-in    { animation: fadeIn .4s ease both; }
        .animate-slide-in-r { animation: slideInRight .4s ease both; }

        /* ─── Password strength bar ─── */
        .strength-bar { transition: width 0.3s ease, background-color 0.3s ease; }

        /* ─── Custom checkbox ─── */
        .custom-checkbox:checked { background-color: #1F4E79; border-color: #1F4E79; }

        /* ─── Input focus ring ─── */
        .input-brand:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(31,78,121,0.15);
            border-color: #1F4E79;
        }

        /* ─── Card hover ─── */
        .card-hover {
            transition: all .25s ease;
        }
        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(31,78,121,.1);
        }

        /* ─── Toggle switch (shared) ─── */
        .toggle-switch {
            width: 48px; height: 26px;
            background: #d1d5db; border-radius: 9999px;
            position: relative; cursor: pointer;
            transition: background .3s ease;
        }
        .toggle-switch::after {
            content: '';
            position: absolute; top: 3px; left: 3px;
            width: 20px; height: 20px;
            background: white; border-radius: 50%;
            box-shadow: 0 1px 3px rgba(0,0,0,.15);
            transition: transform .3s ease;
        }
        .toggle-switch.active { background: #1F4E79; }
        .toggle-switch.active::after { transform: translateX(22px); }

        /* ─── Scrollbar styling ─── */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* ─── Stagger delays ─── */
        .stagger-1 { animation-delay: .05s; }
        .stagger-2 { animation-delay: .1s; }
        .stagger-3 { animation-delay: .15s; }
        .stagger-4 { animation-delay: .2s; }
        .stagger-5 { animation-delay: .25s; }
    </style>

    @stack('styles')

    <!-- Firebase SDK (Compat version for simplicity) -->
    <script src="https://www.gstatic.com/firebasejs/9.6.1/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.6.1/firebase-messaging-compat.js"></script>
    <script src="{{ asset('js/firebase-messaging.js') }}" defer></script>
    <script>
        function themeHandler() {
            return {
                isDark: false,
                init() {
                    // Cek localStorage atau preferensi sistem
                    if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                        this.isDark = true;
                    } else {
                        this.isDark = false;
                    }
                    
                    // Dengarkan perubahan sistem
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
<body class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50/60 to-indigo-50/40 dark:from-slate-900 dark:via-slate-800/80 dark:to-indigo-950/40 text-gray-800 dark:text-gray-100 transition-colors duration-300">

    @yield('content')

    @stack('scripts')
</body>
</html>
