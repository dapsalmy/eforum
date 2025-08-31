<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserReputation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category',
        'score',
        'helpful_count',
        'verified_count',
    ];

    protected $casts = [
        'score' => 'integer',
        'helpful_count' => 'integer',
        'verified_count' => 'integer',
    ];

    /**
     * Categories of reputation
     */
    const CATEGORIES = [
        'visa_expert' => 'Visa & Immigration Expert',
        'job_helper' => 'Job & Career Helper',
        'relationship_advisor' => 'Relationship Advisor',
        'general' => 'General Contributor',
    ];

    /**
     * Get the user that owns the reputation.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get reputation level based on score
     */
    public function getLevel(): string
    {
        if ($this->score >= 1000) {
            return 'Expert';
        } elseif ($this->score >= 500) {
            return 'Advanced';
        } elseif ($this->score >= 200) {
            return 'Intermediate';
        } elseif ($this->score >= 50) {
            return 'Contributor';
        }
        return 'Beginner';
    }

    /**
     * Get level color for display
     */
    public function getLevelColor(): string
    {
        return match($this->getLevel()) {
            'Expert' => 'gold',
            'Advanced' => 'purple',
            'Intermediate' => 'blue',
            'Contributor' => 'green',
            default => 'gray',
        };
    }

    /**
     * Calculate reputation score
     */
    public static function calculateScore($helpfulCount, $verifiedCount): int
    {
        // Base score from helpful votes
        $baseScore = $helpfulCount * 5;
        
        // Bonus for verified helpful responses
        $verifiedBonus = $verifiedCount * 15;
        
        // Total score with multiplier for consistency
        $totalScore = $baseScore + $verifiedBonus;
        
        // Apply multiplier based on verified ratio
        if ($helpfulCount > 0) {
            $verifiedRatio = $verifiedCount / $helpfulCount;
            if ($verifiedRatio > 0.8) {
                $totalScore *= 1.5;
            } elseif ($verifiedRatio > 0.6) {
                $totalScore *= 1.25;
            }
        }
        
        return (int) $totalScore;
    }

    /**
     * Increment reputation
     */
    public function addReputation($points, $isVerified = false): void
    {
        $this->increment('score', $points);
        $this->increment('helpful_count');
        
        if ($isVerified) {
            $this->increment('verified_count');
        }
    }
}
