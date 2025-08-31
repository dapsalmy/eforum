<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Advertisement extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'image_url',
        'link_url',
        'ad_type', // banner, sidebar, popup, video
        'position', // top, footer, sidebar, in_content
        'status', // pending, active, paused, rejected, expired
        'start_date',
        'end_date',
        'impressions',
        'clicks',
        'budget',
        'spent',
        'target_audience', // json: demographics, interests, locations
        'schedule', // json: days, hours, frequency
        'approved_by',
        'approved_at',
        'rejection_reason',
        'category_id', // forum category targeting
        'tags', // json array for targeting
        'device_targeting', // desktop, mobile, tablet
        'location_targeting', // json: countries, states, cities
        'frequency_cap', // max impressions per user
        'priority', // 1-10 for ad rotation
        'is_featured',
        'tracking_pixel',
        'conversion_goal',
        'notes'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'approved_at' => 'datetime',
        'target_audience' => 'array',
        'schedule' => 'array',
        'tags' => 'array',
        'device_targeting' => 'array',
        'location_targeting' => 'array',
        'is_featured' => 'boolean',
        'budget' => 'decimal:2',
        'spent' => 'decimal:2',
        'impressions' => 'integer',
        'clicks' => 'integer',
        'frequency_cap' => 'integer',
        'priority' => 'integer'
    ];

    /**
     * Get the user that owns the advertisement
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin who approved the advertisement
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the category for targeting
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Check if advertisement is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && 
               $this->start_date <= now() && 
               $this->end_date >= now() &&
               $this->spent < $this->budget;
    }

    /**
     * Check if advertisement is within budget
     */
    public function isWithinBudget(): bool
    {
        return $this->spent < $this->budget;
    }

    /**
     * Increment impressions
     */
    public function incrementImpressions(): void
    {
        $this->increment('impressions');
    }

    /**
     * Increment clicks
     */
    public function incrementClicks(): void
    {
        $this->increment('clicks');
    }

    /**
     * Calculate CTR (Click Through Rate)
     */
    public function getCtrAttribute(): float
    {
        if ($this->impressions === 0) {
            return 0;
        }
        return round(($this->clicks / $this->impressions) * 100, 2);
    }

    /**
     * Calculate remaining budget
     */
    public function getRemainingBudgetAttribute(): float
    {
        return max(0, $this->budget - $this->spent);
    }

    /**
     * Check if user has reached frequency cap
     */
    public function hasReachedFrequencyCap(int $userId): bool
    {
        if (!$this->frequency_cap) {
            return false;
        }

        $impressions = AdImpression::where('advertisement_id', $this->id)
            ->where('user_id', $userId)
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        return $impressions >= $this->frequency_cap;
    }

    /**
     * Check if advertisement targets specific user
     */
    public function targetsUser(User $user): bool
    {
        // Check device targeting
        if (!empty($this->device_targeting)) {
            $userDevice = $this->getUserDevice();
            if (!in_array($userDevice, $this->device_targeting)) {
                return false;
            }
        }

        // Check location targeting
        if (!empty($this->location_targeting)) {
            $userLocation = $user->state ?? 'Unknown';
            if (!in_array($userLocation, $this->location_targeting)) {
                return false;
            }
        }

        // Check category targeting
        if ($this->category_id) {
            // Logic to check if user is viewing relevant category
            // This would be implemented in the ad serving logic
        }

        return true;
    }

    /**
     * Get user device type
     */
    private function getUserDevice(): string
    {
        $userAgent = request()->userAgent();
        
        if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i', $userAgent)) {
            return 'tablet';
        }
        
        if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i', $userAgent)) {
            return 'mobile';
        }
        
        return 'desktop';
    }

    /**
     * Scope for active advertisements
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now())
                    ->whereRaw('spent < budget');
    }

    /**
     * Scope for pending approval
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for featured advertisements
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for position
     */
    public function scopeForPosition($query, string $position)
    {
        return $query->where('position', $position);
    }
}
