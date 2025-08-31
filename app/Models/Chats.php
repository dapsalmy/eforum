<?php

namespace App\Models;

use App\Models\User;
use App\Models\Messages;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Chats extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'status'
    ];

    public function user_sender()
    {
        return $this->belongsTo(User::class, 'sender_id', 'id');
    }

    public function user_receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id', 'id');
    }

    public function messages()
    {
        return $this->hasMany(Messages::class, 'chat_id');
    }

    public function latest_message()
    {
        return $this->hasMany(Messages::class, 'chat_id')->orderByDesc('created_at')->first();
    }

    public function latest_message_nav()
    {
        return $this->hasMany(Messages::class, 'chat_id')->where('sender_id', '!=', Auth::user()->id)->orderByDesc('created_at')->first();
    }

    public function unread_messages()
    {
        return $this->hasMany(Messages::class, 'chat_id')->where('sender_id', '!=', Auth::user()->id)->where("seen", 2)->count();
    }
}
