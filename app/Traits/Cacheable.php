<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait Cacheable
{
    /**
     * Cache key prefix
     */
    protected function getCachePrefix(): string
    {
        return 'eforum_' . class_basename($this) . '_';
    }

    /**
     * Get cache key for a specific identifier
     */
    protected function getCacheKey(string $identifier): string
    {
        return $this->getCachePrefix() . $identifier;
    }

    /**
     * Get or set cache
     */
    protected function remember(string $key, $ttl, \Closure $callback)
    {
        $cacheKey = $this->getCacheKey($key);
        
        if (config('cache.default') === 'array') {
            // Don't cache in testing/local environment
            return $callback();
        }
        
        return Cache::remember($cacheKey, $ttl, $callback);
    }

    /**
     * Forget cache
     */
    protected function forget(string $key): void
    {
        Cache::forget($this->getCacheKey($key));
    }

    /**
     * Flush all cache for this model
     */
    protected function flushCache(): void
    {
        // This is a simple implementation - in production you might want to use tags
        Cache::flush();
    }

    /**
     * Clear model cache when saving
     */
    protected static function bootCacheable(): void
    {
        static::saved(function ($model) {
            $model->clearModelCache();
        });

        static::deleted(function ($model) {
            $model->clearModelCache();
        });
    }

    /**
     * Clear model-specific cache
     */
    protected function clearModelCache(): void
    {
        // Override in model to implement specific cache clearing
        Cache::forget($this->getCacheKey($this->id));
    }
}
