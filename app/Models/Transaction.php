<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_code',
        'ticket_id',
        'reader_id',
        'reader_name',
        'amount',
        'type', // 'fine', 'card_renewal'
        'payment_method', // 'vietqr', 'momo', 'vnpay', 'cash'
        'description',
        'status' // 'completed', 'pending'
    ];
    // BỔ SUNG 2 HÀM QUAN HỆ DƯỚI ĐÂY:
    public function ticket()
    {
        return $this->belongsTo(BorrowTicket::class, 'ticket_id');
    }

    public function reader()
    {
        return $this->belongsTo(User::class, 'reader_id');
    }
}
