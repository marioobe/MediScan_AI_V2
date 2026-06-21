<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - Admin MediScan AI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            200: '#c7d2fe',
                            300: '#a5b4fc',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            800: '#3730a3',
                            900: '#312e81',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        @keyframes pageFadeIn {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes pageFadeOut {
            from { opacity: 1; transform: translateY(0); }
            to   { opacity: 0; transform: translateY(-8px); }
        }
        body.page-leaving main {
            animation: pageFadeOut 0.2s ease-out forwards;
        }
        main.page-enter {
            animation: pageFadeIn 0.3s ease-out;
        }
    </style>
    @stack('head')
</head>
<body class="bg-slate-950 font-sans antialiased">
    <div class="flex h-screen overflow-hidden">
        {{-- Backdrop for mobile sidebar --}}
        <div id="sidebar-backdrop" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-40 hidden md:hidden" onclick="toggleSidebar()"></div>

        {{-- Sidebar --}}
        <aside id="sidebar" class="fixed md:relative z-50 md:z-0 w-64 bg-slate-900 border-r border-slate-800 text-white flex-shrink-0 -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col">
            <div class="p-6 border-b border-slate-800 flex items-center justify-between">
                <a href="/admin/dashboard" class="flex items-center space-x-2">
                    <svg class="w-8 h-8 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    <span class="text-lg font-bold">MediScan AI</span>
                </a>
                <button onclick="toggleSidebar()" class="md:hidden p-2 rounded-lg text-slate-400 hover:bg-slate-800/60 transition" aria-label="Close sidebar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
                <a href="/admin/dashboard" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-slate-800/60 transition @yield('active-dashboard', '')">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span>Dashboard</span>
                </a>
                <a href="/admin/models" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-slate-800/60 transition @yield('active-models', '')">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/></svg>
                    <span>Models</span>
                </a>
                <a href="/admin/training" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-slate-800/60 transition @yield('active-training', '')">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    <span>Training</span>
                </a>
                <a href="/admin/predictions" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-slate-800/60 transition @yield('active-predictions', '')">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span>Predictions</span>
                </a>
            </nav>
            <div class="p-4 border-t border-slate-800">
                <div class="flex items-center space-x-3 px-4 py-2">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-r from-indigo-400 to-sky-400 flex items-center justify-center text-white text-sm font-bold">A</div>
                    <span class="text-sm">Admin</span>
                </div>
                <form action="/admin/logout" method="POST">
                    @csrf
                    <button type="submit" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-slate-800/60 transition text-slate-400 mt-2 w-full">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        {{-- Main Content Area --}}
        <div class="flex-1 flex flex-col overflow-hidden">
            {{-- Header --}}
            <header class="bg-slate-900/80 backdrop-blur-sm border-b border-slate-800 px-4 md:px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <button id="hamburger-btn" onclick="toggleSidebar()" class="md:hidden p-2 rounded-lg text-slate-400 hover:bg-slate-800 transition" aria-label="Toggle sidebar">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                        <h1 class="text-xl font-semibold text-white">@yield('page-title', 'Dashboard')</h1>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="text-sm text-slate-400 hidden sm:inline">Admin</span>
                        <div class="w-8 h-8 rounded-full bg-gradient-to-r from-indigo-400 to-sky-400 flex items-center justify-center text-white text-sm font-bold">A</div>
                    </div>
                </div>
                @hasSection('breadcrumb')
                <div class="flex items-center space-x-2 text-sm text-slate-400 mt-2">
                    <a href="/admin/dashboard" class="hover:text-indigo-400 transition">Dashboard</a>
                    @yield('breadcrumb')
                </div>
                @endif
            </header>

            {{-- Content --}}
            <main class="flex-1 overflow-y-auto p-4 md:p-6">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- Toast Container --}}
    <div id="toast-container" class="fixed top-4 right-4 z-[100] flex flex-col space-y-2 w-80 pointer-events-none"></div>

    {{-- Confirm Modal --}}
    <div id="confirm-modal" class="fixed inset-0 z-50 hidden" aria-modal="true">
        <div class="absolute inset-0 bg-black/50" onclick="closeConfirm()"></div>
        <div class="relative min-h-screen flex items-center justify-center p-4">
            <div class="bg-slate-800 border border-slate-700 rounded-2xl shadow-2xl max-w-sm w-full p-6">
                <div class="text-center">
                    <div class="w-12 h-12 mx-auto mb-4 rounded-full bg-red-900/30 flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-white mb-2" id="confirm-title">Confirm</h3>
                    <p class="text-sm text-slate-400 mb-6" id="confirm-message">Are you sure?</p>
                    <div class="flex gap-3 justify-center">
                        <button onclick="closeConfirm()" class="px-4 py-2 border border-slate-700 text-slate-300 rounded-lg text-sm font-medium hover:bg-slate-700/30 transition">Cancel</button>
                        <button id="confirm-yes-btn" class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 transition">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Flash messages to toast --}}
    @if(session('success'))
    <script>window.addEventListener('DOMContentLoaded', function() { showToast('{{ session('success') }}', 'success'); });</script>
    @endif
    @if(session('error'))
    <script>window.addEventListener('DOMContentLoaded', function() { showToast('{{ session('error') }}', 'error'); });</script>
    @endif

    <script>
    // ===== Sidebar Toggle =====
    function toggleSidebar() {
        var sidebar = document.getElementById('sidebar');
        var backdrop = document.getElementById('sidebar-backdrop');
        sidebar.classList.toggle('-translate-x-full');
        backdrop.classList.toggle('hidden');
    }

    window.addEventListener('resize', function() {
        if (window.innerWidth >= 768) {
            var sidebar = document.getElementById('sidebar');
            var backdrop = document.getElementById('sidebar-backdrop');
            sidebar.classList.remove('-translate-x-full');
            backdrop.classList.add('hidden');
        }
    });

    // ===== Toast System =====
    function showToast(message, type) {
        type = type || 'success';
        var container = document.getElementById('toast-container');
        var colors = {
            success: { bg: 'bg-green-900/30', border: 'border-green-700', text: 'text-green-300', icon: 'check' },
            error: { bg: 'bg-red-900/30', border: 'border-red-700', text: 'text-red-300', icon: 'x' },
            warning: { bg: 'bg-yellow-900/30', border: 'border-yellow-700', text: 'text-yellow-300', icon: 'warning' },
            info: { bg: 'bg-blue-900/30', border: 'border-blue-700', text: 'text-blue-300', icon: 'info' },
        };
        var cfg = colors[type] || colors.success;
        var icons = {
            check: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>',
            x: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>',
            warning: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
            info: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>',
        };
        var toast = document.createElement('div');
        toast.className = cfg.bg + ' border ' + cfg.border + ' rounded-xl p-4 shadow-lg flex items-start gap-3 transition-all duration-300 translate-x-full opacity-0 pointer-events-auto';
        toast.innerHTML = '<svg class="w-5 h-5 shrink-0 ' + cfg.text + '" fill="none" stroke="currentColor" viewBox="0 0 24 24">' + (icons[cfg.icon] || icons.info) + '</svg>'
            + '<p class="text-sm ' + cfg.text + ' flex-1">' + message + '</p>'
            + '<button onclick="this.parentElement.remove()" class="shrink-0 ' + cfg.text + ' opacity-60 hover:opacity-100 transition">'
            + '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>';
        container.appendChild(toast);
        requestAnimationFrame(function() {
            toast.classList.remove('translate-x-full', 'opacity-0');
        });
        if (type !== 'loading') {
            setTimeout(function() {
                toast.classList.add('translate-x-full', 'opacity-0');
                setTimeout(function() { if (toast.parentElement) toast.remove(); }, 300);
            }, 4000);
        }
    }

    // ===== Confirm Modal =====
    var _confirmCallback = null;

    function showConfirm(title, message, onConfirm) {
        document.getElementById('confirm-title').textContent = title;
        document.getElementById('confirm-message').textContent = message;
        document.getElementById('confirm-modal').classList.remove('hidden');
        _confirmCallback = onConfirm;
    }

    function closeConfirm() {
        document.getElementById('confirm-modal').classList.add('hidden');
        _confirmCallback = null;
    }

    document.getElementById('confirm-yes-btn').addEventListener('click', function() {
        if (_confirmCallback) _confirmCallback();
        closeConfirm();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') { closeConfirm(); }
    });

    // ===== Page Fade Transition =====
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
            setTimeout(function() {
                window.location.href = url;
            }, 200);
        }

        document.addEventListener('click', function(e) {
            var link = e.target.closest('a');
            if (!link) return;
            if (e.ctrlKey || e.metaKey || e.which === 2) return;
            var href = link.getAttribute('href');
            if (!href) return;
            if (href.startsWith('http') && !href.startsWith(window.location.origin)) return;
            if (href.startsWith('#') || href.startsWith('javascript:') || href.startsWith('mailto:')) return;
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
