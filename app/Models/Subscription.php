<?php

namespace App\Models;

use App\Models\Plans;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan_id',
        'will_expire',
        'price',
        'verified',
        'points',
        'categories',
        'tips',
        'comments',
        'reactions',
        'followers',
        'messages',
        'users',
        'viewers',
        'ads',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plans::class);
    }
}
