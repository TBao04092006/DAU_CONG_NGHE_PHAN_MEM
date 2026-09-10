<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
if (file_exists(__DIR__.'/../vendor/autoload.php')) {
    require __DIR__.'/../vendor/autoload.php';
    $app = require_once __DIR__.'/../bootstrap/app.php';
    $kernel = $app->make(Kernel::class);
    $response = $kernel->handle(
        $request = Request::capture()
    )->send();
    $kernel->terminate($request, $response);
} else {
    // Elegant standalone preview mode when running 'php -S' or 'artisan serve' before composer finishes
    header("Content-Type: text/html; charset=UTF-8");
    echo '<!DOCTYPE html>
    <html lang="vi">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>LibraNova - Laravel Library System</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-slate-900 min-h-screen flex items-center justify-center p-4 text-slate-100 font-sans">
        <div class="max-w-2xl w-full bg-slate-800 p-8 rounded-3xl shadow-2xl border border-slate-700 text-center space-y-6">
            <div class="w-16 h-16 bg-sky-500/20 text-sky-400 rounded-2xl mx-auto flex items-center justify-center font-bold text-2xl border border-sky-400/30">LN</div>
            
            <div class="space-y-1">
                <h1 class="text-2xl font-bold text-white tracking-wide">Hệ Thống Quản Lý Thư Viện LibraNova</h1>
                <p class="text-sm text-slate-400">Máy chủ PHP đang chạy thành công trên máy của bạn.</p>
            </div>

            <div class="p-5 bg-slate-900/90 rounded-2xl text-left border border-slate-700/80 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-amber-400 uppercase tracking-wider">Lệnh cài đặt vượt qua kiểm tra phiên bản PHP:</span>
                    <span class="text-[10px] bg-sky-900/50 text-sky-300 px-2 py-0.5 rounded">Khuyên dùng</span>
                </div>
                <div class="bg-black/60 p-3 rounded-xl font-mono text-xs text-emerald-400 border border-emerald-500/30 select-all">
                    composer install --ignore-platform-reqs
                </div>
                <p class="text-xs text-slate-400">
                    💡 Thêm cờ <code>--ignore-platform-reqs</code> giúp Composer bỏ qua các yêu cầu khắt khe về phiên bản PHP/extension của XAMPP để cài đặt 100% thành công.
                </p>
            </div>

            <div class="pt-2 text-xs text-slate-500 flex justify-between items-center border-t border-slate-700">
                <span>Cấu trúc: Laravel 10/11 • Blade Views • Tailwind CSS • JavaScript</span>
                <span class="text-sky-400 font-medium">LibraNova v2.6</span>
            </div>
        </div>
    </body>
    </html>';
}
