<?php

namespace App\Models;

use App\Models\User;
use App\Models\Posts;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Favorites extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'favoritable_type', 'favoriteable_id'];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function post(){
        return $this->belongsTo(Posts::class);
    }
}
