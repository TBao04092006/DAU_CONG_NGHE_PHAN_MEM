@extends('layouts.app')

@section('title', 'Quên Mật Khẩu')

@section('content')
<div class="min-h-[calc(100vh-12rem)] flex items-center justify-center py-6">
    <div class="w-full max-w-md bg-white rounded-3xl shadow-xl border border-slate-200/90 overflow-hidden p-6 sm:p-8 relative z-10">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center border border-amber-200">
                <i data-lucide="key-round" class="w-6 h-6"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold text-slate-900 tracking-tight">Khôi Phục Mật Khẩu</h2>
                <p class="text-xs text-slate-500">Đặt lại mật khẩu mới & tự động mở khóa tài khoản</p>
            </div>
        </div>

        <form action="{{ route('password.reset.direct') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Địa chỉ Email đăng ký <span class="text-red-500">*</span></label>
                <div class="relative">
                    <i data-lucide="mail" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                    <input type="email" name="email" required value="{{ old('email') }}"
                        class="w-full pl-10 pr-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 transition"
                        placeholder="an.nguyen@libranova.vn">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Mật khẩu mới (tối thiểu 6 ký tự) <span class="text-red-500">*</span></label>
                <div class="relative">
                    <i data-lucide="lock" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                    <input type="password" name="new_password" required minlength="6"
                        class="w-full pl-10 pr-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 transition"
                        placeholder="Nhập mật khẩu mới (VD: 123456)">
                </div>
            </div>

            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-xs text-slate-600 leading-relaxed">
                💡 <span class="font-semibold text-slate-800">Tính năng mở khóa tự động:</span> Nếu tài khoản của bạn bị tạm khóa do nhập sai mật khẩu quá 5 lần, thao tác đặt lại mật khẩu này cũng sẽ tự động mở khóa tài khoản để bạn đăng nhập lại ngay lập tức.
            </div>

            <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                <a href="{{ route('login') }}" class="text-xs text-slate-500 hover:text-slate-800 flex items-center gap-1">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                    <span>Quay lại Đăng nhập</span>
                </a>
                <button type="submit" class="py-2.5 px-5 bg-sky-600 hover:bg-sky-700 text-white text-xs font-semibold rounded-xl shadow-md transition flex items-center gap-2">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Cập Nhật Mật Khẩu</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
