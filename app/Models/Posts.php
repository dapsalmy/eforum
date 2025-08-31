<?php

namespace App\Models;

use App\Models\User;
use App\Models\Comments;
use App\Models\Favorites;
use App\Models\Reactions;
use App\Models\PostsViews;
use App\Models\Admin\Categories;
use Illuminate\Database\Eloquent\Model;
use Overtrue\LaravelLike\Traits\Likeable;
use Qirolab\Laravel\Reactions\Traits\Reactable;
use Overtrue\LaravelFavorite\Traits\Favoriteable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Qirolab\Laravel\Reactions\Contracts\ReactableInterface;

class Posts extends Model implements ReactableInterface
{
    use HasFactory;
    use \Conner\Tagging\Taggable;
    use Likeable;
    use Favoriteable;
    use Reactable;

    protected $fillable = [
        'post_id',
        'user_id',
        'category_id',
        'title',
        'slug',
        'body',
        'pinned'
    ];

    public function category(){
        return $this->belongsTo(Categories::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function comments(){
        return $this->hasMany(Comments::class, 'post_id')->where('status', 1)->orderByDesc('created_at')->get();
    }

    public function search_comments(){
        return $this->hasMany(Comments::class, 'post_id');
    }

    public function search_views(){
        return $this->hasMany(PostsViews::class, 'post_id');
    }

    public function views(){
        return $this->hasMany(PostsViews::class, 'post_id')->get();
    }

    public function likes(){
        return $this->hasMany(Likes::class, 'likeable_id')->where('likeable_type', 'App\Models\Posts')->orderByDesc('created_at')->get();
    }

    public function search_likes(){
        return $this->hasMany(Likes::class, 'likeable_id');
    }

    public function user_likes(){
        return $this->hasMany(Likes::class, 'likeable_id')->where('likeable_type', 'App\Models\Posts')->orderByDesc('created_at')->limit(5)->get();
    }

    public function favorited(){
        return $this->belongsTo(Favorites::class, 'id', 'favoriteable_id');
    }

    public function shares(){
        return $this->hasMany(Shares::class, 'post_id')->get();
    }

    public function user_reactions(){
        return $this->hasMany(Reactions::class, 'reactable_id')->where('reactable_type', 'App\Models\Posts')->orderByDesc('created_at')->limit(5)->get();
    }

    public function total_reactions(){
        return $this->hasMany(Reactions::class, 'reactable_id')->where('reactable_type', 'App\Models\Posts')->orderByDesc('created_at')->get();
    }

    public function followings(){
        return $this->belongsTo(Follow::class);
    }
}
