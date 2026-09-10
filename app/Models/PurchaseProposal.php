<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseProposal extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author',
        'category_name',
        'reason', // 'out_of_stock', 'damaged', 'reader_request'
        'suggested_qty',
        'estimated_price',
        'librarian_name',
        'status', // 'pending', 'approved', 'rejected', 'stocked'
        'admin_note'
    ];
}
