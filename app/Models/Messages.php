<?php

namespace App\Models;

use App\Models\User;
use App\Models\Chats;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Messages extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_id',
        'sender_id',
        'message',
        'attachement_name',
        'file_ext',
        'mime_type',
        'seen'
    ];

    public function chats()
    {
        return $this->belongsTo(Chats::class);
    }

    public function user_sender()
    {
        return $this->belongsTo(User::class, 'sender_id', 'id');
    }
}
