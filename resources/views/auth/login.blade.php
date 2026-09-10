@extends('layouts.app')

@section('title', 'Đăng Nhập Hệ Thống')

@section('content')
<div class="min-h-[calc(100vh-12rem)] flex items-center justify-center py-6">
    <div class="w-full max-w-4xl bg-white rounded-3xl shadow-xl border border-slate-200/90 overflow-hidden grid grid-cols-1 lg:grid-cols-12 relative z-10">
        <!-- Left Branding Panel -->
        <div class="lg:col-span-5 bg-gradient-to-br from-slate-900 via-indigo-950 to-blue-950 text-white p-8 sm:p-10 flex flex-col justify-between relative overflow-hidden">
            <div class="relative z-10">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-sky-300 text-xs font-medium backdrop-blur-md mb-6 border border-white/10">
                    <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                    Hệ Thống Quản Lý Thư Viện
                </div>
                <h1 class="text-3xl font-bold font-serif tracking-tight text-white mb-2">LibraNova</h1>
                <p class="text-slate-300 text-xs leading-relaxed">
                    Tự động hóa toàn diện quy trình vận hành kho sách, mượn trả, tính phạt trễ hạn tự động và cổng thanh toán trực tuyến VietQR.
                </p>
            </div>

            <div class="relative z-10 mt-8 space-y-3">
                <div class="flex items-center gap-3 text-xs text-slate-300">
                    <div class="w-7 h-7 rounded-lg bg-white/10 flex items-center justify-center text-sky-400">
                        <i data-lucide="book-check" class="w-4 h-4"></i>
                    </div>
                    <span>Quản lý kho sách & định vị vị trí kệ</span>
                </div>
                <div class="flex items-center gap-3 text-xs text-slate-300">
                    <div class="w-7 h-7 rounded-lg bg-white/10 flex items-center justify-center text-emerald-400">
                        <i data-lucide="qr-code" class="w-4 h-4"></i>
                    </div>
                    <span>Nộp phạt trễ hạn & gia hạn qua VietQR</span>
                </div>
                <div class="flex items-center gap-3 text-xs text-slate-300">
                    <div class="w-7 h-7 rounded-lg bg-white/10 flex items-center justify-center text-amber-400">
                        <i data-lucide="lock" class="w-4 h-4"></i>
                    </div>
                    <span>Kiểm soát an toàn & ghi nhận Audit Log</span>
                </div>
            </div>

            <!-- Quick accounts hint -->
            <div class="relative z-10 mt-6 pt-4 border-t border-white/10 text-[11px] text-slate-400">
                <div class="font-semibold text-slate-300 mb-1">Tài khoản demo (Mật khẩu: 123456):</div>
                <div class="space-y-0.5">
                    <div>• Độc giả: <code class="text-sky-300">an.nguyen@libranova.vn</code></div>
                    <div>• Thủ thư: <code class="text-purple-300">thuthu@libranova.vn</code></div>
                    <div>• Admin: <code class="text-amber-300">admin@libranova.vn</code></div>
                </div>
            </div>
        </div>

        <!-- Right Form Panel -->
        <div class="lg:col-span-7 p-8 sm:p-10 flex flex-col justify-center bg-white">
            <div class="mb-5">
                <h2 class="text-xl font-bold text-slate-900 tracking-tight">Đăng nhập tài khoản</h2>
                <p class="text-xs text-slate-500 mt-1">Chọn phân hệ làm việc hoặc nhấn chọn tài khoản mẫu để đăng nhập ngay</p>
            </div>

            <!-- Role Selector Tabs -->
            <div class="grid grid-cols-3 gap-2 p-1.5 bg-slate-100/90 rounded-2xl mb-5">
                <button type="button" onclick="selectRole('reader')" id="btn-role-reader" class="role-tab active flex items-center justify-center gap-1.5 py-2 px-3 rounded-xl text-xs font-semibold transition bg-white text-blue-700 shadow-sm">
                    <i data-lucide="user" class="w-3.5 h-3.5"></i>
                    Độc giả
                </button>
                <button type="button" onclick="selectRole('librarian')" id="btn-role-librarian" class="role-tab flex items-center justify-center gap-1.5 py-2 px-3 rounded-xl text-xs font-medium text-slate-600 hover:text-slate-900 transition">
                    <i data-lucide="bookmark" class="w-3.5 h-3.5"></i>
                    Thủ thư
                </button>
                <button type="button" onclick="selectRole('admin')" id="btn-role-admin" class="role-tab flex items-center justify-center gap-1.5 py-2 px-3 rounded-xl text-xs font-medium text-slate-600 hover:text-slate-900 transition">
                    <i data-lucide="shield" class="w-3.5 h-3.5"></i>
                    Admin
                </button>
            </div>

            <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="role" id="selected_role" value="reader">

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Địa chỉ Email</label>
                    <div class="relative">
                        <i data-lucide="mail" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                        <input type="email" name="email" id="login-email" required
                            class="w-full pl-10 pr-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 transition"
                            placeholder="an.nguyen@libranova.vn" value="{{ old('email', 'an.nguyen@libranova.vn') }}">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Mật khẩu</label>
                    <div class="relative">
                        <i data-lucide="lock" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                        <input type="password" name="password" id="login-password" required
                            class="w-full pl-10 pr-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 transition"
                            placeholder="••••••••" value="123456">
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs text-slate-500">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" name="remember" class="rounded text-sky-600 border-slate-300 focus:ring-sky-500" checked>
                        <span>Ghi nhớ đăng nhập</span>
                    </label>
                    <button type="button" onclick="openForgotPasswordModal()" class="text-sky-600 hover:text-sky-800 font-medium transition cursor-pointer hover:underline">
                        Quên mật khẩu?
                    </button>
                </div>

                <button type="submit" class="w-full py-2.5 px-4 bg-sky-600 hover:bg-sky-700 text-white text-sm font-semibold rounded-xl shadow-md transition flex items-center justify-center gap-2">
                    <span>Truy cập hệ thống</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </button>
            </form>

            <!-- Register Section -->
            <div class="mt-6 pt-5 border-t border-slate-100 flex items-center justify-between text-xs">
                <span class="text-slate-500">Chưa có thẻ thư viện độc giả?</span>
                <button type="button" onclick="openRegisterModal()" class="inline-flex items-center gap-1.5 text-sky-600 hover:text-sky-700 font-semibold transition hover:underline">
                    <i data-lucide="user-plus" class="w-3.5 h-3.5"></i>
                    Đăng ký tài khoản mới
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ================= MODAL: QUÊN MẬT KHẨU ================= -->
<div id="forgot-password-modal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-6 sm:p-7 border border-slate-200 relative animate-in fade-in zoom-in duration-200">
        <button type="button" onclick="closeForgotPasswordModal()" class="absolute top-5 right-5 text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100">
            <i data-lucide="x" class="w-5 h-5"></i>
        </button>

        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center border border-amber-200">
                <i data-lucide="key-round" class="w-5 h-5"></i>
            </div>
            <div>
                <h3 class="text-base font-bold text-slate-900">Khôi phục & Đặt lại mật khẩu</h3>
                <p class="text-xs text-slate-500">Tự động đặt lại mật khẩu và mở khóa tài khoản</p>
            </div>
        </div>

        <form action="{{ route('password.reset.direct') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Email tài khoản</label>
                <div class="relative">
                    <i data-lucide="mail" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                    <input type="email" name="email" id="forgot-email" required
                        class="w-full pl-10 pr-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500"
                        placeholder="Nhập email cần đặt lại (VD: an.nguyen@libranova.vn)">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Mật khẩu mới (tối thiểu 6 ký tự)</label>
                <div class="relative">
                    <i data-lucide="lock" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                    <input type="password" name="new_password" required minlength="6"
                        class="w-full pl-10 pr-4 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500"
                        placeholder="Nhập mật khẩu mới (VD: 123456)">
                </div>
            </div>

            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-[11px] text-slate-600 leading-relaxed">
                💡 <span class="font-semibold text-slate-800">Lưu ý bảo mật:</span> Sau khi đặt lại, nếu tài khoản của bạn đang bị khóa do nhập sai nhiều lần, hệ thống sẽ tự động mở khóa trạng thái hoạt động ngay lập tức.
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="closeForgotPasswordModal()" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                    Hủy bỏ
                </button>
                <button type="submit" class="px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white text-xs font-semibold rounded-xl shadow-md transition flex items-center gap-1.5">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Cập nhật mật khẩu mới</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL: ĐĂNG KÝ ĐỘC GIẢ MỚI ================= -->
<div id="register-modal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full p-6 sm:p-8 border border-slate-200 relative animate-in fade-in zoom-in duration-200 max-h-[90vh] overflow-y-auto">
        <button type="button" onclick="closeRegisterModal()" class="absolute top-5 right-5 text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100">
            <i data-lucide="x" class="w-5 h-5"></i>
        </button>

        <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center border border-sky-200">
                <i data-lucide="user-plus" class="w-5 h-5"></i>
            </div>
            <div>
                <h3 class="text-lg font-bold text-slate-900">Đăng ký Thẻ Độc Giả</h3>
                <p class="text-xs text-slate-500">Cấp mã thẻ thư viện điện tử miễn phí tức thì</p>
            </div>
        </div>

        <form action="{{ route('register.post') }}" method="POST" class="space-y-3.5">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Họ và tên độc giả <span class="text-red-500">*</span></label>
                <input type="text" name="name" required
                    class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500"
                    placeholder="Nguyễn Văn A">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Email đăng ký <span class="text-red-500">*</span></label>
                    <input type="email" name="email" required
                        class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500"
                        placeholder="docgia@gmail.com">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Số điện thoại</label>
                    <input type="tel" name="phone"
                        class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500"
                        placeholder="0912345678">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Địa chỉ cư trú</label>
                <input type="text" name="address"
                    class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500"
                    placeholder="Quận/Huyện, Tỉnh/TP">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Mật khẩu (tối thiểu 6 ký tự) <span class="text-red-500">*</span></label>
                    <input type="password" name="password" required minlength="6"
                        class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500"
                        placeholder="••••••••">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Xác nhận mật khẩu <span class="text-red-500">*</span></label>
                    <input type="password" name="password_confirmation" required minlength="6"
                        class="w-full px-3.5 py-2 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500"
                        placeholder="••••••••">
                </div>
            </div>

            <div class="p-3 bg-sky-50/70 rounded-xl border border-sky-100 text-[11px] text-sky-800 space-y-1">
                <div class="font-semibold flex items-center gap-1.5">
                    <i data-lucide="sparkles" class="w-3.5 h-3.5 text-sky-600"></i>
                    Quyền lợi thẻ độc giả LibraNova:
                </div>
                <div>• Mượn tối đa 5 cuốn sách đồng thời trong 14 ngày.</div>
                <div>• Tự gia hạn mượn sách trực tuyến và nhận thông báo hạn trả qua Email.</div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeRegisterModal()" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                    Đóng
                </button>
                <button type="submit" class="px-5 py-2.5 bg-sky-600 hover:bg-sky-700 text-white text-xs font-semibold rounded-xl shadow-md transition flex items-center gap-1.5">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Tạo thẻ & Đăng nhập ngay</span>
                </button>
            </div>
        </form>
    </div>
</div>
    <button type="button" onclick="openForgotPasswordModal()" class="text-sky-600 hover:text-sky-800 font-medium transition cursor-pointer hover:underline">
        Quên mật khẩu?
    </button>

    <button type="button" onclick="openRegisterModal()" class="inline-flex items-center gap-1.5 text-sky-600 hover:text-sky-700 font-semibold transition hover:underline">
        <i data-lucide="user-plus" class="w-3.5 h-3.5"></i>
        Đăng ký tài khoản mới
    </button>

    <form action="{{ route('password.reset.direct') }}" method="POST">
    @csrf
    ...
    </form>
<script>
    function selectRole(role) {
        document.getElementById('selected_role').value = role;
        const emailInput = document.getElementById('login-email');
        const passInput = document.getElementById('login-password');
        
        document.querySelectorAll('.role-tab').forEach(b => {
            b.className = 'role-tab flex items-center justify-center gap-1.5 py-2 px-3 rounded-xl text-xs font-medium text-slate-600 hover:text-slate-900 transition';
        });

        const activeBtn = document.getElementById(`btn-role-${role}`);
        if (role === 'reader') {
            activeBtn.className = 'role-tab active flex items-center justify-center gap-1.5 py-2 px-3 rounded-xl text-xs font-semibold transition bg-white text-blue-700 shadow-sm';
            emailInput.value = 'an.nguyen@libranova.vn';
            passInput.value = '123456';
        } else if (role === 'librarian') {
            activeBtn.className = 'role-tab active flex items-center justify-center gap-1.5 py-2 px-3 rounded-xl text-xs font-semibold transition bg-white text-purple-700 shadow-sm';
            emailInput.value = 'thuthu@libranova.vn';
            passInput.value = '123456';
        } else {
            activeBtn.className = 'role-tab active flex items-center justify-center gap-1.5 py-2 px-3 rounded-xl text-xs font-semibold transition bg-white text-amber-800 shadow-sm';
            emailInput.value = 'admin@libranova.vn';
            passInput.value = '123456';
        }
    }

    function openForgotPasswordModal() {
        const currentEmail = document.getElementById('login-email').value;
        if (currentEmail) {
            document.getElementById('forgot-email').value = currentEmail;
        }
        document.getElementById('forgot-password-modal').classList.remove('hidden');
    }

    function closeForgotPasswordModal() {
        document.getElementById('forgot-password-modal').classList.add('hidden');
    }

    function openRegisterModal() {
        document.getElementById('register-modal').classList.remove('hidden');
    }

    function closeRegisterModal() {
        document.getElementById('register-modal').classList.add('hidden');
    }

    // Close modals on clicking backdrop
    window.addEventListener('click', function(e) {
        const forgotModal = document.getElementById('forgot-password-modal');
        const registerModal = document.getElementById('register-modal');
        if (e.target === forgotModal) closeForgotPasswordModal();
        if (e.target === registerModal) closeRegisterModal();
    });

</script>
@endsection
