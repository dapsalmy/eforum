<?php

namespace App\Models;

use App\Models\User;
use App\Models\Posts;
use Illuminate\Database\Eloquent\Model;
use Overtrue\LaravelLike\Traits\Likeable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comments extends Model
{
    use HasFactory;
    use Likeable;

    protected $fillable = [
        'post_id',
        'user_id',
        'body'
    ];

    public function post(){
        return $this->belongsTo(Posts::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replies()
    {
        return $this->hasMany(Replies::class, 'comment_id', 'id')->where('status', 1)->orderBy('created_at', 'desc');;
    }
}
