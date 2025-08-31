<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type_id',
        'type',
        'amount',
        'currency',
        'gateway',
        'payment_method',
        'purpose',
        'status',
        'transaction_id',
        'reference',
        'metadata',
        'gateway_response'
    ];
}
