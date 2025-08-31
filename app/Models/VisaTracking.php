<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VisaTracking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'visa_type',
        'country',
        'status',
        'application_date',
        'interview_date',
        'decision_date',
        'timeline',
        'documents_checklist',
        'notes',
        'is_public',
    ];

    protected $casts = [
        'application_date' => 'date',
        'interview_date' => 'date',
        'decision_date' => 'date',
        'timeline' => 'array',
        'documents_checklist' => 'array',
        'is_public' => 'boolean',
    ];

    /**
     * Status options
     */
    const STATUSES = [
        'planning' => ['name' => 'Planning', 'color' => 'gray'],
        'documents' => ['name' => 'Gathering Documents', 'color' => 'blue'],
        'submitted' => ['name' => 'Application Submitted', 'color' => 'indigo'],
        'biometrics' => ['name' => 'Biometrics Done', 'color' => 'purple'],
        'interview_scheduled' => ['name' => 'Interview Scheduled', 'color' => 'yellow'],
        'interview_done' => ['name' => 'Interview Completed', 'color' => 'orange'],
        'administrative_processing' => ['name' => 'Administrative Processing', 'color' => 'pink'],
        'approved' => ['name' => 'Approved', 'color' => 'green'],
        'rejected' => ['name' => 'Rejected', 'color' => 'red'],
        'passport_collected' => ['name' => 'Passport Collected', 'color' => 'teal'],
    ];

    /**
     * Common visa types
     */
    const VISA_TYPES = [
        // US Visas
        'us_b1_b2' => 'US Tourist/Business (B1/B2)',
        'us_f1' => 'US Student (F-1)',
        'us_h1b' => 'US Work (H-1B)',
        'us_green_card' => 'US Green Card',
        
        // UK Visas
        'uk_visit' => 'UK Visit',
        'uk_student' => 'UK Student',
        'uk_skilled_worker' => 'UK Skilled Worker',
        'uk_family' => 'UK Family',
        
        // Canada Visas
        'canada_visit' => 'Canada Visit',
        'canada_study' => 'Canada Study Permit',
        'canada_work' => 'Canada Work Permit',
        'canada_express_entry' => 'Canada Express Entry',
        
        // Schengen
        'schengen_visit' => 'Schengen Visit',
        'schengen_business' => 'Schengen Business',
        
        // Others
        'australia_visit' => 'Australia Visit',
        'australia_skilled' => 'Australia Skilled Migration',
        'other' => 'Other',
    ];

    /**
     * Get the user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get timeline updates.
     */
    public function timelineUpdates(): HasMany
    {
        return $this->hasMany(VisaTimelineUpdate::class)->orderBy('update_date', 'desc');
    }

    /**
     * Get status details
     */
    public function getStatusDetailsAttribute(): array
    {
        return self::STATUSES[$this->status] ?? ['name' => ucfirst($this->status), 'color' => 'gray'];
    }

    /**
     * Get visa type name
     */
    public function getVisaTypeNameAttribute(): string
    {
        return self::VISA_TYPES[$this->visa_type] ?? $this->visa_type;
    }

    /**
     * Add timeline update
     */
    public function addTimelineUpdate($title, $description, $status = null): VisaTimelineUpdate
    {
        return $this->timelineUpdates()->create([
            'title' => $title,
            'description' => $description,
            'status' => $status ?? $this->status,
            'update_date' => now(),
        ]);
    }

    /**
     * Calculate days in current status
     */
    public function getDaysInStatusAttribute(): int
    {
        $lastUpdate = $this->timelineUpdates()
            ->where('status', $this->status)
            ->orderBy('update_date', 'desc')
            ->first();

        if ($lastUpdate) {
            return $lastUpdate->update_date->diffInDays(now());
        }

        return $this->created_at->diffInDays(now());
    }

    /**
     * Calculate total processing time
     */
    public function getTotalProcessingDaysAttribute(): ?int
    {
        if (!$this->application_date) {
            return null;
        }

        $endDate = $this->decision_date ?? now();
        return $this->application_date->diffInDays($endDate);
    }

    /**
     * Get progress percentage
     */
    public function getProgressPercentageAttribute(): int
    {
        $statusOrder = [
            'planning' => 10,
            'documents' => 20,
            'submitted' => 30,
            'biometrics' => 40,
            'interview_scheduled' => 50,
            'interview_done' => 60,
            'administrative_processing' => 70,
            'approved' => 100,
            'rejected' => 100,
            'passport_collected' => 100,
        ];

        return $statusOrder[$this->status] ?? 0;
    }

    /**
     * Scope for public timelines
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope by country
     */
    public function scopeForCountry($query, $country)
    {
        return $query->where('country', $country);
    }

    /**
     * Scope by visa type
     */
    public function scopeForVisaType($query, $visaType)
    {
        return $query->where('visa_type', $visaType);
    }

    /**
     * Get similar timelines
     */
    public function getSimilarTimelines($limit = 5)
    {
        return self::where('id', '!=', $this->id)
            ->where('country', $this->country)
            ->where('visa_type', $this->visa_type)
            ->where('is_public', true)
            ->whereIn('status', ['approved', 'passport_collected'])
            ->limit($limit)
            ->get();
    }

    /**
     * Get status badge HTML
     */
    public function getStatusBadge()
    {
        $badges = [
            'planning' => '<span class="badge bg-secondary">Planning</span>',
            'documents' => '<span class="badge bg-info">Gathering Documents</span>',
            'submitted' => '<span class="badge bg-primary">Submitted</span>',
            'biometrics' => '<span class="badge bg-purple">Biometrics</span>',
            'interview_scheduled' => '<span class="badge bg-warning">Interview Scheduled</span>',
            'interview_done' => '<span class="badge bg-info">Interview Done</span>',
            'administrative_processing' => '<span class="badge bg-warning">Admin Processing</span>',
            'approved' => '<span class="badge bg-success">Approved</span>',
            'rejected' => '<span class="badge bg-danger">Rejected</span>',
            'passport_collected' => '<span class="badge bg-success">Completed</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge bg-secondary">' . ucfirst($this->status) . '</span>';
    }
}
