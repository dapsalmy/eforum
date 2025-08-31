<?php

namespace App\Models;

use App\Models\User;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Plans extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'duration',
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
        'order',
        'status'
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'plan_id');
    }

    public function subscription()
    {
        return $this->hasMany(Subscription::class, 'plan_id');
    }
}
