<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'categories',
        'badges',
        'posts',
        'comments',
        'replies',
        'chats',
        'reports',
        'ban_durations',
        'banned_users',
        'plans',
        'buy_points',
        'deposits',
        'subscriptions',
        'tips',
        'withdrawals',
        'transactions',
        'users',
        'roles',
        'pages',
        'faqs',
        'site_settings',
        'gateways',
        'language',
        'mail'
    ];
}
