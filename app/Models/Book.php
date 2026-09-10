<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'isbn',
        'title',
        'author',
        'category_id',
        'publisher_id',
        'publish_year',
        'total_qty',
        'available_qty',
        'shelf_location', // e.g. 'Kệ A1 - Tầng 1 - Ngăn Văn Học 01'
        'description',
        'cover_url',
        'rating',
        'rating_count'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function publisher()
    {
        return $this->belongsTo(Publisher::class);
    }

    public function borrowTickets()
    {
        return $this->hasMany(BorrowTicket::class);
    }
}
