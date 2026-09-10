@extends('layouts.app')

@section('title', 'Cổng Tra Cứu & Độc Giả')

@section('content')
<div class="space-y-6">
    <!-- 1. Thẻ thông tin độc giả (Hero Card) -->
    <div class="bg-gradient-to-br from-blue-900 via-indigo-900 to-slate-900 text-white rounded-3xl p-6 sm:p-8 shadow-lg relative overflow-hidden">
        <div class="absolute -right-16 -bottom-16 w-64 h-64 bg-sky-500/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-sky-300 text-xs font-medium">
                    <i data-lucide="credit-card" class="w-3.5 h-3.5"></i>
                    Thẻ Thư Viện: <strong>{{ $user->card_number ?? 'LIB-2026-8899' }}</strong>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold font-serif">Xin chào, {{ $user->name }}</h1>
                <p class="text-xs sm:text-sm text-slate-300 max-w-xl">
                    Hạn sử dụng thẻ: <strong>{{ $user->card_expiry_date ? \Carbon\Carbon::parse($user->card_expiry_date)->format('d/m/Y') : '15/12/2026' }}</strong>
                    @if($user->status === 'active')
                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">Hợp Lệ</span>
                    @else
                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-red-500/20 text-red-300 border border-red-500/30">Hết Hạn/Khóa</span>
                    @endif
                </p>
            </div>

            <!-- Card Actions -->
            <div class="flex flex-wrap items-center gap-3">
                <a href="#history-section" class="px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white font-semibold text-xs rounded-xl border border-white/20 shadow-xs transition flex items-center gap-2 backdrop-blur-sm">
                    <i data-lucide="history" class="w-4 h-4 text-sky-300"></i>
                    Lịch Sử Mượn - Trả & Giao Dịch
                </a>
                <button type="button" onclick="openCardRenewModal()" class="px-4 py-2.5 bg-sky-500 hover:bg-sky-400 text-slate-950 font-semibold text-xs rounded-xl shadow transition flex items-center gap-2">
                    <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                    Gia Hạn Thẻ ({{ number_format($rules->card_renewal_fee ?? 30000) }}đ)
                </button>
                @if($totalUnpaidFines > 0)
                    <button type="button" onclick="openVietQRModal('{{ $totalUnpaidFines }}', 'NOP PHAT TRE HAN CHO DOC GIA {{ $user->card_number }}', null)" class="px-4 py-2.5 bg-amber-400 hover:bg-amber-300 text-slate-950 font-semibold text-xs rounded-xl shadow transition flex items-center gap-2">
                        <i data-lucide="qr-code" class="w-4 h-4"></i>
                        Nộp Phạt: {{ number_format($totalUnpaidFines) }}đ
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- 2. Quy định thư viện hiện hành -->
    <div class="bg-blue-50/90 border border-blue-200/80 rounded-2xl p-3.5 px-5 flex flex-wrap items-center justify-between gap-3 text-xs text-blue-900 shadow-2xs">
        <div class="flex items-center gap-2">
            <span class="p-1 rounded bg-blue-100 text-blue-700"><i data-lucide="shield-check" class="w-4 h-4"></i></span>
            <span class="font-bold">Quy định thư viện hiện hành:</span>
        </div>
        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-slate-600">
            <span>Mượn tối đa: <strong class="text-blue-800 font-semibold">{{ $rules->max_books_per_loan ?? 5 }} cuốn</strong></span>
            <span>Thời hạn: <strong class="text-blue-800 font-semibold">{{ $rules->max_loan_days ?? 14 }} ngày</strong></span>
            <span>Gia hạn: <strong class="text-blue-800 font-semibold">Tối đa {{ $rules->max_renewal_times ?? 2 }} lần</strong></span>
            <span>Phạt trễ: <strong class="text-rose-600 font-semibold">{{ number_format($rules->fine_per_day ?? 5000) }}đ/ngày</strong></span>
            <span>Phí gia hạn thẻ: <strong class="text-emerald-700 font-semibold">{{ number_format($rules->card_renewal_fee ?? 30000) }}đ</strong></span>
        </div>
    </div>

    <!-- 3. Sách Đang Mượn & Hạn Trả (Chỉ hiện các cuốn chưa trả, không lặp) -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                    <i data-lucide="bookmark-check" class="w-4 h-4"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-900">Sách Đang Mượn & Hạn Trả</h2>
                    <p class="text-xs text-slate-500">Tự động tính phí phạt trễ hạn {{ number_format($rules->fine_per_day ?? 5000) }}đ/ngày theo quy định</p>
                </div>
            </div>
            @php
                $activeLoans = $tickets->where('status', '!=', 'returned');
            @endphp
            <span class="text-xs font-semibold px-2.5 py-1 bg-slate-100 text-slate-700 rounded-full">
                {{ $activeLoans->count() }} cuốn đang giữ
            </span>
        </div>

        @if($activeLoans->count() === 0)
            <div class="text-center py-8 text-slate-400 text-xs">
                Hiện bạn không có sách nào đang mượn. Hãy tra cứu kho sách bên dưới để chọn mượn nhé!
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($activeLoans as $t)
                    <div class="p-4 rounded-2xl border {{ $t->status === 'overdue' ? 'border-red-200 bg-red-50/40' : 'border-slate-200 bg-slate-50/50' }} flex flex-col justify-between gap-3">
                        <div class="space-y-1">
                            <div class="flex items-center justify-between text-[11px]">
                                <span class="font-mono text-slate-400 font-semibold">#{{ $t->ticket_code }}</span>
                                @if($t->status === 'overdue')
                                    <span class="px-2 py-0.5 rounded-md font-bold text-[10px] bg-red-100 text-red-700">Trễ {{ $t->overdue_days }} ngày</span>
                                @elseif($t->status === 'pending')
                                    <span class="px-2 py-0.5 rounded-md font-bold text-[10px] bg-amber-100 text-amber-800">Chờ nhận sách</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-md font-bold text-[10px] bg-blue-100 text-blue-700">Đang mượn</span>
                                @endif
                            </div>
                            <h3 class="font-bold text-sm text-slate-900 line-clamp-1">{{ $t->book->title ?? 'Sách thư viện' }}</h3>
                            <p class="text-xs text-slate-500">Tác giả: {{ $t->book->author ?? 'Đang cập nhật' }}</p>
                            <p class="text-xs text-slate-600 font-medium">Hạn trả: <strong>{{ \Carbon\Carbon::parse($t->due_date)->format('d/m/Y') }}</strong></p>
                            
                            @if($t->fine_amount > 0 && $t->payment_status === 'unpaid')
                                <div class="p-2 bg-red-100/80 rounded-xl text-xs text-red-800 font-semibold flex items-center justify-between mt-2">
                                    <span>Phạt trễ hạn:</span>
                                    <span>{{ number_format($t->fine_amount) }}đ</span>
                                </div>
                            @endif
                        </div>

                        <!-- Ticket Action Buttons -->
                        <div class="flex items-center gap-2 pt-2 border-t border-slate-200/60">
                            @if($t->status !== 'returned' && $t->status !== 'pending')
                                <form action="{{ route('reader.loan.renew', $t->id) }}" method="POST" class="flex-1">
                                    @csrf
                                    <button type="submit" class="w-full py-1.5 px-2.5 text-xs font-semibold bg-white border border-slate-200 hover:bg-slate-100 rounded-lg text-slate-700 transition" {{ $t->renew_count >= ($rules->max_renewal_times ?? 2) ? 'disabled title="Đã hết lượt gia hạn"' : '' }}>
                                        Gia hạn (+7 ngày)
                                    </button>
                                </form>
                                @if($t->fine_amount > 0 && $t->payment_status === 'unpaid')
                                    <button type="button" onclick="openVietQRModal('{{ $t->fine_amount }}', 'NOP PHAT PHIEU {{ $t->ticket_code }}', '{{ $t->id }}')" class="py-1.5 px-3 text-xs font-semibold bg-red-600 hover:bg-red-700 text-white rounded-lg transition flex items-center gap-1">
                                        <i data-lucide="qr-code" class="w-3.5 h-3.5"></i>
                                        Nộp phạt
                                    </button>
                                @endif
                            @endif
                            <button type="button" 
                                data-id="{{ $t->book_id }}" 
                                data-title="{{ $t->book->title ?? '' }}" 
                                onclick="openRateModal(this.dataset.id, this.dataset.title)" 
                                class="p-1.5 text-amber-500 hover:bg-amber-50 rounded-lg border border-slate-200 transition"
                                title="Đánh giá sách">
                                <i data-lucide="star" class="w-3.5 h-3.5"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- 4. KHU VỰC LỊCH SỬ MƯỢN - TRẢ & GIAO DỊCH (ĐƯỢC LÀM ĐẸP RỘNG RÃI, KHÔNG BỊ CO CỘT) -->
    <div id="history-section" class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200 shadow-sm space-y-5 scroll-mt-20">
        <!-- Header & Tab Switches -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-slate-100 pb-5">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center shadow-xs flex-shrink-0">
                    <i data-lucide="history" class="w-5 h-5"></i>
                </div>
                <div>
                    <h2 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                        Lịch Sử Mượn - Trả & Giao Dịch
                        <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200">Độc Giả</span>
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">Theo dõi chi tiết tất cả các lượt mượn trả sách, gia hạn và các giao dịch tài chính</p>
                </div>
            </div>

            <!-- Tab Buttons -->
            <div class="flex items-center p-1.5 bg-slate-100/90 rounded-2xl border border-slate-200/80 self-start lg:self-auto">
                <button type="button" 
                    id="btn-tab-borrow" 
                    onclick="switchHistoryTab('borrow')" 
                    class="px-4 py-2 text-xs font-bold rounded-xl transition flex items-center gap-2 bg-white text-indigo-900 shadow-xs">
                    <i data-lucide="book-check" class="w-4 h-4 text-indigo-600"></i>
                    <span>Lịch Sử Mượn - Trả Sách</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">{{ $tickets->count() }}</span>
                </button>

                <button type="button" 
                    id="btn-tab-card" 
                    onclick="switchHistoryTab('card')" 
                    class="px-4 py-2 text-xs font-bold rounded-xl transition flex items-center gap-2 text-slate-600 hover:text-slate-900">
                    <i data-lucide="credit-card" class="w-4 h-4 text-slate-400"></i>
                    <span>Lịch Sử Giao Dịch Thẻ & Phí</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-200 text-slate-700">{{ $allTransactions->count() }}</span>
                </button>
            </div>
        </div>

        <!-- ================= TAB 1: LỊCH SỬ MƯỢN - TRẢ SÁCH ================= -->
        <div id="tab-content-borrow" class="space-y-4">
            <!-- Filter Bar -->
            <div class="flex flex-wrap items-center justify-between gap-3 bg-slate-50 p-3.5 rounded-2xl border border-slate-200/80">
                <div class="relative flex-1 min-w-[240px]">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                    <input type="text" id="filter-borrow-kw" onkeyup="filterBorrowTable()" placeholder="Tìm nhanh theo Tên sách, ISBN, Mã phiếu mượn..." class="w-full pl-9 pr-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 transition shadow-2xs">
                </div>

                <div class="flex items-center gap-2 text-xs">
                    <label class="text-slate-500 font-medium hidden sm:inline">Trạng thái:</label>
                    <select id="filter-borrow-status" onchange="filterBorrowTable()" class="py-2 px-3 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 shadow-2xs text-slate-700">
                        <option value="">Tất cả trạng thái</option>
                        <option value="borrowing">Đang mượn</option>
                        <option value="returned">Đã trả sách</option>
                        <option value="overdue">Quá hạn</option>
                        <option value="pending">Chờ nhận sách</option>
                    </select>

                    <button type="button" onclick="resetBorrowFilters()" class="py-2 px-3.5 bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold rounded-xl text-xs transition">
                        Đặt lại
                    </button>
                </div>
            </div>

            <!-- Table of Borrow - Return History -->
            <div class="overflow-x-auto rounded-2xl border border-slate-200 shadow-sm bg-white">
                <table class="w-full text-left border-collapse min-w-[1020px]" id="table-borrow-history">
                    <thead>
                        <tr class="bg-slate-100/90 text-slate-700 text-[11px] font-bold uppercase tracking-wider border-b border-slate-200">
                            <th class="py-3.5 px-4 w-[140px]">MÃ THẺ</th>
                            <th class="py-3.5 px-4 w-[160px]">TÊN NGƯỜI DÙNG</th>
                            <th class="py-3.5 px-4 w-[150px]">NGÀY THỰC HIỆN</th>
                            <th class="py-3.5 px-4 min-w-[240px]">TÊN SÁCH (TÊN GIAO DỊCH)</th>
                            <th class="py-3.5 px-4 w-[150px]">MÃ SÁCH</th>
                            <th class="py-3.5 px-4 w-[150px]">MÃ GIAO DỊCH</th>
                            <th class="py-3.5 px-4 w-[130px]">PHÍ GIAO DỊCH</th>
                            <th class="py-3.5 px-4 w-[120px] text-center">TRẠNG THÁI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                        @forelse($tickets as $t)
                            <tr class="hover:bg-indigo-50/40 transition borrow-row" 
                                data-title="{{ strtolower($t->book->title ?? '') }}" 
                                data-isbn="{{ strtolower($t->book->isbn ?? '') }}" 
                                data-code="{{ strtolower($t->ticket_code) }}" 
                                data-status="{{ $t->status }}">
                                <!-- 1. Mã Thẻ -->
                                <td class="py-3.5 px-4 font-mono font-semibold text-slate-900 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-sky-50 text-sky-800 border border-sky-200 text-[11px]">
                                        <i data-lucide="credit-card" class="w-3.5 h-3.5 text-sky-600"></i>
                                        {{ $user->card_number ?? $t->reader->card_number ?? 'LIB-2026-8899' }}
                                    </span>
                                </td>

                                <!-- 2. Tên Người Dùng -->
                                <td class="py-3.5 px-4 font-semibold text-slate-900 whitespace-nowrap">
                                    {{ $user->name ?? $t->reader->name ?? 'Độc giả' }}
                                </td>

                                <!-- 3. Ngày Thực Hiện -->
                                <td class="py-3.5 px-4 text-slate-600 whitespace-nowrap space-y-1">
                                    <div class="flex items-center gap-1.5 text-xs">
                                        <span class="text-[10px] uppercase font-bold text-slate-400">Mượn:</span>
                                        <span class="font-medium text-slate-800">{{ \Carbon\Carbon::parse($t->borrow_date)->format('d/m/Y') }}</span>
                                    </div>
                                    <div class="flex items-center gap-1.5 text-[11px] text-slate-500">
                                        <span class="text-[10px] uppercase font-bold text-slate-400">Hạn:</span>
                                        <span>{{ \Carbon\Carbon::parse($t->due_date)->format('d/m/Y') }}</span>
                                    </div>
                                    @if($t->return_date || $t->status === 'returned')
                                        <div class="flex items-center gap-1.5 text-[11px] text-emerald-700 font-semibold">
                                            <span class="text-[10px] uppercase font-bold text-emerald-600">Trả:</span>
                                            <span>{{ $t->return_date ? \Carbon\Carbon::parse($t->return_date)->format('d/m/Y') : \Carbon\Carbon::parse($t->updated_at)->format('d/m/Y') }}</span>
                                        </div>
                                    @endif
                                </td>

                                <!-- 4. Tên Sách (Tên Giao Dịch) -->
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-900 text-xs sm:text-sm hover:text-indigo-600 transition cursor-pointer" onclick="document.getElementById('btn-view-{{ $t->book_id }}')?.click()">
                                        {{ $t->book->title ?? 'Sách thư viện' }}
                                    </div>
                                    <div class="mt-1 flex items-center gap-1.5">
                                        @if($t->status === 'returned')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200 text-[11px] font-medium">
                                                <i data-lucide="check" class="w-3 h-3 text-emerald-600"></i>
                                                Trả sách vào kho lưu thông
                                            </span>
                                        @elseif($t->status === 'overdue')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-rose-50 text-rose-700 border border-rose-200 text-[11px] font-medium">
                                                <i data-lucide="alert-circle" class="w-3 h-3 text-rose-600"></i>
                                                Mượn sách (Quá hạn)
                                            </span>
                                        @elseif($t->status === 'pending')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-amber-50 text-amber-800 border border-amber-200 text-[11px] font-medium">
                                                <i data-lucide="clock" class="w-3 h-3 text-amber-600"></i>
                                                Yêu cầu mượn trực tuyến
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-sky-50 text-sky-800 border border-sky-200 text-[11px] font-medium">
                                                <i data-lucide="book-open" class="w-3 h-3 text-sky-600"></i>
                                                Mượn sách đọc tại nhà {{ $t->renew_count > 0 ? "(Gia hạn {$t->renew_count} lần)" : '' }}
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <!-- 5. Mã Sách -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="font-mono text-xs font-semibold text-slate-800 bg-slate-100 px-2.5 py-1 rounded-md border border-slate-200 inline-block">
                                        {{ $t->book->isbn ?? ('BK-' . str_pad($t->book_id, 4, '0', STR_PAD_LEFT)) }}
                                    </span>
                                    <div class="text-[11px] text-slate-500 mt-1 flex items-center gap-1">
                                        <i data-lucide="map-pin" class="w-3 h-3 text-amber-600"></i>
                                        <span>{{ $t->book->shelf_location ?? 'Kệ A1' }}</span>
                                    </div>
                                </td>

                                <!-- 6. Mã Giao Dịch -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="font-mono font-bold text-indigo-700 bg-indigo-50 px-2.5 py-1 rounded-lg border border-indigo-200 text-xs inline-block">
                                        #{{ $t->ticket_code }}
                                    </span>
                                </td>

                                <!-- 7. Phí Giao Dịch -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    @if($t->fine_amount > 0)
                                        <div class="font-bold text-rose-600 text-xs">{{ number_format($t->fine_amount) }}đ</div>
                                        @if($t->payment_status === 'paid')
                                            <span class="inline-flex items-center gap-0.5 text-[10px] text-emerald-700 font-semibold mt-0.5">
                                                <i data-lucide="check" class="w-3 h-3"></i> Đã nộp
                                            </span>
                                        @else
                                            <button type="button" onclick="openVietQRModal('{{ $t->fine_amount }}', 'NOP PHAT PHIEU {{ $t->ticket_code }}', '{{ $t->id }}')" class="mt-1 inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-bold bg-rose-600 hover:bg-rose-700 text-white rounded transition shadow-2xs">
                                                <i data-lucide="qr-code" class="w-3 h-3"></i> Nộp phạt
                                            </button>
                                        @endif
                                    @else
                                        <span class="inline-block px-2.5 py-0.5 text-[10px] font-semibold text-emerald-700 bg-emerald-50 rounded-full border border-emerald-200">
                                            0đ (Miễn phí)
                                        </span>
                                    @endif
                                </td>

                                <!-- 8. Trạng Thái -->
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded-full font-bold text-[10px] inline-block {{ $t->status === 'returned' ? 'bg-slate-100 text-slate-700 border border-slate-200' : ($t->status === 'overdue' ? 'bg-red-100 text-red-700 border border-red-200' : ($t->status === 'pending' ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-blue-100 text-blue-700 border border-blue-200')) }}">
                                        {{ $t->status === 'returned' ? 'Đã Trả Sách' : ($t->status === 'overdue' ? 'Quá Hạn' : ($t->status === 'pending' ? 'Chờ Nhận' : 'Đang Mượn')) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-10 text-slate-400 text-xs">
                                    Chưa có dữ liệu nhật ký mượn - trả sách.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ================= TAB 2: LỊCH SỬ GIA HẠN THẺ & GIAO DỊCH ================= -->
        <div id="tab-content-card" class="space-y-4 hidden">
            <!-- Filter Bar for Transactions -->
            <div class="flex flex-wrap items-center justify-between gap-3 bg-slate-50 p-3.5 rounded-2xl border border-slate-200/80">
                <div class="flex items-center gap-3">
                    <span class="text-xs text-slate-600">Tổng số giao dịch: <strong class="text-slate-900 font-bold">{{ $allTransactions->count() }} lượt</strong></span>
                    <span class="text-slate-300">|</span>
                    <span class="text-xs text-slate-600">Tổng tiền đã trả: <strong class="text-indigo-700 font-bold">{{ number_format($allTransactions->where('status', 'completed')->sum('amount')) }} VNĐ</strong></span>
                </div>
                <div class="relative min-w-[240px]">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                    <input type="text" id="filter-card-kw" onkeyup="filterCardTable()" placeholder="Tìm theo Mã giao dịch, Nội dung..." class="w-full pl-9 pr-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 transition shadow-2xs">
                </div>
            </div>

            <!-- Table of Card Transactions -->
            <div class="overflow-x-auto rounded-2xl border border-slate-200 shadow-sm bg-white">
                <table class="w-full text-left border-collapse min-w-[1020px]" id="table-card-history">
                    <thead>
                        <tr class="bg-slate-100/90 text-slate-700 text-[11px] font-bold uppercase tracking-wider border-b border-slate-200">
                            <th class="py-3.5 px-4 w-[140px]">MÃ THẺ</th>
                            <th class="py-3.5 px-4 w-[160px]">TÊN NGƯỜI DÙNG</th>
                            <th class="py-3.5 px-4 w-[150px]">NGÀY THỰC HIỆN</th>
                            <th class="py-3.5 px-4 min-w-[240px]">TÊN SÁCH (TÊN GIAO DỊCH)</th>
                            <th class="py-3.5 px-4 w-[150px]">MÃ SÁCH</th>
                            <th class="py-3.5 px-4 w-[150px]">MÃ GIAO DỊCH</th>
                            <th class="py-3.5 px-4 w-[130px]">PHÍ GIAO DỊCH</th>
                            <th class="py-3.5 px-4 w-[120px] text-center">TRẠNG THÁI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                        @forelse($allTransactions as $tx)
                            <tr class="hover:bg-indigo-50/40 transition card-tx-row"
                                data-search="{{ strtolower($tx->transaction_code . ' ' . $tx->description . ' ' . $tx->payment_method) }}">
                                <!-- 1. Mã Thẻ -->
                                <td class="py-3.5 px-4 font-mono font-semibold text-slate-900 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-sky-50 text-sky-800 border border-sky-200 text-[11px]">
                                        <i data-lucide="credit-card" class="w-3.5 h-3.5 text-sky-600"></i>
                                        {{ $user->card_number ?? 'LIB-2026-8899' }}
                                    </span>
                                </td>

                                <!-- 2. Tên Người Dùng -->
                                <td class="py-3.5 px-4 font-semibold text-slate-900 whitespace-nowrap">
                                    {{ $tx->reader_name ?? $user->name ?? 'Độc giả' }}
                                </td>

                                <!-- 3. Ngày Thực Hiện -->
                                <td class="py-3.5 px-4 text-slate-600 whitespace-nowrap font-mono text-xs">
                                    <div class="font-bold text-slate-900">{{ \Carbon\Carbon::parse($tx->created_at)->format('H:i') }}</div>
                                    <div class="text-[11px] text-slate-500">{{ \Carbon\Carbon::parse($tx->created_at)->format('d/m/Y') }}</div>
                                </td>

                                <!-- 4. Tên Sách (Tên Giao Dịch) -->
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-900 text-xs sm:text-sm">
                                        @if($tx->type === 'card_renewal')
                                            Gia hạn thẻ thư viện thường niên 1 năm
                                        @elseif($tx->type === 'fine')
                                            Nộp phạt vi phạm trễ hạn
                                        @else
                                            Thanh toán dịch vụ thư viện
                                        @endif
                                    </div>
                                    <p class="text-[11px] text-slate-500 mt-1 line-clamp-1">{{ $tx->description }}</p>
                                </td>

                                <!-- 5. Mã Sách -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    @if($tx->ticket && $tx->ticket->book)
                                        <span class="font-mono text-xs font-semibold text-slate-800 bg-slate-100 px-2.5 py-1 rounded-md border border-slate-200 inline-block">
                                            {{ $tx->ticket->book->isbn ?? ('BK-' . $tx->ticket->book_id) }}
                                        </span>
                                    @else
                                        <span class="font-mono text-[11px] font-semibold text-sky-800 bg-sky-50 px-2.5 py-1 rounded-md border border-sky-200 inline-block">
                                            THE-THU-VIEN
                                        </span>
                                    @endif
                                </td>

                                <!-- 6. Mã Giao Dịch -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="font-mono font-bold text-sky-700 bg-sky-50 px-2.5 py-1 rounded-lg border border-sky-200 text-xs inline-block">
                                        #{{ $tx->transaction_code }}
                                    </span>
                                </td>

                                <!-- 7. Phí Giao Dịch -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <div class="font-bold text-slate-900 text-xs">{{ number_format($tx->amount) }} VNĐ</div>
                                    <span class="inline-block mt-0.5 text-[10px] text-slate-500 uppercase font-semibold">{{ $tx->payment_method ?? 'VietQR' }}</span>
                                </td>

                                <!-- 8. Trạng Thái -->
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded-full font-bold text-[10px] inline-block bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        Hoàn Tất
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-10 text-slate-400 text-xs">
                                    Chưa có giao dịch gia hạn thẻ hoặc nộp phí nào được ghi nhận.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- 5. Tra Cứu Kho Sách Trực Tuyến -->
    <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200 shadow-sm space-y-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-100 pb-5">
            <div>
                <h2 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight">Tra Cứu Kho Sách Trực Tuyến</h2>
                <p class="text-xs text-slate-500 mt-0.5">Xem thông tin chi tiết, định vị vị trí kệ và đăng ký mượn sách trực tuyến</p>
            </div>

            <!-- Search Form -->
            <form action="{{ route('reader.index') }}" method="GET" id="reader-search-form" class="space-y-2">
                <div class="flex flex-wrap items-center gap-2">
                    <div class="relative flex-1 min-w-[200px]">
                        <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                        <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="Tên sách, tác giả, NXB, ISBN, vị trí..." class="w-full pl-9 pr-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-500/20 focus:bg-white transition">
                    </div>

                    <!-- Nút mở Modal Tag Sách -->
                    <div class="relative">
                        <button type="button" onclick="openReaderTagModal()" id="btn-reader-tag-filter" class="flex items-center gap-1.5 py-2 px-3 text-xs border rounded-xl transition {{ request('selected_tags') ? 'bg-sky-50 text-sky-800 border-sky-300 font-semibold shadow-2xs' : 'bg-slate-50 hover:bg-slate-100 text-slate-700 border-slate-200' }}">
                            <i data-lucide="tags" class="w-3.5 h-3.5 text-sky-600"></i>
                            <span id="reader-tag-button-label">
                                @if(request('selected_tags'))
                                    Lọc tag ({{ count(array_filter(explode(',', request('selected_tags')))) }})
                                @else
                                    Tất cả thể loại / nhãn tag
                                @endif
                            </span>
                        </button>
                        <input type="hidden" name="selected_tags" id="reader_selected_tags_input" value="{{ request('selected_tags') }}">
                    </div>

                    <select name="status" class="py-2 px-3 text-xs bg-slate-50 border border-slate-200 rounded-xl">
                        <option value="">Tất cả tình trạng</option>
                        <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Còn sách</option>
                        <option value="low_stock" {{ request('status') == 'low_stock' ? 'selected' : '' }}>Sắp hết (≤ 2 cuốn)</option>
                        <option value="out_of_stock" {{ request('status') == 'out_of_stock' ? 'selected' : '' }}>Hết lượt</option>
                    </select>

                    <button type="submit" class="py-2 px-4 bg-sky-600 hover:bg-sky-700 text-white font-semibold text-xs rounded-xl shadow-xs transition">Lọc</button>
                </div>

                <!-- Hiển thị badge các tag đang lọc -->
                @if(request('selected_tags'))
                    <div class="flex flex-wrap items-center gap-1.5 pt-1 text-xs">
                        <span class="text-slate-500 font-medium text-[11px]">Đang lọc tag:</span>
                        @foreach(array_filter(array_map('trim', explode(',', request('selected_tags')))) as $tag)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-sky-100 text-sky-800 text-[11px] font-semibold rounded-full border border-sky-200">
                                <span>{{ $tag }}</span>
                                <button type="button" onclick="removeSingleTag('{{ $tag }}')" class="hover:text-sky-950 ml-0.5">
                                    <i data-lucide="x" class="w-3 h-3"></i>
                                </button>
                            </span>
                        @endforeach
                    </div>
                @endif
            </form>
        </div>

        <!-- Book Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($books as $b)
                <div class="bg-slate-50/70 border border-slate-200/90 rounded-2xl p-4 flex flex-col justify-between hover:shadow-md transition">
                    <div class="space-y-3">
                        <!-- Ảnh bìa sách (Click để xem chi tiết) -->
                        <div class="relative h-44 rounded-xl overflow-hidden bg-slate-200 cursor-pointer group" onclick="document.getElementById('btn-view-{{ $b->id }}')?.click()">
                            <img src="{{ $b->cover_url }}" alt="{{ $b->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            <div class="absolute top-2 right-2">
                                @if($b->available_qty > 0)
                                    <span class="px-2 py-0.5 bg-emerald-600/90 text-white font-bold text-[10px] rounded-md backdrop-blur-sm shadow">
                                        Còn {{ $b->available_qty }}/{{ $b->total_qty }} cuốn
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 bg-red-600/90 text-white font-bold text-[10px] rounded-md backdrop-blur-sm shadow">
                                        Hết sách
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div>
                            <div class="text-[10px] font-mono text-sky-700 uppercase font-semibold">{{ $b->category->name ?? 'Chung' }}</div>
                            <h3 class="font-bold text-sm text-slate-900 leading-snug line-clamp-2 mt-0.5 cursor-pointer hover:text-sky-600 transition" onclick="document.getElementById('btn-view-{{ $b->id }}')?.click()">
                                {{ $b->title }}
                            </h3>
                            <p class="text-xs text-slate-500 mt-1">Tác giả: <span class="font-medium text-slate-700">{{ $b->author }}</span></p>
                            <p class="text-[11px] text-slate-400">NXB: {{ $b->publisher->name ?? 'Thư viện' }} ({{ $b->publish_year }})</p>
                        </div>

                        <div class="p-2.5 bg-amber-50/90 border border-amber-200/70 rounded-xl flex items-start gap-2 text-xs text-amber-900">
                            <i data-lucide="map-pin" class="w-4 h-4 text-amber-600 flex-shrink-0 mt-0.5"></i>
                            <div>
                                <div class="text-[10px] uppercase font-bold text-amber-700 tracking-wider">Vị Trí Trên Kệ:</div>
                                <div class="font-semibold text-xs">{{ $b->shelf_location }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Chân thẻ: Đánh giá & NÚT XEM -->
                    <div class="mt-4 pt-3 border-t border-slate-200/60 flex items-center justify-between text-xs">
                        <div class="flex items-center gap-1 text-amber-500 font-bold">
                            <i data-lucide="star" class="w-3.5 h-3.5 fill-current"></i>
                            <span>{{ $b->rating }}</span>
                            <span class="text-slate-400 font-normal text-[11px]">({{ $b->rating_count }})</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="openRateModal('{{ $b->id }}', '{{ addslashes($b->title) }}')" class="text-slate-500 hover:text-slate-700 font-medium">
                                Đánh giá
                            </button>
                            <button type="button" 
                                id="btn-view-{{ $b->id }}"
                                class="px-3 py-1.5 bg-sky-600 hover:bg-sky-700 text-white font-semibold text-xs rounded-xl shadow-xs flex items-center gap-1.5 transition"
                                data-book-id="{{ $b->id }}"
                                data-book-title="{{ $b->title }}"
                                data-book-author="{{ $b->author }}"
                                data-book-isbn="{{ $b->isbn }}"
                                data-book-category="{{ $b->category->name ?? 'Tổng Hợp' }}"
                                data-book-publisher="{{ $b->publisher->name ?? 'Thư viện' }}"
                                data-book-year="{{ $b->publish_year }}"
                                data-book-location="{{ $b->shelf_location }}"
                                data-book-cover="{{ $b->cover_url }}"
                                data-book-available="{{ $b->available_qty }}"
                                data-book-total="{{ $b->total_qty }}"
                                data-book-rating="{{ $b->rating }}"
                                data-book-rating-count="{{ $b->rating_count }}"
                                data-book-description="{{ $b->description }}"
                                onclick="openBookDetailModal(this)">
                                <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                <span>Xem</span>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- ================= MODAL: XEM CHI TIẾT SÁCH & MƯỢN SÁCH ================= -->
<div id="modal-book-detail" class="fixed inset-0 z-[100] bg-slate-950/60 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-xl w-full p-6 shadow-2xl space-y-4 border border-slate-200">
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center">
                    <i data-lucide="book-open" class="w-4 h-4"></i>
                </div>
                <h3 class="font-bold text-slate-900 text-sm">Thông Tin Chi Tiết Đầu Sách</h3>
            </div>
            <button type="button" onclick="closeModal('modal-book-detail')" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Book Main Info (Cover + Metadata) -->
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="w-28 sm:w-32 h-44 rounded-2xl overflow-hidden bg-slate-100 border border-slate-200 flex-shrink-0 shadow-sm">
                <img id="modal_detail_cover" src="" alt="Ảnh bìa sách" class="w-full h-full object-cover">
            </div>
            <div class="space-y-1.5 flex-1 min-w-0 text-xs">
                <div class="flex items-center gap-2">
                    <span id="modal_detail_category" class="px-2 py-0.5 bg-sky-50 text-sky-700 font-semibold text-[10px] rounded-md border border-sky-200 uppercase">
                        Thể loại
                    </span>
                    <span id="modal_detail_stock_badge" class="px-2 py-0.5 rounded-full font-bold text-[10px]">
                        Còn sách
                    </span>
                </div>
                <h2 id="modal_detail_title" class="text-base sm:text-lg font-bold text-slate-900 leading-snug"></h2>
                <p class="text-slate-600">Tác giả: <strong id="modal_detail_author" class="text-slate-800"></strong></p>
                <p class="text-slate-500">NXB: <span id="modal_detail_publisher"></span> <span id="modal_detail_year"></span></p>
                <p class="text-slate-400 font-mono text-[11px]">ISBN: <span id="modal_detail_isbn"></span></p>
                <div class="flex items-center gap-1 text-amber-500 font-bold pt-1">
                    <i data-lucide="star" class="w-3.5 h-3.5 fill-current"></i>
                    <span id="modal_detail_rating">5.0</span>
                    <span id="modal_detail_rating_count" class="text-slate-400 font-normal text-[11px]">(0)</span>
                </div>
            </div>
        </div>

        <!-- Vị trí kệ sách chính xác -->
        <div class="p-3 bg-amber-50/90 border border-amber-200/80 rounded-2xl flex items-center gap-3 text-xs text-amber-900">
            <div class="w-8 h-8 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0 text-amber-700">
                <i data-lucide="map-pin" class="w-4 h-4"></i>
            </div>
            <div>
                <div class="text-[10px] uppercase font-bold text-amber-800 tracking-wider">Vị Trí Định Vị Kệ Sách:</div>
                <div id="modal_detail_location" class="font-bold text-slate-900 text-sm"></div>
            </div>
        </div>

        <!-- Giới thiệu tóm tắt nội dung sách -->
        <div class="bg-slate-50 border border-slate-200 rounded-2xl p-3 text-xs space-y-1">
            <div class="font-bold text-slate-800 flex items-center gap-1.5">
                <i data-lucide="file-text" class="w-3.5 h-3.5 text-sky-600"></i>
                <span>Tóm Tắt Nội Dung / Giới Thiệu Tác Phẩm:</span>
            </div>
            <p id="modal_detail_description" class="text-slate-600 leading-relaxed text-[11px] max-h-28 overflow-y-auto pr-1 whitespace-pre-line"></p>
        </div>

        <!-- Action / Form Mượn Sách gửi qua Thủ thư -->
        <form action="{{ url('/reader/borrow') }}" method="POST" id="form-borrow-book" class="pt-2 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
            @csrf
            <input type="hidden" name="book_id" id="modal_borrow_book_id" value="">
            <span class="text-[11px] text-slate-500">Yêu cầu mượn sẽ được gửi ngay đến quầy ca trực của Thủ thư.</span>

            <div class="flex items-center gap-2">
                <button type="button" onclick="closeModal('modal-book-detail')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition">
                    Đóng
                </button>
                <button type="submit" id="modal_detail_borrow_btn" class="px-5 py-2 bg-sky-600 hover:bg-sky-700 text-white font-bold text-xs rounded-xl shadow transition flex items-center justify-center gap-1.5 min-w-[120px]">
                    <i data-lucide="bookmark-plus" class="w-4 h-4"></i>
                    <span>Mượn Sách</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: VietQR Online Payment -->
<div id="vietqr-modal" class="fixed inset-0 z-[100] bg-slate-950/60 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4 border border-slate-200">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center">
                    <i data-lucide="qr-code" class="w-4 h-4"></i>
                </div>
                <h3 class="font-bold text-slate-900 text-sm">Thanh Toán VietQR Napas 247</h3>
            </div>
            <button type="button" onclick="closeVietQRModal()" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <div class="text-center space-y-2">
            <div class="w-56 h-56 mx-auto bg-slate-100 rounded-2xl border border-slate-200 p-2 flex items-center justify-center shadow-inner">
                <img id="modal-qr-image" src="" alt="Mã QR Napas 247" class="w-full h-full object-contain rounded-xl">
            </div>
            <p class="text-xs text-slate-500">Mở ứng dụng Ngân hàng (MB, Vietcombank, Techcombank...) hoặc Ví MoMo để quét mã</p>
        </div>

        <div class="bg-slate-50 p-3 rounded-2xl border border-slate-200 text-xs space-y-1">
            <div class="flex justify-between text-slate-500">
                <span>Số tiền thanh toán:</span>
                <span id="modal-qr-amount" class="font-bold text-sky-700 text-sm">0đ</span>
            </div>
            <div class="flex justify-between text-slate-500">
                <span>Nội dung chuyển khoản:</span>
                <span id="modal-qr-desc" class="font-mono text-slate-700 font-semibold text-[11px]">NOP PHAT</span>
            </div>
            <div class="flex justify-between text-slate-500">
                <span>Ngân hàng & STK:</span>
                <span class="font-semibold text-slate-700">{{ $rules->bank_name ?? 'MB Bank' }} - <span class="font-mono text-sky-700">{{ $rules->bank_account ?? '0987654321' }}</span></span>
            </div>
            <div class="flex justify-between text-slate-500">
                <span>Đơn vị thụ hưởng:</span>
                <span class="font-semibold text-slate-700 uppercase">{{ $rules->account_holder ?? 'THU VIEN LIBRANOVA QUOC GIA' }}</span>
            </div>
        </div>

        <div class="flex items-center gap-2 pt-2">
            <button type="button" onclick="confirmPaymentSuccess()" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-xl shadow transition flex items-center justify-center gap-2">
                <i data-lucide="check-circle" class="w-4 h-4"></i>
                Đã Hoàn Tất Chuyển Tiền
            </button>
        </div>
    </div>
</div>

<!-- Modal: Card Renewal -->
<div id="card-renew-modal" class="fixed inset-0 z-[100] bg-slate-950/60 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl space-y-4 border border-slate-200">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-bold text-slate-900 text-sm">Gia Hạn Thẻ Thư Viện Thường Niên</h3>
            <button type="button" onclick="closeCardRenewModal()" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <p class="text-xs text-slate-500 leading-relaxed">
            Phí gia hạn niên khóa: <strong>{{ number_format($rules->card_renewal_fee ?? 30000) }} VNĐ / 12 tháng</strong>. Thẻ của bạn sẽ được tự động cộng thêm 1 năm tính từ hạn hiện tại.
        </p>
        <form action="{{ route('reader.card.renew') }}" method="POST" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Phương thức thanh toán</label>
                <select name="payment_method" class="w-full py-2 px-3 text-xs bg-slate-50 border border-slate-200 rounded-xl">
                    <option value="vietqr">Quét mã VietQR tự động</option>
                    <option value="momo">Ví điện tử MoMo</option>
                    <option value="vnpay">Cổng VNPAY-QR</option>
                </select>
            </div>
            <button type="submit" class="w-full py-2.5 bg-sky-600 hover:bg-sky-700 text-white font-semibold text-xs rounded-xl shadow transition">
                Xác Nhận & Gia Hạn
            </button>
        </form>
    </div>
</div>

<!-- Modal: Book Rating -->
<div id="rate-modal" class="fixed inset-0 z-[100] bg-slate-950/60 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl space-y-4 border border-slate-200">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="font-bold text-slate-900 text-sm">Đánh Giá Tác Phẩm</h3>
            <button type="button" onclick="closeRateModal()" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form action="{{ route('reader.book.rate') }}" method="POST" class="space-y-3">
            @csrf
            <input type="hidden" name="book_id" id="rate-book-id">
            <div>
                <p id="rate-book-title" class="text-xs font-bold text-slate-800 line-clamp-1 mb-2"></p>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Mức độ hài lòng</label>
                <select name="rating" class="w-full py-2 px-3 text-xs bg-slate-50 border border-slate-200 rounded-xl">
                    <option value="5">⭐⭐⭐⭐⭐ (5 sao - Xuất sắc)</option>
                    <option value="4">⭐⭐⭐⭐ (4 sao - Rất hay)</option>
                    <option value="3">⭐⭐⭐ (3 sao - Tốt)</option>
                    <option value="2">⭐⭐ (2 sao - Bình thường)</option>
                    <option value="1">⭐ (1 sao - Cần cải thiện)</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Cảm nhận của bạn (Tùy chọn)</label>
                <textarea name="comment" rows="2" class="w-full py-2 px-3 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-sky-500/20" placeholder="Nội dung sách rất bổ ích..."></textarea>
            </div>
            <button type="submit" class="w-full py-2 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs rounded-xl shadow transition">
                Gửi Đánh Giá
            </button>
        </form>
    </div>
</div>

<!-- Cấu hình lưu trữ dữ liệu an toàn cho JavaScript -->
<input type="hidden" id="initial_selected_tags_storage" value="{{ request('selected_tags', '') }}">
<input type="hidden" id="cfg_bank_name" value="{{ $rules->bank_name ?? 'MB Bank' }}">
<input type="hidden" id="cfg_bank_account" value="{{ $rules->bank_account ?? '0987654321' }}">
<input type="hidden" id="cfg_account_holder" value="{{ $rules->account_holder ?? 'THU VIEN LIBRANOVA QUOC GIA' }}">
<input type="hidden" id="cfg_csrf_token" value="{{ csrf_token() }}">

<script>
    let activeTicketId = null;

    // Chuyển đổi giữa 2 Tab: Lịch Sử Mượn - Trả Sách & Lịch Sử Gia Hạn Thẻ
    function switchHistoryTab(tab) {
        const btnBorrow = document.getElementById('btn-tab-borrow');
        const btnCard = document.getElementById('btn-tab-card');
        const contentBorrow = document.getElementById('tab-content-borrow');
        const contentCard = document.getElementById('tab-content-card');

        if (!btnBorrow || !btnCard || !contentBorrow || !contentCard) return;

        if (tab === 'borrow') {
            btnBorrow.className = 'px-4 py-2 text-xs font-bold rounded-xl transition flex items-center gap-2 bg-white text-indigo-900 shadow-xs';
            btnCard.className = 'px-4 py-2 text-xs font-bold rounded-xl transition flex items-center gap-2 text-slate-600 hover:text-slate-900';
            contentBorrow.classList.remove('hidden');
            contentCard.classList.add('hidden');
        } else {
            btnCard.className = 'px-4 py-2 text-xs font-bold rounded-xl transition flex items-center gap-2 bg-white text-indigo-900 shadow-xs';
            btnBorrow.className = 'px-4 py-2 text-xs font-bold rounded-xl transition flex items-center gap-2 text-slate-600 hover:text-slate-900';
            contentCard.classList.remove('hidden');
            contentBorrow.classList.add('hidden');
        }

        if (window.lucide && typeof window.lucide.createIcons === 'function') {
            window.lucide.createIcons();
        }
    }

    // Lọc bảng Lịch sử mượn - trả
    function filterBorrowTable() {
        const kw = (document.getElementById('filter-borrow-kw')?.value || '').toLowerCase().trim();
        const status = (document.getElementById('filter-borrow-status')?.value || '').toLowerCase().trim();
        const rows = document.querySelectorAll('#table-borrow-history .borrow-row');

        rows.forEach(function(row) {
            const title = row.getAttribute('data-title') || '';
            const isbn = row.getAttribute('data-isbn') || '';
            const code = row.getAttribute('data-code') || '';
            const rowStatus = (row.getAttribute('data-status') || '').toLowerCase();

            const matchKw = !kw || title.includes(kw) || isbn.includes(kw) || code.includes(kw);
            const matchStatus = !status || rowStatus === status;

            row.style.display = (matchKw && matchStatus) ? '' : 'none';
        });
    }

    function resetBorrowFilters() {
        const kwInput = document.getElementById('filter-borrow-kw');
        const statusSelect = document.getElementById('filter-borrow-status');
        if (kwInput) kwInput.value = '';
        if (statusSelect) statusSelect.value = '';
        filterBorrowTable();
    }

    // Lọc bảng Lịch sử giao dịch & gia hạn thẻ
    function filterCardTable() {
        const kw = (document.getElementById('filter-card-kw')?.value || '').toLowerCase().trim();
        const rows = document.querySelectorAll('#table-card-history .card-tx-row');

        rows.forEach(function(row) {
            const search = row.getAttribute('data-search') || '';
            row.style.display = (!kw || search.includes(kw)) ? '' : 'none';
        });
    }

    function openBookDetailModal(btn) {
        const d = btn.dataset;
        document.getElementById('modal_borrow_book_id').value = d.bookId;
        document.getElementById('modal_detail_title').textContent = d.bookTitle;
        document.getElementById('modal_detail_author').textContent = d.bookAuthor;
        document.getElementById('modal_detail_isbn').textContent = d.bookIsbn || 'Chưa có';
        document.getElementById('modal_detail_category').textContent = d.bookCategory || 'Chung';
        document.getElementById('modal_detail_publisher').textContent = d.bookPublisher || 'NXB Tri Thức';
        document.getElementById('modal_detail_year').textContent = d.bookYear ? `(${d.bookYear})` : '';
        document.getElementById('modal_detail_location').textContent = d.bookLocation;
        document.getElementById('modal_detail_cover').src = d.bookCover || 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?auto=format&fit=crop&q=80&w=400';
        document.getElementById('modal_detail_description').textContent = d.bookDescription || 'Đầu sách này hiện chưa có nội dung tóm tắt chi tiết.';
        document.getElementById('modal_detail_rating').textContent = d.bookRating || '5.0';
        document.getElementById('modal_detail_rating_count').textContent = `(${d.bookRatingCount || 0} đánh giá)`;

        const available = parseInt(d.bookAvailable || '0');
        const badge = document.getElementById('modal_detail_stock_badge');
        const borrowBtn = document.getElementById('modal_detail_borrow_btn');

        if (available > 0) {
            badge.className = 'px-2 py-0.5 rounded-full font-bold text-[10px] bg-emerald-100 text-emerald-800 border border-emerald-200';
            badge.textContent = `Còn sẵn ${available} / ${d.bookTotal} cuốn`;
            borrowBtn.disabled = false;
            borrowBtn.className = 'px-5 py-2 bg-sky-600 hover:bg-sky-700 text-white font-bold text-xs rounded-xl shadow transition flex items-center justify-center gap-1.5 min-w-[120px]';
        } else {
            badge.className = 'px-2 py-0.5 rounded-full font-bold text-[10px] bg-rose-100 text-rose-800 border border-rose-200';
            badge.textContent = 'Hết sách trên kệ';
            borrowBtn.disabled = true;
            borrowBtn.className = 'px-5 py-2 bg-slate-300 text-slate-500 font-bold text-xs rounded-xl shadow-none cursor-not-allowed flex items-center justify-center gap-1.5 min-w-[120px]';
        }

        openModal('modal-book-detail');
        if (window.lucide) {
            window.lucide.createIcons();
        }
    }

    function openVietQRModal(amount, desc, ticketId) {
        activeTicketId = ticketId;
        const rawBank = (document.getElementById('cfg_bank_name')?.value || 'MB Bank').toUpperCase();
        let bank = 'MB';
        if (rawBank.indexOf('MB') !== -1) bank = 'MB';
        else if (rawBank.indexOf('TECHCOM') !== -1 || rawBank.indexOf('TCB') !== -1) bank = 'TCB';
        else if (rawBank.indexOf('VIETCOM') !== -1 || rawBank.indexOf('VCB') !== -1) bank = 'VCB';
        else if (rawBank.indexOf('VIETIN') !== -1 || rawBank.indexOf('CTG') !== -1 || rawBank.indexOf('ICB') !== -1) bank = 'ICB';
        else if (rawBank.indexOf('BIDV') !== -1) bank = 'BIDV';
        else if (rawBank.indexOf('ACB') !== -1) bank = 'ACB';
        else if (rawBank.indexOf('TPB') !== -1 || rawBank.indexOf('TIEN PHONG') !== -1) bank = 'TPB';
        else if (rawBank.indexOf('VPB') !== -1 || rawBank.indexOf('VPBANK') !== -1) bank = 'VPB';
        else bank = rawBank.split(/[\s(]/)[0].replace(/[^a-zA-Z0-9]/g, '') || 'MB';

        const account = document.getElementById('cfg_bank_account')?.value || '0987654321';
        const holder = document.getElementById('cfg_account_holder')?.value || 'THU VIEN LIBRANOVA QUOC GIA';
        const qrUrl = 'https://img.vietqr.io/image/' + bank + '-' + account + '-compact2.png?amount=' + amount + '&addInfo=' + encodeURIComponent(desc) + '&accountName=' + encodeURIComponent(holder);

        document.getElementById('modal-qr-image').src = qrUrl;
        document.getElementById('modal-qr-amount').textContent = Number(amount).toLocaleString('vi-VN') + 'đ';
        document.getElementById('modal-qr-desc').textContent = desc;
        
        const modal = document.getElementById('vietqr-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeVietQRModal() {
        const modal = document.getElementById('vietqr-modal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }

    function confirmPaymentSuccess() {
        const csrfToken = document.getElementById('cfg_csrf_token')?.value || '';
        fetch('/payment/confirm', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ ticket_id: activeTicketId, payment_method: 'vietqr' })
        }).then(() => {
            closeVietQRModal();
            window.location.reload();
        });
    }

    function openCardRenewModal() {
        const modal = document.getElementById('card-renew-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeCardRenewModal() {
        const modal = document.getElementById('card-renew-modal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }

    function openRateModal(bookId, bookTitle) {
        document.getElementById('rate-book-id').value = bookId;
        document.getElementById('rate-book-title').textContent = bookTitle;
        const modal = document.getElementById('rate-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeRateModal() {
        const modal = document.getElementById('rate-modal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }

    const initialTagsRaw = document.getElementById('initial_selected_tags_storage')?.value || '';
    let readerSelectedTags = new Set(
        initialTagsRaw ? initialTagsRaw.split(',').map(s => s.trim()).filter(Boolean) : []
    );

    function openModal(id) {
        const m = document.getElementById(id);
        if (m) {
            m.classList.remove('hidden');
            m.classList.add('flex');
        }
    }

    function closeModal(id) {
        const m = document.getElementById(id);
        if (m) {
            m.classList.remove('flex');
            m.classList.add('hidden');
        }
    }

    function openReaderTagModal() {
        document.querySelectorAll('#modal-category-tags .tag-checkbox').forEach(cb => {
            cb.checked = readerSelectedTags.has(cb.value);
        });
        updateTagBadgeCount();
        openModal('modal-category-tags');
    }

    function onTagChecked(checkbox) {
        if (checkbox.checked) {
            readerSelectedTags.add(checkbox.value);
        } else {
            readerSelectedTags.delete(checkbox.value);
        }
        updateTagBadgeCount();
    }

    function updateTagBadgeCount() {
        const countBadge = document.getElementById('tags-count-badge');
        if (countBadge) {
            countBadge.textContent = readerSelectedTags.size;
        }
    }

    function clearAllSelectedTags() {
        readerSelectedTags.clear();
        document.querySelectorAll('#modal-category-tags .tag-checkbox').forEach(cb => cb.checked = false);
        updateTagBadgeCount();
    }

    function filterTags(keyword) {
        const term = (keyword || '').toLowerCase().trim();
        document.querySelectorAll('#modal-category-tags .tag-item').forEach(item => {
            const text = item.textContent.toLowerCase();
            if (!term || text.includes(term)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    }

    function confirmCategoryTagSelection() {
        const tagsArr = Array.from(readerSelectedTags);
        const tagsInput = document.getElementById('reader_selected_tags_input');
        if (tagsInput) {
            tagsInput.value = tagsArr.join(',');
        }
        closeModal('modal-category-tags');
        document.getElementById('reader-search-form').submit();
    }

    function removeSingleTag(tag) {
        readerSelectedTags.delete(tag);
        const tagsInput = document.getElementById('reader_selected_tags_input');
        if (tagsInput) {
            tagsInput.value = Array.from(readerSelectedTags).join(',');
        }
        document.getElementById('reader-search-form').submit();
    }
</script>

@include('partials.modal-category-tags')
@endsection