<?php

namespace App\Models;

use App\Models\Posts;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PostsViews extends Model
{
    use HasFactory;

    protected $table = 'posts_views';

    protected $fillable = ['post_id', 'user_id'];

    public function Posts()
    {
        return $this->belongsTo(Posts::class);
    }

    //
    public static function createViewLog($post) {

        $item= new PostsViews;
        $item->post_id = $post->id;
        $item->user_id = (Auth::check())?Auth::user()->id:null;
        $item->ip = request()->ip();
        $item->agent = request()->header('User-Agent');
        $item->save();
    }
}
