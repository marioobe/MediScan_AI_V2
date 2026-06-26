<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - MediScan AI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
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
</head>
<body class="bg-slate-950 font-sans antialiased min-h-screen flex items-center justify-center p-4">
    <main class="w-full">
    <div class="w-full max-w-md mx-auto">
        <div class="bg-slate-800 border border-slate-700 rounded-2xl shadow-xl p-8">
            <div class="text-center mb-8">
                <a href="/" class="inline-flex items-center space-x-2 mb-6">
                    <svg class="w-8 h-8 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    <span class="text-xl font-bold bg-gradient-to-r from-indigo-400 to-cyan-400 bg-clip-text text-transparent">MediScan AI</span>
                </a>
                <h1 class="text-2xl font-bold text-white">Admin Panel</h1>
                <p class="text-slate-400 mt-1">Masuk untuk mengelola model</p>
            </div>

            @if($errors->any())
                <div class="mb-4 p-3 bg-red-900/30 border border-red-700 text-red-300 rounded-lg text-sm">{{ $errors->first('email') }}</div>
            @endif

            <form method="POST" action="/admin/login">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-300 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full px-4 py-3 rounded-lg border border-slate-700 bg-slate-900/60 text-white placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                        placeholder="admin@gmail.com">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-300 mb-1">Password</label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-3 rounded-lg border border-slate-700 bg-slate-900/60 text-white placeholder-slate-500 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                        placeholder="Masukkan password">
                </div>
                <div class="flex items-center justify-between mb-6">
                    <label class="flex items-center text-sm text-slate-400">
                        <input type="checkbox" name="remember" class="rounded border-slate-600 bg-slate-900/60 text-indigo-500 focus:ring-indigo-500">
                        <span class="ml-2">Ingat saya</span>
                    </label>
                    <a href="/" class="text-sm text-indigo-400 hover:text-indigo-300">&larr; Kembali</a>
                </div>
                <button type="submit" class="w-full bg-gradient-to-r from-indigo-500 to-cyan-500 text-white py-3 rounded-lg font-medium hover:shadow-lg hover:from-indigo-600 hover:to-cyan-600 transition-all">
                    Masuk
                </button>
            </form>
        </div>
        </div>
    </main>
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
</body>
</html>
