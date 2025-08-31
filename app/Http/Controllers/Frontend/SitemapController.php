<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Admin\Categories;
use App\Models\Admin\Pages;
use App\Models\Posts;
use App\Models\User;
use Conner\Tagging\Model\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class SitemapController extends Controller
{
    public function index()
    {
        return response()->view('frontend.sitemap.index')->header('Content-Type', 'text/xml');
    }

    public function categories()
    {
        $categories = Categories::where('status', 1)->orderBy('updated_at', 'desc')->get();

        return response()->view('frontend.sitemap.categories', [
            'categories' => $categories,
        ])->header('Content-Type', 'text/xml');
    }

    public function tags()
    {
        $items = Tag::orderBy('id', 'desc')->get();

        return response()->view('frontend.sitemap.tags', [
            'items' => $items,
        ])->header('Content-Type', 'text/xml');
    }

    public function pages()
    {
        $items = Pages::where('status', 1)->orderBy('updated_at', 'desc')->get();

        return response()->view('frontend.sitemap.pages', [
            'items' => $items,
        ])->header('Content-Type', 'text/xml');
    }

    public function posts()
    {
        $items = Posts::where('status', 1)->where('public', 1)->orderBy('updated_at', 'desc')->get();

        return response()->view('frontend.sitemap.posts', [
            'items' => $items,
        ])->header('Content-Type', 'text/xml');
    }

    public function users()
    {
        $users = User::orderBy('updated_at', 'desc')->get();

        return response()->view('frontend.sitemap.users', [
            'users' => $users,
        ])->header('Content-Type', 'text/xml');
    }

    public function robots()
    {
        return response()->view('frontend.sitemap.robots')->header('Content-Type', 'text');
    }
}
