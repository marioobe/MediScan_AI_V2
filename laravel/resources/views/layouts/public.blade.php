<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MediScan AI') - Klasifikasi Citra Medis Berbasis AI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#eef2ff', 100: '#e0e7ff', 200: '#c7d2fe',
                            300: '#a5b4fc', 400: '#818cf8', 500: '#6366f1',
                            600: '#4f46e5', 700: '#4338ca', 800: '#3730a3', 900: '#312e81',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { background: #020617; }
        .glow-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(120px);
            pointer-events: none;
        }
        .glow-card:hover {
            transform: translateY(-4px);
            border-color: rgba(99,102,241,0.3);
            box-shadow: 0 8px 40px rgba(99,102,241,0.15);
        }
        .glow-card {
            transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }
        .glow-btn {
            box-shadow: 0 0 20px rgba(99,102,241,0.3), 0 0 60px rgba(6,182,212,0.15);
            transition: all 0.3s ease;
        }
        .glow-btn:hover {
            box-shadow: 0 0 30px rgba(99,102,241,0.5), 0 0 80px rgba(6,182,212,0.25);
            transform: translateY(-2px);
        }
        .navbar-blur {
            backdrop-filter: blur(16px) saturate(180%);
            -webkit-backdrop-filter: blur(16px) saturate(180%);
            background-color: rgba(2,6,23,0.75);
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        .navbar-transparent {
            background-color: transparent;
            border-bottom: 1px solid transparent;
        }
        .mockup-frame {
            box-shadow: 0 0 40px rgba(99,102,241,0.1), 0 0 80px rgba(6,182,212,0.05);
        }
        .step-number {
            background: linear-gradient(135deg, rgba(99,102,241,0.2), rgba(6,182,212,0.1));
            border: 1px solid rgba(99,102,241,0.2);
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        .animate-float { animation: float 6s ease-in-out infinite; }
        @keyframes pulse-glow {
            0%, 100% { opacity: 0.6; }
            50% { opacity: 1; }
        }
        .animate-pulse-glow { animation: pulse-glow 4s ease-in-out infinite; }

        @keyframes pageFadeIn {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes pageFadeOut {
            from { opacity: 1; transform: translateY(0); }
            to   { opacity: 0; transform: translateY(-8px); }
        }
        body.page-leaving main { animation: pageFadeOut 0.2s ease-out forwards; }
        main.page-enter { animation: pageFadeIn 0.3s ease-out; }
    </style>
    @stack('head')
</head>
<body class="bg-slate-950 text-white font-sans antialiased">
    <nav id="navbar" class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 navbar-transparent">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 md:h-20">
                <a href="/" class="flex items-center space-x-2.5 group">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-cyan-500 flex items-center justify-center shadow-lg shadow-indigo-500/20 group-hover:shadow-indigo-500/40 transition-shadow">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <span class="text-xl font-bold bg-gradient-to-r from-indigo-400 via-indigo-300 to-cyan-400 bg-clip-text text-transparent">MediScan AI</span>
                </a>
                <div class="flex items-center space-x-1 md:space-x-2">
                    <a href="/" class="text-white/90 hover:text-white px-3 py-2 text-sm font-medium rounded-lg hover:bg-white/5 transition">Beranda</a>
                    <a href="/tes" class="text-white/70 hover:text-white px-3 py-2 text-sm font-medium rounded-lg hover:bg-white/5 transition">Prediksi</a>
                    <a href="/admin/login" class="ml-2 bg-gradient-to-r from-indigo-500 to-cyan-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:shadow-lg hover:shadow-indigo-500/25 transition-all">Admin</a>
                </div>
            </div>
        </div>
    </nav>
    <main>
        @yield('content')
    </main>
    <footer class="border-t border-white/5 bg-slate-950">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-10">
                {{-- Left: Brand & Info --}}
                <div class="text-center lg:text-left">
                    <div class="flex items-center justify-center lg:justify-start space-x-2.5 mb-3">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 to-cyan-500 flex items-center justify-center shadow-md shadow-indigo-500/20">
                            <svg class="w-4.5 h-4.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <span class="text-base font-bold bg-gradient-to-r from-indigo-400 via-indigo-300 to-cyan-400 bg-clip-text text-transparent">MediScan AI</span>
                    </div>
                    <p class="text-white/40 text-xs leading-relaxed max-w-xs mx-auto lg:mx-0">
                        Proyek Aplikasi Kelompok IV &mdash; Teknik Informatika
                    </p>
                    <p class="text-white/25 text-xs mt-3">&copy; 2026 MediScan AI-KELOMPOK IV. All rights reserved.</p>
                </div>

                {{-- Right: Quick Links --}}
                <div class="text-center lg:text-right">
                    <p class="text-white/40 text-xs font-semibold uppercase tracking-wider mb-3">Tautan Cepat</p>
                    <div class="flex flex-wrap items-center justify-center lg:justify-end gap-5">
                        <a href="/" class="text-white/40 hover:text-white text-sm transition">Beranda</a>
                        <a href="/tes" class="text-white/40 hover:text-white text-sm transition">Prediksi</a>
                        <a href="/admin/login" class="text-white/40 hover:text-white text-sm transition">Admin</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vanilla-tilt/1.8.1/vanilla-tilt.min.js" integrity="sha512-ATjfMXIJt2W3dRA/ZfJdO4KQZ1dZ3S2ZrS4Z4LhD/u2mdYsSBhajpPwhN1T4/U7RbE2V3yGA4dOSHzXbGTwBRg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        var navbar = document.getElementById('navbar');
        window.addEventListener('scroll', function() {
            if (window.scrollY > 40) {
                navbar.classList.remove('navbar-transparent');
                navbar.classList.add('navbar-blur');
            } else {
                navbar.classList.remove('navbar-blur');
                navbar.classList.add('navbar-transparent');
            }
        });
    </script>

    <script>
    (function() {
        var main = document.querySelector('main');
        var isTransitioning = false;

        function triggerFadeIn() {
            if (main) {
                main.classList.remove('page-enter');
                void main.offsetWidth;
                main.classList.add('page-enter');
            }
        }

        function triggerFadeOut(url) {
            if (isTransitioning) return;
            isTransitioning = true;
            document.body.classList.add('page-leaving');
            setTimeout(function() { window.location.href = url; }, 200);
        }

        document.addEventListener('click', function(e) {
            var link = e.target.closest('a');
            if (!link) return;
            if (e.ctrlKey || e.metaKey || e.which === 2) return;
            var href = link.getAttribute('href');
            if (!href) return;
            if (href.startsWith('#') || href.startsWith('javascript:') || href.startsWith('mailto:') || href.startsWith('tel:')) return;
            if (link.hasAttribute('download') || link.target === '_blank') return;
            e.preventDefault();
            triggerFadeOut(href);
        });

        window.addEventListener('pageshow', function(e) {
            if (e.persisted) {
                isTransitioning = false;
                document.body.classList.remove('page-leaving');
                triggerFadeIn();
            }
        });

        triggerFadeIn();
    })();
    </script>
    @stack('scripts')
</body>
</html>
