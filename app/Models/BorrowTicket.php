<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BorrowTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_code',
        'reader_id',
        'book_id',
        'borrow_date',
        'due_date',
        'return_date',
        'status', // 'borrowing', 'returned', 'overdue'
        'renew_count',
        'overdue_days',
        'fine_amount',
        'payment_status', // 'unpaid', 'paid', 'none'
        'payment_method',
        'created_by_staff'
    ];

    protected $casts = [
        'borrow_date' => 'date',
        'due_date' => 'date',
        'return_date' => 'date',
        'fine_amount' => 'decimal:2',
    ];

    public function reader()
    {
        return $this->belongsTo(User::class, 'reader_id');
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function calculateOverdue(int $finePerDay = 5000): void
    {
        if ($this->status === 'returned') return;

        $now = Carbon::now()->startOfDay();
        $due = Carbon::parse($this->due_date)->startOfDay();

        if ($now->greaterThan($due)) {
            $days = $now->diffInDays($due);
            $this->overdue_days = $days;
            $this->fine_amount = $days * $finePerDay;
            $this->status = 'overdue';
            if ($this->payment_status === 'none') {
                $this->payment_status = 'unpaid';
            }
        } else {
            $this->overdue_days = 0;
            $this->fine_amount = 0;
            $this->status = 'borrowing';
        }
    }
}
