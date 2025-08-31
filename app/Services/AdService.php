<?php

namespace App\Services;

use App\Models\Advertisement;
use App\Models\AdImpression;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AdService
{
    /**
     * Get advertisement for a specific position
     */
    public function getAd(string $position, ?User $user = null): ?Advertisement
    {
        $cacheKey = "ad_{$position}_" . ($user?->id ?? 'guest');
        
        return Cache::remember($cacheKey, 300, function () use ($position, $user) {
            $query = Advertisement::active()
                ->forPosition($position)
                ->where('spent', '<', DB::raw('budget'))
                ->orderBy('priority', 'desc')
                ->orderBy('is_featured', 'desc')
                ->orderBy(DB::raw('RAND()'));

            // Apply targeting filters
            if ($user) {
                $query->where(function ($q) use ($user) {
                    $q->whereNull('target_audience')
                      ->orWhereJsonContains('target_audience->demographics', $user->gender)
                      ->orWhereJsonContains('target_audience->interests', $user->interests);
                });

                // Check frequency cap
                $query->whereNotExists(function ($subQuery) use ($user) {
                    $subQuery->select(DB::raw(1))
                        ->from('ad_impressions')
                        ->whereColumn('ad_impressions.advertisement_id', 'advertisements.id')
                        ->where('ad_impressions.user_id', $user->id)
                        ->where('ad_impressions.created_at', '>=', now()->subDays(30))
                        ->havingRaw('COUNT(*) >= advertisements.frequency_cap');
                });
            }

            return $query->first();
        });
    }

    /**
     * Record ad impression
     */
    public function recordImpression(Advertisement $ad, ?User $user = null): void
    {
        $userAgent = request()->userAgent();
        
        // Skip bot impressions
        if (AdImpression::isBot($userAgent)) {
            return;
        }

        $impression = AdImpression::create([
            'advertisement_id' => $ad->id,
            'user_id' => $user?->id,
            'ip_address' => request()->ip(),
            'user_agent' => $userAgent,
            'referrer' => request()->header('referer'),
            'page_url' => request()->fullUrl(),
            'position' => $ad->position,
            'device_type' => $this->getDeviceType($userAgent),
            'location' => $user?->state ?? 'Unknown',
            'session_id' => session()->getId(),
            'impression_type' => 'view',
            'revenue' => $this->calculateRevenue($ad, 'view'),
            'is_bot' => false
        ]);

        // Increment ad impressions
        $ad->incrementImpressions();

        // Update spent amount
        $ad->increment('spent', $impression->revenue);

        // Clear cache
        Cache::forget("ad_{$ad->position}_" . ($user?->id ?? 'guest'));
    }

    /**
     * Record ad click
     */
    public function recordClick(Advertisement $ad, ?User $user = null): void
    {
        $userAgent = request()->userAgent();
        
        // Skip bot clicks
        if (AdImpression::isBot($userAgent)) {
            return;
        }

        $click = AdImpression::create([
            'advertisement_id' => $ad->id,
            'user_id' => $user?->id,
            'ip_address' => request()->ip(),
            'user_agent' => $userAgent,
            'referrer' => request()->header('referer'),
            'page_url' => request()->fullUrl(),
            'position' => $ad->position,
            'device_type' => $this->getDeviceType($userAgent),
            'location' => $user?->state ?? 'Unknown',
            'session_id' => session()->getId(),
            'impression_type' => 'click',
            'revenue' => $this->calculateRevenue($ad, 'click'),
            'is_bot' => false
        ]);

        // Increment ad clicks
        $ad->incrementClicks();

        // Update spent amount
        $ad->increment('spent', $click->revenue);
    }

    /**
     * Get device type from user agent
     */
    private function getDeviceType(string $userAgent): string
    {
        if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i', $userAgent)) {
            return 'tablet';
        }
        
        if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i', $userAgent)) {
            return 'mobile';
        }
        
        return 'desktop';
    }

    /**
     * Calculate revenue for impression/click
     */
    private function calculateRevenue(Advertisement $ad, string $type): float
    {
        // This would integrate with your pricing model
        // For now, using simple CPM/CPC model
        
        if ($type === 'view') {
            return 0.001; // $1 CPM = $0.001 per view
        } else {
            return 0.50; // $0.50 per click
        }
    }

    /**
     * Get ad statistics
     */
    public function getStats(?User $user = null): array
    {
        $query = Advertisement::query();
        
        if ($user) {
            $query->where('user_id', $user->id);
        }

        $totalAds = $query->count();
        $activeAds = $query->where('status', 'active')->count();
        $pendingAds = $query->where('status', 'pending')->count();

        $totalImpressions = AdImpression::whereHas('advertisement', function ($q) use ($user) {
            if ($user) {
                $q->where('user_id', $user->id);
            }
        })->views()->count();

        $totalClicks = AdImpression::whereHas('advertisement', function ($q) use ($user) {
            if ($user) {
                $q->where('user_id', $user->id);
            }
        })->clicks()->count();

        $totalRevenue = AdImpression::whereHas('advertisement', function ($q) use ($user) {
            if ($user) {
                $q->where('user_id', $user->id);
            }
        })->sum('revenue');

        return [
            'total_ads' => $totalAds,
            'active_ads' => $activeAds,
            'pending_ads' => $pendingAds,
            'total_impressions' => $totalImpressions,
            'total_clicks' => $totalClicks,
            'total_revenue' => $totalRevenue,
            'ctr' => $totalImpressions > 0 ? round(($totalClicks / $totalImpressions) * 100, 2) : 0
        ];
    }

    /**
     * Get trending ads for admin dashboard
     */
    public function getTrendingAds(int $limit = 10): array
    {
        return Advertisement::with(['user', 'category'])
            ->select('advertisements.*')
            ->selectRaw('(impressions * 0.3 + clicks * 0.7) as engagement_score')
            ->orderBy('engagement_score', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Clean up expired ads
     */
    public function cleanupExpiredAds(): int
    {
        return Advertisement::where('end_date', '<', now())
            ->where('status', 'active')
            ->update(['status' => 'expired']);
    }

    /**
     * Pause ads that exceeded budget
     */
    public function pauseOverBudgetAds(): int
    {
        return Advertisement::where('spent', '>=', DB::raw('budget'))
            ->where('status', 'active')
            ->update(['status' => 'paused']);
    }
}
