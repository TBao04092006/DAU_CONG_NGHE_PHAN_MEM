@extends('layouts.app')

@section('title', 'Đăng Ký Thẻ Độc Giả')

@section('content')
<div class="min-h-[calc(100vh-12rem)] flex items-center justify-center py-6">
    <div class="w-full max-w-xl bg-white rounded-3xl shadow-xl border border-slate-200/90 overflow-hidden p-6 sm:p-8 relative z-10">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center border border-sky-200">
                <i data-lucide="user-plus" class="w-6 h-6"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold text-slate-900 tracking-tight">Đăng ký Thẻ Độc Giả Mới</h2>
                <p class="text-xs text-slate-500">Mã thẻ thư viện điện tử được cấp tự động ngay khi hoàn tất</p>
            </div>
        </div>

        <form action="{{ route('register.post') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Họ và tên độc giả <span class="text-red-500">*</span></label>
                <div class="relative">
                    <i data-lucide="user" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                    <input type="text" name="name" required value="{{ old('name') }}"
                        class="w-full pl-10 pr-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 transition"
                        placeholder="Nguyễn Văn An">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Địa chỉ Email <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <i data-lucide="mail" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                        <input type="email" name="email" required value="{{ old('email') }}"
                            class="w-full pl-10 pr-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 transition"
                            placeholder="an.nguyen@gmail.com">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Số điện thoại</label>
                    <div class="relative">
                        <i data-lucide="phone" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                        <input type="tel" name="phone" value="{{ old('phone') }}"
                            class="w-full pl-10 pr-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 transition"
                            placeholder="0912345678">
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Địa chỉ cư trú</label>
                <div class="relative">
                    <i data-lucide="map-pin" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                    <input type="text" name="address" value="{{ old('address') }}"
                        class="w-full pl-10 pr-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 transition"
                        placeholder="Số nhà, Phường/Xã, Quận/Huyện, Tỉnh/TP">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Mật khẩu (tối thiểu 6 ký tự) <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <i data-lucide="lock" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                        <input type="password" name="password" required minlength="6"
                            class="w-full pl-10 pr-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 transition"
                            placeholder="••••••••">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Xác nhận mật khẩu <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <i data-lucide="lock" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                        <input type="password" name="password_confirmation" required minlength="6"
                            class="w-full pl-10 pr-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 transition"
                            placeholder="••••••••">
                    </div>
                </div>
            </div>

            <div class="p-3 bg-sky-50/70 rounded-2xl border border-sky-100 text-xs text-sky-800 space-y-1">
                <div class="font-semibold flex items-center gap-1.5">
                    <i data-lucide="shield-check" class="w-4 h-4 text-sky-600"></i>
                    Chính sách thẻ thư viện điện tử:
                </div>
                <div>• Thẻ có hiệu lực 12 tháng kể từ ngày đăng ký, mượn tối đa 5 quyển cùng lúc.</div>
                <div>• Gia hạn trực tuyến thuận tiện qua tài khoản cá nhân.</div>
            </div>

            <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                <a href="{{ route('login') }}" class="text-xs text-slate-500 hover:text-slate-800 flex items-center gap-1">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                    <span>Đã có tài khoản? Đăng nhập</span>
                </a>
                <button type="submit" class="py-2.5 px-5 bg-sky-600 hover:bg-sky-700 text-white text-xs font-semibold rounded-xl shadow-md transition flex items-center gap-2">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Đăng Ký & Tạo Thẻ Ngay</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
