<?php

namespace App\Http\Controllers\Api;

use App\Models\Posts;
use App\Models\Comments;
use App\Models\Replies;
use App\Models\Categories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PostController extends ApiController
{
    /**
     * Get posts with filtering and pagination
     */
    public function index(Request $request)
    {
        $query = Posts::with(['user', 'category', 'comments'])
            ->where('status', 1)
            ->where('public', 1);

        // Apply filters
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('content', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('tag')) {
            $query->where('tags', 'like', '%' . $request->tag . '%');
        }

        // Apply sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $posts = $query->paginate($request->per_page ?? 20);

        return $this->success([
            'posts' => $posts->items(),
            'pagination' => $this->transformPagination($posts)
        ], 'Posts retrieved successfully');
    }

    /**
     * Get post details
     */
    public function show($id, Request $request)
    {
        $post = Posts::with(['user', 'category', 'comments' => function ($query) {
            $query->with(['user', 'replies' => function ($subQuery) {
                $subQuery->with('user');
            }])->orderBy('created_at', 'asc');
        }])->find($id);

        if (!$post) {
            return $this->notFound('Post not found');
        }

        // Increment view count if user is not the author
        if (Auth::check() && Auth::id() !== $post->user_id) {
            $post->increment('views');
        }

        return $this->success([
            'post' => [
                'id' => $post->id,
                'title' => $post->title,
                'slug' => $post->slug,
                'content' => $post->content,
                'tags' => $post->tags,
                'views' => $post->views,
                'status' => $post->status,
                'pinned' => $post->pinned,
                'created_at' => $post->created_at,
                'updated_at' => $post->updated_at,
                'user' => [
                    'id' => $post->user->id,
                    'name' => $post->user->name,
                    'username' => $post->user->username,
                    'avatar' => $post->user->avatar
                ],
                'category' => $post->category ? [
                    'id' => $post->category->id,
                    'name' => $post->category->name,
                    'slug' => $post->category->slug
                ] : null,
                'comments_count' => $post->comments->count(),
                'comments' => $post->comments->map(function ($comment) {
                    return [
                        'id' => $comment->id,
                        'content' => $comment->content,
                        'created_at' => $comment->created_at,
                        'user' => [
                            'id' => $comment->user->id,
                            'name' => $comment->user->name,
                            'username' => $comment->user->username,
                            'avatar' => $comment->user->avatar
                        ],
                        'replies_count' => $comment->replies->count(),
                        'replies' => $comment->replies->map(function ($reply) {
                            return [
                                'id' => $reply->id,
                                'content' => $reply->content,
                                'created_at' => $reply->created_at,
                                'user' => [
                                    'id' => $reply->user->id,
                                    'name' => $reply->user->name,
                                    'username' => $reply->user->username,
                                    'avatar' => $reply->user->avatar
                                ]
                            ];
                        })
                    ];
                })
            ]
        ], 'Post retrieved successfully');
    }

    /**
     * Create new post
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'tags' => 'nullable|string|max:500',
            'public' => 'boolean'
        ]);

        $post = Posts::create([
            'title' => $request->title,
            'content' => $request->content,
            'slug' => $this->createSlug($request->title),
            'category_id' => $request->category_id,
            'tags' => $request->tags,
            'user_id' => Auth::id(),
            'public' => $request->public ?? true,
            'status' => 1
        ]);

        return $this->success([
            'post' => [
                'id' => $post->id,
                'title' => $post->title,
                'slug' => $post->slug,
                'created_at' => $post->created_at
            ]
        ], 'Post created successfully', 201);
    }

    /**
     * Update post
     */
    public function update(Request $request, Posts $post)
    {
        // Check if user owns the post
        if ($post->user_id !== Auth::id()) {
            return $this->error('Unauthorized', [], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'tags' => 'nullable|string|max:500'
        ]);

        $post->update([
            'title' => $request->title,
            'content' => $request->content,
            'category_id' => $request->category_id,
            'tags' => $request->tags
        ]);

        return $this->success([
            'post' => [
                'id' => $post->id,
                'title' => $post->title,
                'updated_at' => $post->updated_at
            ]
        ], 'Post updated successfully');
    }

    /**
     * Delete post
     */
    public function destroy(Posts $post)
    {
        // Check if user owns the post
        if ($post->user_id !== Auth::id()) {
            return $this->error('Unauthorized', [], 403);
        }

        $post->delete();
        return $this->success(null, 'Post deleted successfully');
    }

    /**
     * Add comment to post
     */
    public function addComment(Request $request, $postId)
    {
        $request->validate([
            'content' => 'required|string|max:1000'
        ]);

        $post = Posts::find($postId);
        if (!$post) {
            return $this->notFound('Post not found');
        }

        $comment = Comments::create([
            'post_id' => $postId,
            'user_id' => Auth::id(),
            'content' => $request->content,
            'status' => 1
        ]);

        return $this->success([
            'comment' => [
                'id' => $comment->id,
                'content' => $comment->content,
                'created_at' => $comment->created_at,
                'user' => [
                    'id' => Auth::user()->id,
                    'name' => Auth::user()->name,
                    'username' => Auth::user()->username
                ]
            ]
        ], 'Comment added successfully', 201);
    }

    /**
     * Add reply to comment
     */
    public function addReply(Request $request, $commentId)
    {
        $request->validate([
            'content' => 'required|string|max:1000'
        ]);

        $comment = Comments::find($commentId);
        if (!$comment) {
            return $this->notFound('Comment not found');
        }

        $reply = Replies::create([
            'comment_id' => $commentId,
            'user_id' => Auth::id(),
            'content' => $request->content,
            'status' => 1
        ]);

        return $this->success([
            'reply' => [
                'id' => $reply->id,
                'content' => $reply->content,
                'created_at' => $reply->created_at,
                'user' => [
                    'id' => Auth::user()->id,
                    'name' => Auth::user()->name,
                    'username' => Auth::user()->username
                ]
            ]
        ], 'Reply added successfully', 201);
    }

    /**
     * Like/Unlike post
     */
    public function toggleLike($postId)
    {
        $post = Posts::find($postId);
        if (!$post) {
            return $this->notFound('Post not found');
        }

        $user = Auth::user();
        $like = $user->likes()->where('post_id', $postId)->first();

        if ($like) {
            $like->delete();
            $message = 'Post unliked successfully';
        } else {
            $user->likes()->create(['post_id' => $postId]);
            $message = 'Post liked successfully';
        }

        return $this->success([
            'liked' => !$like,
            'likes_count' => $post->likes()->count()
        ], $message);
    }

    /**
     * Get categories
     */
    public function categories()
    {
        $categories = Categories::where('status', 1)
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'description']);

        return $this->success([
            'categories' => $categories
        ], 'Categories retrieved successfully');
    }

    /**
     * Get user's posts
     */
    public function userPosts(Request $request, $userId)
    {
        $posts = Posts::with(['category'])
            ->where('user_id', $userId)
            ->where('status', 1)
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 20);

        return $this->success([
            'posts' => $posts->items(),
            'pagination' => $this->transformPagination($posts)
        ], 'User posts retrieved successfully');
    }

    /**
     * Get feed (posts from followed users)
     */
    public function feed(Request $request)
    {
        $user = Auth::user();
        $followingIds = $user->following()->pluck('users.id');

        $posts = Posts::with(['user', 'category'])
            ->whereIn('user_id', $followingIds)
            ->where('status', 1)
            ->where('public', 1)
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 20);

        return $this->success([
            'posts' => $posts->items(),
            'pagination' => $this->transformPagination($posts)
        ], 'Feed retrieved successfully');
    }

    /**
     * Get trending posts
     */
    public function trending(Request $request)
    {
        $posts = Posts::with(['user', 'category'])
            ->where('status', 1)
            ->where('public', 1)
            ->withCount('comments')
            ->orderBy('comments_count', 'desc')
            ->orderBy('views', 'desc')
            ->limit($request->limit ?? 10)
            ->get();

        return $this->success([
            'posts' => $posts
        ], 'Trending posts retrieved successfully');
    }

    /**
     * Search posts
     */
    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2|max:100'
        ]);

        $posts = Posts::with(['user', 'category'])
            ->where('status', 1)
            ->where('public', 1)
            ->where(function ($query) use ($request) {
                $query->where('title', 'like', '%' . $request->q . '%')
                      ->orWhere('content', 'like', '%' . $request->q . '%')
                      ->orWhere('tags', 'like', '%' . $request->q . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 20);

        return $this->success([
            'posts' => $posts->items(),
            'pagination' => $this->transformPagination($posts),
            'query' => $request->q
        ], 'Search results retrieved successfully');
    }

    /**
     * Create slug from title
     */
    private function createSlug($title)
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        $count = Posts::where('slug', 'like', $slug . '%')->count();

        return $count ? $slug . '-' . ($count + 1) : $slug;
    }
}
