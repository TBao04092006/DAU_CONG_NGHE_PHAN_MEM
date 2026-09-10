<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Hệ Thống Quản Lý Thư Viện') - LibraNova</title>
    
    <!-- Google Fonts: Be Vietnam Pro & Playfair Display -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet">
    
    <!-- Lucide Icons CDN -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- Tailwind CSS CDN for high-fidelity styling -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Be Vietnam Pro"', 'sans-serif'],
                        serif: ['"Playfair Display"', 'serif'],
                    },
                    colors: {
                        brand: {
                            blue: '#0284c7',
                            darkBlue: '#0f172a',
                            lightBlue: '#38bdf8',
                            purple: '#7e22ce',
                            amber: '#b45309'
                        }
                    }
                }
            }
        }
    </script>
    
    <link rel="stylesheet" href="/css/style.css">
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col font-sans relative antialiased selection:bg-sky-100 selection:text-sky-900">

    <!-- Dong Son Drum (Trống Đồng Đông Sơn) Light Blue Background Watermarks -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden z-0 select-none opacity-20">
        <!-- Top Right Drum -->
        <svg class="absolute -top-32 -right-32 w-[680px] h-[680px] text-sky-400 stroke-current fill-none" viewBox="0 0 600 600">
            <circle cx="300" cy="300" r="285" stroke-width="2.5" />
            <circle cx="300" cy="300" r="280" stroke-width="1" stroke-dasharray="3,3" />
            <circle cx="300" cy="300" r="268" stroke-width="1.8" />
            <circle cx="300" cy="300" r="230" stroke-width="1.4" />
            <circle cx="300" cy="300" r="140" stroke-width="1.6" />
            <circle cx="300" cy="300" r="88" stroke-width="1.5" />
            <!-- Central Star 14 rays -->
            <polygon points="300,240 307,275 342,260 320,290 360,300 320,310 342,340 307,325 300,360 293,325 258,340 280,310 240,300 280,290 258,260 293,275" stroke-width="2" />
            <circle cx="300" cy="300" r="8" fill="currentColor" />
        </svg>

        <!-- Center Left Drum -->
        <svg class="absolute top-1/2 -left-44 -translate-y-1/2 w-[760px] h-[760px] text-blue-400 stroke-current fill-none" viewBox="0 0 600 600">
            <circle cx="300" cy="300" r="285" stroke-width="2.5" />
            <circle cx="300" cy="300" r="230" stroke-width="1.4" />
            <circle cx="300" cy="300" r="140" stroke-width="1.6" />
            <polygon points="300,240 307,275 342,260 320,290 360,300 320,310 342,340 307,325 300,360 293,325 258,340 280,310 240,300 280,290 258,260 293,275" stroke-width="2" />
            <circle cx="300" cy="300" r="8" fill="currentColor" />
        </svg>
    </div>

    <!-- Top Navigation Bar -->
    <header class="sticky top-0 z-30 bg-white/95 backdrop-blur-md border-b border-slate-200/90 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <!-- Brand Logo -->
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-slate-900 to-indigo-950 flex items-center justify-center text-amber-300 shadow-md">
                    <i data-lucide="book-open" class="w-5 h-5"></i>
                </div>
                <div>
                    <a href="/" class="text-lg font-bold tracking-tight text-slate-900 flex items-center gap-2">
                        LibraNova
                        <span class="text-[11px] font-medium px-2 py-0.5 rounded-full bg-sky-100 text-sky-800 border border-sky-200">Laravel</span>
                    </a>
                    <p class="text-[11px] text-slate-500 hidden sm:block">Hệ Thống Quản Lý Thư Viện Tự Động Hóa</p>
                </div>
            </div>

            <!-- User Status & Actions -->
            <div class="flex items-center gap-3">
                @if(Auth::check())
                    <!-- Session Timeout Badge -->
                    <div class="hidden md:flex items-center gap-1.5 px-3 py-1 bg-slate-100 border border-slate-200 rounded-full text-xs font-mono text-slate-600">
                        <i data-lucide="clock" class="w-3.5 h-3.5 text-slate-400"></i>
                        <span>Phiên:</span>
                        <span id="session-timer" class="font-bold text-sky-700">14:59</span>
                    </div>

                    <!-- Role Badge -->
                    @php
                        $role = Auth::user()->role;
                        $roleColors = [
                            'reader' => 'bg-blue-50 text-blue-700 border-blue-200',
                            'librarian' => 'bg-purple-50 text-purple-700 border-purple-200',
                            'admin' => 'bg-amber-50 text-amber-800 border-amber-200'
                        ];
                        $roleLabels = [
                            'reader' => 'Độc Giả',
                            'librarian' => 'Thủ Thư',
                            'admin' => 'Quản Trị Viên'
                        ];
                    @endphp
                    <div class="flex items-center gap-2 px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl">
                        <div class="text-right hidden sm:block">
                            <div class="text-xs font-bold text-slate-800">{{ Auth::user()->name }}</div>
                            <span class="text-[10px] px-1.5 py-0.2 rounded font-semibold border {{ $roleColors[$role] ?? 'bg-slate-100' }}">
                                {{ $roleLabels[$role] ?? ucfirst($role) }}
                            </span>
                        </div>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Đăng xuất">
                                <i data-lucide="log-out" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-semibold bg-sky-600 text-white rounded-xl shadow hover:bg-sky-700 transition">
                        Đăng Nhập
                    </a>
                @endif
            </div>
        </div>
    </header>

    <!-- Global Alert Messages (Có nút ✕ tắt nhanh và không khóa stacking context) -->
    <div id="global-alert-container" class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 mt-4">
        @if(session('success'))
            <div id="alert-success" class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl flex items-center justify-between shadow-sm mb-4">
                <div class="flex items-center gap-3">
                    <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600 flex-shrink-0"></i>
                    <div class="text-sm font-medium">{{ session('success') }}</div>
                </div>
                <button type="button" onclick="document.getElementById('alert-success')?.remove()" class="text-emerald-500 hover:text-emerald-700 p-1 rounded-lg">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div id="alert-error" class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-2xl flex items-center justify-between shadow-sm mb-4">
                <div class="flex items-center gap-3">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-red-600 flex-shrink-0"></i>
                    <div class="text-sm font-medium">{{ session('error') }}</div>
                </div>
                <button type="button" onclick="document.getElementById('alert-error')?.remove()" class="text-red-500 hover:text-red-700 p-1 rounded-lg">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        @endif
    </div>

    <!-- Main Content: Đã bỏ relative z-10 để các modal z-[100] luôn nổi lên trên cùng -->
    <main class="flex-1 max-w-7xl w-full mx-auto p-4 sm:p-6 lg:p-8">
        @yield('content')
    </main>

    <!-- Main Content -->
    <main class="flex-1 max-w-7xl w-full mx-auto p-4 sm:p-6 lg:p-8 relative z-10">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="mt-auto border-t border-slate-200/80 bg-white/80 backdrop-blur-sm py-6 relative z-10">
        <div class="max-w-7xl mx-auto px-4 text-center text-xs text-slate-500 flex flex-col sm:flex-row items-center justify-between gap-2">
            <div>
                © {{ date('Y') }} <strong>LibraNova</strong> — Hệ Thống Quản Lý Thư Viện Tự Động Hóa Vận Hành
            </div>
            <div class="flex items-center gap-4 text-[11px] text-slate-400">
                <span>Laravel Framework</span>
                <span>•</span>
                <span>HTML - CSS - JavaScript</span>
                <span>•</span>
                <span>VietQR Napas 247</span>
            </div>
        </div>
    </footer>

    <!-- Init Lucide Icons -->
    <script>
        lucide.createIcons();

        // Session timeout countdown timer
        let timeLeft = 15 * 60;
        const timerEl = document.getElementById('session-timer');
        if (timerEl) {
            setInterval(() => {
                if (timeLeft > 0) {
                    timeLeft--;
                    const m = Math.floor(timeLeft / 60);
                    const s = timeLeft % 60;
                    timerEl.textContent = `${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
                }
            }, 1000);
        }
    </script>

    <script src="/js/app.js"></script>
    @yield('scripts')
</body>
</html>
