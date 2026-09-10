<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'max_books_per_loan',
        'max_loan_days',
        'fine_per_day',
        'card_renewal_fee',
        'max_renewal_times',
        'session_timeout_minutes',
        'max_failed_logins',
        'bank_name',
        'bank_account',
        'account_holder'
    ];
}
