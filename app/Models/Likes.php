<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Overtrue\LaravelLike\Traits\Likeable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Likes extends Model
{
    use HasFactory;

    use Likeable;

    protected $fillable = ['user_id', 'likeable_type', 'likeable_id'];

    public function User()
    {
        return $this->belongsTo(User::class);
    }
}
