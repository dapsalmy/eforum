<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class StorageService
{
    /**
     * Get the configured storage disk
     */
    public static function disk()
    {
        $driver = get_setting('storage_driver', 'local');
        
        if ($driver === 'wasabi' && get_setting('enable_wasabi') === '1') {
            return Storage::disk('wasabi');
        }
        
        if ($driver === 's3' && get_setting('enable_s3') === '1') {
            return Storage::disk('s3');
        }
        
        return Storage::disk('local');
    }
    
    /**
     * Get URL for stored file
     */
    public static function url($path)
    {
        // If CDN is enabled, use CDN URL
        if (get_setting('enable_cdn') === '1' && get_setting('cdn_url')) {
            return rtrim(get_setting('cdn_url'), '/') . '/' . ltrim($path, '/');
        }
        
        // If using cloud storage
        $driver = get_setting('storage_driver', 'local');
        if (in_array($driver, ['wasabi', 's3'])) {
            return self::disk()->url($path);
        }
        
        // Default to local URL
        return URL::asset($path);
    }
    
    /**
     * Get URL for assets (CSS, JS, etc)
     */
    public static function assetUrl($path)
    {
        // If CDN assets URL is configured
        if (get_setting('enable_cdn') === '1' && get_setting('cdn_assets_url')) {
            return rtrim(get_setting('cdn_assets_url'), '/') . '/' . ltrim($path, '/');
        }
        
        // If general CDN URL is configured
        if (get_setting('enable_cdn') === '1' && get_setting('cdn_url')) {
            return rtrim(get_setting('cdn_url'), '/') . '/' . ltrim($path, '/');
        }
        
        // Default to local URL
        return URL::asset($path);
    }
    
    /**
     * Upload file to configured storage
     */
    public static function upload($file, $directory = '', $filename = null)
    {
        if (!$filename) {
            $filename = md5(microtime()) . '.' . $file->getClientOriginalExtension();
        }
        
        $path = $directory ? rtrim($directory, '/') . '/' . $filename : $filename;
        
        // Upload to configured disk
        self::disk()->put($path, file_get_contents($file), 'public');
        
        return $path;
    }
    
    /**
     * Delete file from storage
     */
    public static function delete($path)
    {
        return self::disk()->delete($path);
    }
    
    /**
     * Check if file exists
     */
    public static function exists($path)
    {
        return self::disk()->exists($path);
    }
    
    /**
     * Get file size
     */
    public static function size($path)
    {
        return self::disk()->size($path);
    }
    
    /**
     * Get file mime type
     */
    public static function mimeType($path)
    {
        return self::disk()->mimeType($path);
    }
}
