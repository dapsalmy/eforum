<?php

namespace App\Models;

use App\Models\User;
use App\Models\Posts;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Points extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'type', 
        'score',
        'context',
        'related_id',
        'reason'
    ];

    /**
     * Point types mapping
     */
    const TYPES = [
        1 => 'login',
        2 => 'register',
        3 => 'post',
        4 => 'comment',
        5 => 'reply',
        6 => 'like',
        7 => 'reaction',
        8 => 'share',
        9 => 'subscription',
        10 => 'tip',
        11 => 'visa_answer',
        12 => 'job_referral',
        13 => 'helpful_vote',
        14 => 'best_answer',
        15 => 'profile_verified',
        16 => 'expertise_endorsed',
    ];

    /**
     * Context types for Nigerian forum
     */
    const CONTEXTS = [
        'visa_help' => 'Visa & Immigration Help',
        'job_referral' => 'Job Referral',
        'relationship_advice' => 'Relationship Advice',
        'best_answer' => 'Best Answer',
        'helpful_content' => 'Helpful Content',
        'profile_verified' => 'Profile Verification',
        'expertise' => 'Expertise Recognition',
    ];

    /**
     * Get the user that owns the points.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get type name
     */
    public function getTypeNameAttribute(): string
    {
        return self::TYPES[$this->type] ?? 'unknown';
    }

    /**
     * Get context name
     */
    public function getContextNameAttribute(): string
    {
        return self::CONTEXTS[$this->context] ?? $this->context ?? '';
    }

    /**
     * Award points to user
     */
    public static function award($userId, $type, $score, $context = null, $relatedId = null, $reason = null): self
    {
        $points = self::create([
            'user_id' => $userId,
            'type' => $type,
            'score' => $score,
            'context' => $context,
            'related_id' => $relatedId,
            'reason' => $reason,
        ]);

        // Update user's total points
        $user = User::find($userId);
        if ($user) {
            $user->increment('reputation_score', $score);
            
            // Update category-specific reputation
            if ($context && in_array($context, ['visa_help', 'job_referral', 'relationship_advice'])) {
                $category = match($context) {
                    'visa_help' => 'visa_expert',
                    'job_referral' => 'job_helper',
                    'relationship_advice' => 'relationship_advisor',
                };
                
                UserReputation::firstOrCreate(
                    ['user_id' => $userId, 'category' => $category]
                )->addReputation($score, $context === 'best_answer');
            }
        }

        return $points;
    }
}
