<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifications extends Model
{
    use HasFactory;

    protected $fillable = [
    'sender_id',
    'recipient_id',
    'notification_type',
    'post_id',
    'like_id',
    'comment_id',
    'reply_id',
    'react_id',
    'following_id',
    'tip_id',
    'seen'];
}
