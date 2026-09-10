<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'operator_name',
        'operator_role',
        'action', // e.g. 'DELETE_BOOK', 'UPDATE_RULES', 'LOCK_USER'
        'target_id',
        'details',
        'ip_address'
    ];
}
