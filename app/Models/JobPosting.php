<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use App\Helpers\Currency;

class JobPosting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'slug',
        'description',
        'company_name',
        'company_website',
        'company_logo',
        'job_type',
        'employment_type',
        'location',
        'visa_sponsorship',
        'visa_types',
        'salary_min',
        'salary_max',
        'salary_currency',
        'salary_period',
        'required_skills',
        'preferred_skills',
        'requirements',
        'benefits',
        'how_to_apply',
        'application_url',
        'application_email',
        'deadline',
        'views',
        'applications',
        'is_featured',
        'is_verified',
        'status',
        'published_at',
        'expires_at',
    ];

    protected $casts = [
        'visa_sponsorship' => 'boolean',
        'visa_types' => 'array',
        'required_skills' => 'array',
        'preferred_skills' => 'array',
        'salary_min' => 'decimal:2',
        'salary_max' => 'decimal:2',
        'is_featured' => 'boolean',
        'is_verified' => 'boolean',
        'deadline' => 'date',
        'published_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Job types
     */
    const JOB_TYPES = [
        'remote' => 'Remote',
        'hybrid' => 'Hybrid',
        'onsite' => 'On-site',
    ];

    /**
     * Employment types
     */
    const EMPLOYMENT_TYPES = [
        'full-time' => 'Full-time',
        'part-time' => 'Part-time',
        'contract' => 'Contract',
        'internship' => 'Internship',
        'freelance' => 'Freelance',
    ];

    /**
     * Status options
     */
    const STATUS_DRAFT = 'draft';
    const STATUS_ACTIVE = 'active';
    const STATUS_EXPIRED = 'expired';
    const STATUS_FILLED = 'filled';

    /**
     * Common visa types for sponsorship
     */
    const VISA_TYPES = [
        'h1b' => 'H-1B (USA)',
        'green_card' => 'Green Card (USA)',
        'tier2' => 'Tier 2 (UK)',
        'skilled_worker' => 'Skilled Worker (UK)',
        'pr_canada' => 'PR (Canada)',
        'work_permit' => 'Work Permit (Canada)',
        'eu_blue_card' => 'EU Blue Card',
        'skilled_migration' => 'Skilled Migration (Australia)',
        'work_visa_general' => 'Work Visa (General)',
    ];

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($job) {
            if (empty($job->slug)) {
                $job->slug = Str::slug($job->title . '-' . Str::random(6));
            }
        });
    }

    /**
     * Get the user who posted the job.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Categories::class);
    }

    /**
     * Get applications for this job.
     */
    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    /**
     * Get users who saved this job.
     */
    public function savedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'job_saved')
            ->withTimestamps();
    }

    /**
     * Scope for active jobs
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    /**
     * Scope for jobs with visa sponsorship
     */
    public function scopeWithVisaSponsorship($query)
    {
        return $query->where('visa_sponsorship', true);
    }

    /**
     * Scope for remote jobs
     */
    public function scopeRemote($query)
    {
        return $query->where('job_type', 'remote');
    }

    /**
     * Scope for featured jobs
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Get formatted salary range
     */
    public function getSalaryRangeAttribute(): string
    {
        if (!$this->salary_min && !$this->salary_max) {
            return 'Negotiable';
        }

        $currency = $this->salary_currency ?? 'NGN';
        $period = $this->salary_period ?? 'monthly';

        if ($this->salary_min && $this->salary_max) {
            if ($currency === 'NGN') {
                return Currency::formatNaira($this->salary_min) . ' - ' . 
                       Currency::formatNaira($this->salary_max) . ' ' . $period;
            }
            return $currency . ' ' . number_format($this->salary_min) . ' - ' . 
                   number_format($this->salary_max) . ' ' . $period;
        }

        if ($this->salary_min) {
            return 'From ' . ($currency === 'NGN' ? 
                Currency::formatNaira($this->salary_min) : 
                $currency . ' ' . number_format($this->salary_min)) . ' ' . $period;
        }

        return 'Up to ' . ($currency === 'NGN' ? 
            Currency::formatNaira($this->salary_max) : 
            $currency . ' ' . number_format($this->salary_max)) . ' ' . $period;
    }

    /**
     * Check if job is expired
     */
    public function isExpired(): bool
    {
        if ($this->status === self::STATUS_EXPIRED || $this->status === self::STATUS_FILLED) {
            return true;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return true;
        }

        if ($this->deadline && $this->deadline->isPast()) {
            return true;
        }

        return false;
    }

    /**
     * Increment view count
     */
    public function incrementViews(): void
    {
        $this->increment('views');
    }

    /**
     * Get visa types as array
     */
    public function getVisaTypesListAttribute(): array
    {
        if (!$this->visa_types) {
            return [];
        }

        return collect($this->visa_types)
            ->map(fn($type) => self::VISA_TYPES[$type] ?? $type)
            ->toArray();
    }
}
