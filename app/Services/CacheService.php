<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CacheService
{
    /**
     * Cache TTL constants (in seconds)
     */
    const TTL_MINUTE = 60;
    const TTL_HOUR = 3600;
    const TTL_DAY = 86400;
    const TTL_WEEK = 604800;
    const TTL_MONTH = 2592000;

    /**
     * Cache key prefixes
     */
    const PREFIX_USER = 'user_';
    const PREFIX_POST = 'post_';
    const PREFIX_CATEGORY = 'category_';
    const PREFIX_SETTINGS = 'settings_';
    const PREFIX_STATS = 'stats_';
    const PREFIX_JOB = 'job_';
    const PREFIX_VISA = 'visa_';

    /**
     * Get or set cached settings
     */
    public static function settings(string $key, $default = null)
    {
        return Cache::remember(self::PREFIX_SETTINGS . $key, self::TTL_DAY, function () use ($key, $default) {
            $setting = \App\Models\Admin\Settings::where('type', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Clear settings cache
     */
    public static function clearSettings(): void
    {
        Cache::forget(self::PREFIX_SETTINGS . '*');
    }

    /**
     * Cache user data
     */
    public static function user(int $userId, \Closure $callback = null)
    {
        $key = self::PREFIX_USER . $userId;
        
        if ($callback === null) {
            return Cache::get($key);
        }
        
        return Cache::remember($key, self::TTL_HOUR, $callback);
    }

    /**
     * Cache homepage stats
     */
    public static function homepageStats()
    {
        return Cache::remember(self::PREFIX_STATS . 'homepage', self::TTL_HOUR, function () {
            return [
                'total_users' => \App\Models\User::count(),
                'total_posts' => \App\Models\Post::where('status', 1)->count(),
                'total_comments' => \App\Models\Comment::count(),
                'total_jobs' => \App\Models\JobPosting::where('status', 'active')->count(),
                'total_visa_trackings' => \App\Models\VisaTracking::where('is_public', true)->count(),
                'recent_users' => \App\Models\User::latest()->limit(5)->get(),
            ];
        });
    }

    /**
     * Cache category list
     */
    public static function categories()
    {
        return Cache::remember(self::PREFIX_CATEGORY . 'all', self::TTL_DAY, function () {
            return \App\Models\Category::where('status', 1)
                ->withCount('posts')
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Cache popular posts
     */
    public static function popularPosts(int $limit = 10)
    {
        return Cache::remember(self::PREFIX_POST . 'popular_' . $limit, self::TTL_HOUR, function () use ($limit) {
            return \App\Models\Post::where('status', 1)
                ->orderBy('views', 'desc')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Cache trending posts
     */
    public static function trendingPosts(int $limit = 10)
    {
        return Cache::remember(self::PREFIX_POST . 'trending_' . $limit, self::TTL_HOUR, function () use ($limit) {
            return \App\Models\Post::where('status', 1)
                ->where('created_at', '>=', now()->subDays(7))
                ->withCount('comments')
                ->orderBy('comments_count', 'desc')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Cache job statistics
     */
    public static function jobStats()
    {
        return Cache::remember(self::PREFIX_JOB . 'stats', self::TTL_HOUR, function () {
            return [
                'total_active' => \App\Models\JobPosting::where('status', 'active')->count(),
                'with_sponsorship' => \App\Models\JobPosting::where('visa_sponsorship', true)->count(),
                'remote_jobs' => \App\Models\JobPosting::where('is_remote', true)->count(),
                'by_category' => \App\Models\JobPosting::select('category_id')
                    ->selectRaw('COUNT(*) as count')
                    ->where('status', 'active')
                    ->groupBy('category_id')
                    ->get(),
            ];
        });
    }

    /**
     * Cache visa statistics
     */
    public static function visaStats()
    {
        return Cache::remember(self::PREFIX_VISA . 'stats', self::TTL_HOUR, function () {
            $total = \App\Models\VisaTracking::whereIn('status', ['approved', 'rejected'])->count();
            $approved = \App\Models\VisaTracking::where('status', 'approved')->count();
            
            return [
                'success_rate' => $total > 0 ? round(($approved / $total) * 100, 1) : 0,
                'total_trackings' => \App\Models\VisaTracking::count(),
                'by_country' => \App\Models\VisaTracking::select('country')
                    ->selectRaw('COUNT(*) as count')
                    ->groupBy('country')
                    ->orderBy('count', 'desc')
                    ->limit(10)
                    ->get(),
            ];
        });
    }

    /**
     * Cache leaderboard
     */
    public static function leaderboard(string $period = 'all', int $limit = 10)
    {
        $key = self::PREFIX_STATS . 'leaderboard_' . $period . '_' . $limit;
        $ttl = $period === 'all' ? self::TTL_DAY : self::TTL_HOUR;
        
        return Cache::remember($key, $ttl, function () use ($period, $limit) {
            $query = \App\Models\User::query();
            
            if ($period === 'month') {
                $query->whereHas('reputations', function ($q) {
                    $q->where('created_at', '>=', now()->startOfMonth());
                });
            } elseif ($period === 'week') {
                $query->whereHas('reputations', function ($q) {
                    $q->where('created_at', '>=', now()->startOfWeek());
                });
            }
            
            return $query->orderBy('reputation_score', 'desc')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Clear all cache (use sparingly)
     */
    public static function clearAll(): void
    {
        try {
            Cache::flush();
            Log::info('All cache cleared');
        } catch (\Exception $e) {
            Log::error('Failed to clear cache: ' . $e->getMessage());
        }
    }

    /**
     * Clear specific cache pattern
     */
    public static function clearPattern(string $pattern): void
    {
        // This is a simple implementation - Redis supports pattern deletion
        if (config('cache.default') === 'redis') {
            $redis = Cache::getRedis();
            $keys = $redis->keys(config('cache.prefix') . $pattern . '*');
            foreach ($keys as $key) {
                $redis->del($key);
            }
        }
    }

    /**
     * Warm up cache
     */
    public static function warmUp(): void
    {
        try {
            // Cache frequently accessed data
            self::categories();
            self::homepageStats();
            self::popularPosts();
            self::trendingPosts();
            self::jobStats();
            self::visaStats();
            self::leaderboard();
            
            Log::info('Cache warmed up successfully');
        } catch (\Exception $e) {
            Log::error('Cache warm up failed: ' . $e->getMessage());
        }
    }
}
