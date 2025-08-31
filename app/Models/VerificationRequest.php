<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VerificationRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'verification_type',
        'status',
        'documents',
        'credentials',
        'admin_notes',
        'verified_at',
        'verified_by',
    ];

    protected $casts = [
        'documents' => 'array',
        'credentials' => 'array',
        'verified_at' => 'datetime',
    ];

    /**
     * Verification types
     */
    const TYPES = [
        'visa_consultant' => [
            'name' => 'Visa Consultant',
            'description' => 'Certified immigration consultant or lawyer',
            'requirements' => [
                'Professional certification',
                'Years of experience',
                'Client testimonials',
            ],
        ],
        'recruiter' => [
            'name' => 'Verified Recruiter',
            'description' => 'HR professional or recruitment agency',
            'requirements' => [
                'Company verification',
                'LinkedIn profile',
                'Business registration',
            ],
        ],
        'career_coach' => [
            'name' => 'Career Coach',
            'description' => 'Professional career advisor',
            'requirements' => [
                'Coaching certification',
                'Portfolio or testimonials',
                'Professional website',
            ],
        ],
        'relationship_counselor' => [
            'name' => 'Relationship Counselor',
            'description' => 'Licensed therapist or counselor',
            'requirements' => [
                'Professional license',
                'Years of practice',
                'Specialization proof',
            ],
        ],
        'diaspora_expert' => [
            'name' => 'Diaspora Expert',
            'description' => 'Experienced Nigerian living abroad',
            'requirements' => [
                'Proof of residence abroad',
                'Years of experience',
                'Community involvement',
            ],
        ],
    ];

    /**
     * Status options
     */
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_EXPIRED = 'expired';

    /**
     * Get the user that owns the request.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin who verified.
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Scope for pending requests
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for approved requests
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Approve the request
     */
    public function approve($adminId, $notes = null): void
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'verified_at' => now(),
            'verified_by' => $adminId,
            'admin_notes' => $notes,
        ]);

        // Update user verification
        $this->user->update([
            'verified' => true,
            'verification_type' => $this->verification_type,
            'verification_date' => now(),
        ]);

        // Award verification points
        Points::create([
            'user_id' => $this->user_id,
            'type' => 10, // New verification type
            'score' => 50,
            'context' => 'profile_verified',
            'reason' => 'Profile verification approved',
        ]);
    }

    /**
     * Reject the request
     */
    public function reject($adminId, $notes): void
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'verified_at' => now(),
            'verified_by' => $adminId,
            'admin_notes' => $notes,
        ]);
    }

    /**
     * Get type details
     */
    public function getTypeDetails(): array
    {
        return self::TYPES[$this->verification_type] ?? [];
    }
}
