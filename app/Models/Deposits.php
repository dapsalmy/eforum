<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deposits extends Model
{
    use HasFactory;

    protected $fillable = [
        'deposit_id',
        'user_id',
        'amount',
        'gateway',
        'image',
        'percentage_applied',
        'transaction_fee',
        'status'
    ];

}
