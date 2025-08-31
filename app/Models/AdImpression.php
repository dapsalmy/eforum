<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdImpression extends Model
{
    use HasFactory;

    protected $fillable = [
        'advertisement_id',
        'user_id',
        'ip_address',
        'user_agent',
        'referrer',
        'page_url',
        'position',
        'device_type',
        'location',
        'session_id',
        'impression_type', // view, click
        'revenue',
        'is_bot'
    ];

    protected $casts = [
        'revenue' => 'decimal:4',
        'is_bot' => 'boolean'
    ];

    /**
     * Get the advertisement
     */
    public function advertisement(): BelongsTo
    {
        return $this->belongsTo(Advertisement::class);
    }

    /**
     * Get the user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for views
     */
    public function scopeViews($query)
    {
        return $query->where('impression_type', 'view');
    }

    /**
     * Scope for clicks
     */
    public function scopeClicks($query)
    {
        return $query->where('impression_type', 'click');
    }

    /**
     * Scope for today
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope for this month
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
    }

    /**
     * Check if IP is from a bot
     */
    public static function isBot(string $userAgent): bool
    {
        $botPatterns = [
            'bot', 'crawler', 'spider', 'scraper', 'curl', 'wget',
            'python', 'java', 'perl', 'ruby', 'php', 'go',
            'semrush', 'ahrefs', 'moz', 'googlebot', 'bingbot',
            'facebook', 'twitter', 'linkedin', 'whatsapp'
        ];

        $userAgent = strtolower($userAgent);
        
        foreach ($botPatterns as $pattern) {
            if (strpos($userAgent, $pattern) !== false) {
                return true;
            }
        }

        return false;
    }
}
