<?php

namespace App\Models\Admin;

use App\Models\Posts;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    use HasFactory;

    protected $table = 'categories';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'status'
    ];

    public function posts(){
        return $this->hasMany(Posts::class, 'category_id');
    }

    public function top_users(){
        return $this->hasMany(Posts::class, 'category_id')->where('status', 1)->where('public', 1)->orderByDesc('created_at')->limit(4)->get();
    }

    public function latest_post(){
        return $this->hasMany(Posts::class, 'category_id',)->where('status', 1)->where('public', 1)->latest()->first();
    }
}
