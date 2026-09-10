<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // 'reader', 'librarian', 'admin'
        'card_number',
        'card_expiry_date',
        'status', // 'active', 'locked', 'expired'
        'phone',
        'address',
        'failed_login_count'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'card_expiry_date' => 'date',
    ];

    public function borrowTickets()
    {
        return $this->hasMany(BorrowTicket::class, 'reader_id');
    }

    public function isReader(): bool
    {
        return $this->role === 'reader';
    }

    public function isLibrarian(): bool
    {
        return $this->role === 'librarian';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
