<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ApiKey extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'key',
        'permissions',
        'status',
        'approved_by',
        'approved_at',
        'last_used_at',
        'expires_at',
        'rate_limit',
        'notes'
    ];

    protected $casts = [
        'permissions' => 'array',
        'approved_at' => 'datetime',
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
        'rate_limit' => 'integer'
    ];

    protected $hidden = [
        'key'
    ];

    /**
     * Get the user that owns the API key
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin who approved the API key
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Generate a new API key
     */
    public static function generateKey(): string
    {
        return 'ef_' . Str::random(32);
    }

    /**
     * Check if the API key is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && 
               $this->approved_at !== null && 
               ($this->expires_at === null || $this->expires_at->isFuture());
    }

    /**
     * Check if the API key has expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    /**
     * Check if the API key has a specific permission
     */
    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions ?? []);
    }

    /**
     * Update last used timestamp
     */
    public function updateLastUsed(): void
    {
        $this->update(['last_used_at' => now()]);
    }

    /**
     * Get masked key for display
     */
    public function getMaskedKeyAttribute(): string
    {
        return substr($this->key, 0, 8) . '...' . substr($this->key, -8);
    }

    /**
     * Scope for active keys
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->whereNotNull('approved_at')
                    ->where(function ($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>', now());
                    });
    }

    /**
     * Scope for pending approval
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for expired keys
     */
    public function scopeExpired($query)
    {
        return $query->whereNotNull('expires_at')
                    ->where('expires_at', '<', now());
    }
}
