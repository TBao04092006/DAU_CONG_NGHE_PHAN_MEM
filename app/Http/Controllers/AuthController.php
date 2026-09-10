<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\AuditLog;
use App\Models\SystemRule;
use Carbon\Carbon;

class AuthController extends Controller
{
    /**
     * Tự động kiểm tra và khởi tạo dữ liệu mẫu nếu database đang trống.
     */
    protected function ensureDefaultSeed()
    {
        try {
            if (User::count() === 0 || !User::where('email', 'admin@libranova.vn')->exists()) {
                // 1. Quy định hệ thống mặc định
                if (!SystemRule::first()) {
                    SystemRule::create([
                        'max_books_per_loan' => 5,
                        'max_loan_days' => 14,
                        'fine_per_day' => 5000,
                        'card_renewal_fee' => 30000,
                        'max_renewal_times' => 2,
                        'session_timeout_minutes' => 15,
                        'max_failed_logins' => 5,
                        'bank_name' => 'MB Bank (Ngân hàng Quân Đội)',
                        'bank_account' => '0987654321',
                        'account_holder' => 'THU VIEN LIBRANOVA QUOC GIA'
                    ]);
                }

                // 2. Tài khoản Độc giả mẫu
                User::firstOrCreate(
                    ['email' => 'an.nguyen@libranova.vn'],
                    [
                        'name' => 'Nguyễn Văn An',
                        'password' => Hash::make('123456'),
                        'role' => 'reader',
                        'card_number' => 'LIB-2026-8899',
                        'card_expiry_date' => Carbon::now()->addYear(),
                        'status' => 'active',
                        'phone' => '0901234567',
                        'address' => 'Hà Nội'
                    ]
                );

                // 3. Tài khoản Thủ thư mẫu
                User::firstOrCreate(
                    ['email' => 'thuthu@libranova.vn'],
                    [
                        'name' => 'Trần Thu Thư',
                        'password' => Hash::make('123456'),
                        'role' => 'librarian',
                        'status' => 'active',
                        'phone' => '0912345678',
                        'address' => 'Thư viện LibraNova Trụ Sở Chính'
                    ]
                );

                // 4. Tài khoản Quản trị viên (Admin) mẫu
                User::firstOrCreate(
                    ['email' => 'admin@libranova.vn'],
                    [
                        'name' => 'Phạm Quang Admin',
                        'password' => Hash::make('123456'),
                        'role' => 'admin',
                        'status' => 'active',
                        'phone' => '0988888888',
                        'address' => 'Ban Giám Đốc Thư viện LibraNova'
                    ]
                );
            }
        } catch (\Exception $e) {
            // Tránh ngắt quãng nếu các bảng database chưa hoàn tất migration
        }
    }

    /**
     * Hiển thị giao diện đăng nhập (Tự động chuyển hướng nếu đã đăng nhập trước đó)
     */
    public function showLoginForm()
    {
        $this->ensureDefaultSeed();

        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            // Kiểm tra role và điều hướng tương ứng
            switch ($user->role) {
                case 'admin':
                    return redirect()->route('admin.index');
                case 'librarian':
                    return redirect()->route('librarian.index');
                case 'reader':
                default:
                    return redirect()->route('reader.index');
            }
        }

        return view('auth.login');
    }

    /**
     * Xử lý xác thực đăng nhập và điều hướng chính xác theo Role
     */
    public function login(Request $request)
    {
        $this->ensureDefaultSeed();

        // 1. Validate dữ liệu đầu vào
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ], [
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Địa chỉ email không đúng định dạng.',
            'password.required' => 'Vui lòng nhập mật khẩu.'
        ]);

        $email = strtolower(trim($request->email));
        $user = User::where('email', $email)->first();

        // Tự động tạo nếu là tài khoản demo mà chưa có trong DB
        if (!$user && in_array($email, ['an.nguyen@libranova.vn', 'thuthu@libranova.vn', 'admin@libranova.vn'])) {
            $this->ensureDefaultSeed();
            $user = User::where('email', $email)->first();
        }

        // Kiểm tra tồn tại người dùng
        if (!$user) {
            return back()->withErrors(['email' => 'Tài khoản không tồn tại trong hệ thống. Hãy bấm "Đăng ký thẻ độc giả" bên dưới để tạo tài khoản mới.'])->withInput();
        }

        // Kiểm tra tài khoản có bị khóa không
        if ($user->status === 'locked') {
            return back()->withErrors(['email' => 'Tài khoản này đã bị khóa an toàn. Bạn có thể nhấn "Quên mật khẩu?" để đặt lại mật khẩu và tự động mở khóa.'])->withInput();
        }

        // 2. Xác thực mật khẩu (hỗ trợ cả Hash bcrypt lẫn mật khẩu demo 123456)
        $passwordValid = Hash::check($request->password, $user->password) || $request->password === '123456';

        if (!$passwordValid) {
            $user->increment('failed_login_count');

            if ($user->failed_login_count >= 5) {
                $user->status = 'locked';
                $user->save();

                AuditLog::create([
                    'operator_name' => 'Hệ thống Tự Động',
                    'operator_role' => 'system',
                    'action' => 'AUTO_LOCK_ACCOUNT',
                    'target_id' => (string)$user->id,
                    'details' => "Tự động khóa tài khoản {$user->email} do nhập sai mật khẩu 5 lần liên tiếp.",
                    'ip_address' => $request->ip()
                ]);

                return back()->withErrors(['email' => 'Bạn đã nhập sai mật khẩu 5 lần liên tiếp. Tài khoản đã bị tạm khóa để bảo vệ an toàn.']);
            }

            return back()->withErrors(['password' => "Mật khẩu không chính xác (Sai lần {$user->failed_login_count}/5). Mật khẩu mặc định là 123456."])->withInput();
        }

        // 3. Đăng nhập thành công -> Reset số lần nhập sai & Đăng nhập vào Auth Guard
        $user->failed_login_count = 0;
        $user->save();

        Auth::login($user, $request->has('remember'));
        $request->session()->regenerate();

        // 4. Ghi nhận Audit Log
        AuditLog::create([
            'operator_name' => $user->name,
            'operator_role' => $user->role,
            'action' => 'LOGIN',
            'target_id' => (string)$user->id,
            'details' => "Đăng nhập thành công với vai trò: " . strtoupper($user->role),
            'ip_address' => $request->ip()
        ]);

        // 5. KIỂM TRA ROLE VÀ ĐIỀU HƯỚNG TƯƠNG ỨNG
        if ($user->role === 'admin') {
            return redirect()->route('admin.index')->with('success', "Chào mừng Quản trị viên {$user->name} quay trở lại!");
        } elseif ($user->role === 'librarian') {
            return redirect()->route('librarian.index')->with('success', "Chào mừng Thủ thư {$user->name} đã vào ca làm việc!");
        } else {
            return redirect()->route('reader.index')->with('success', "Chào mừng độc giả {$user->name} đến với Thư viện LibraNova!");
        }
    }

    /**
     * Hiển thị form đăng ký độc giả mới
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Xử lý đăng ký tài khoản độc giả mới
     */
    public function register(Request $request)
    {
        $this->ensureDefaultSeed();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed'
        ], [
            'name.required' => 'Vui lòng nhập họ và tên.',
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.unique' => 'Email này đã được sử dụng.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải từ 6 ký tự trở lên.',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.'
        ]);

        $cardNumber = 'LIB-2026-' . rand(1000, 9999);

        $user = User::create([
            'name' => trim($request->name),
            'email' => strtolower(trim($request->email)),
            'password' => Hash::make($request->password),
            'role' => 'reader', // Mặc định tài khoản đăng ký công khai là Độc giả
            'card_number' => $cardNumber,
            'card_expiry_date' => Carbon::now()->addYear(),
            'status' => 'active',
            'phone' => $request->phone ?? 'Chưa cập nhật',
            'address' => $request->address ?? 'Đang cập nhật'
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('reader.index')->with('success', "🎉 Đăng ký thẻ độc giả thành công! Mã thẻ của bạn là {$cardNumber}.");
    }

    /**
     * Hiển thị giao diện quên mật khẩu
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Đặt lại mật khẩu và tự động mở khóa tài khoản
     */
    public function resetPasswordDirect(Request $request)
    {
        $this->ensureDefaultSeed();

        $request->validate([
            'email' => 'required|email',
            'new_password' => 'required|string|min:6'
        ], [
            'email.required' => 'Vui lòng nhập email tài khoản.',
            'new_password.required' => 'Vui lòng nhập mật khẩu mới.',
            'new_password.min' => 'Mật khẩu mới phải từ 6 ký tự trở lên.'
        ]);

        $user = User::where('email', strtolower(trim($request->email)))->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Không tìm thấy tài khoản với email này trong hệ thống.'])->withInput();
        }

        $user->password = Hash::make($request->new_password);
        $user->failed_login_count = 0;
        $user->status = 'active'; // Tự động mở khóa tài khoản
        $user->save();

        return redirect()->route('login')->with('success', " Đặt lại mật khẩu thành công và tài khoản đã được mở khóa! Bạn có thể đăng nhập ngay.");
    }

    /**
     * Đăng xuất an toàn khỏi hệ thống
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Đã đăng xuất an toàn khỏi hệ thống.');
    }
}