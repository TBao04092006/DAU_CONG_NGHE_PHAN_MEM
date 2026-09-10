<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('users');
        
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role')->default('reader'); // reader, librarian, admin
            $table->string('card_number')->nullable()->unique();
            $table->date('card_expiry_date')->nullable();
            $table->string('status')->default('active'); // active, locked, expired
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->integer('failed_login_count')->default(0);
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('publishers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->timestamps();
        });

        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('isbn')->unique();
            $table->string('title');
            $table->string('author');
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('publisher_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('publish_year')->default(2024);
            $table->integer('total_qty')->default(5);
            $table->integer('available_qty')->default(5);
            $table->string('shelf_location'); // e.g. Kệ A1 - Tầng 1 - Ngăn Văn Học 01
            $table->text('description')->nullable();
            $table->string('cover_url')->nullable();
            $table->decimal('rating', 3, 1)->default(5.0);
            $table->integer('rating_count')->default(1);
            $table->timestamps();
        });

        Schema::create('borrow_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_code')->unique();
            $table->foreignId('reader_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('book_id')->constrained('books')->cascadeOnDelete();
            $table->date('borrow_date');
            $table->date('due_date');
            $table->date('return_date')->nullable();
            $table->string('status')->default('borrowing'); // borrowing, returned, overdue
            $table->integer('renew_count')->default(0);
            $table->integer('overdue_days')->default(0);
            $table->decimal('fine_amount', 12, 2)->default(0);
            $table->string('payment_status')->default('none'); // none, unpaid, paid
            $table->string('payment_method')->nullable();
            $table->string('created_by_staff')->nullable();
            $table->timestamps();
        });

        Schema::create('purchase_proposals', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('author');
            $table->string('category_name')->nullable();
            $table->string('reason'); // out_of_stock, damaged, reader_request
            $table->integer('suggested_qty')->default(1);
            $table->decimal('estimated_price', 12, 2)->default(100000);
            $table->string('librarian_name');
            $table->string('status')->default('pending'); // pending, approved, rejected, stocked
            $table->text('admin_note')->nullable();
            $table->timestamps();
        });

        Schema::create('system_rules', function (Blueprint $table) {
            $table->id();
            $table->integer('max_books_per_loan')->default(5);
            $table->integer('max_loan_days')->default(14);
            $table->decimal('fine_per_day', 10, 2)->default(5000);
            $table->decimal('card_renewal_fee', 10, 2)->default(30000);
            $table->integer('max_renewal_times')->default(2);
            $table->integer('session_timeout_minutes')->default(15);
            $table->integer('max_failed_logins')->default(5);
            $table->string('bank_name')->default('MB Bank');
            $table->string('bank_account')->default('0987654321');
            $table->string('account_holder')->default('THU VIEN LIBRANOVA QUOC GIA');
            $table->timestamps();
        });

        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_code')->unique();
            $table->foreignId('ticket_id')->nullable();
            $table->foreignId('reader_id')->nullable();
            $table->string('reader_name')->nullable();
            $table->decimal('amount', 12, 2);
            $table->string('type')->default('fine'); // fine, card_renewal
            $table->string('payment_method')->default('vietqr'); // vietqr, momo, vnpay, cash
            $table->string('description')->nullable();
            $table->string('status')->default('completed');
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('operator_name');
            $table->string('operator_role');
            $table->string('action');
            $table->string('target_id')->nullable();
            $table->text('details')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('system_rules');
        Schema::dropIfExists('purchase_proposals');
        Schema::dropIfExists('borrow_tickets');
        Schema::dropIfExists('books');
        Schema::dropIfExists('publishers');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('users');
    }
};
