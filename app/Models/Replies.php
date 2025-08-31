<?php

namespace App\Models;

use App\Models\User;
use App\Models\Comments;
use Illuminate\Database\Eloquent\Model;
use Overtrue\LaravelLike\Traits\Likeable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Replies extends Model
{
    use HasFactory;
    use Likeable;

    protected $fillable = [
        'comment_id',
        'user_id',
        'body'
    ];

    public function comment(){
        return $this->belongsTo(Comments::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
