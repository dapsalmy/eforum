<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NigerianLga extends Model
{
    use HasFactory;

    protected $table = 'nigerian_lgas';

    protected $fillable = [
        'state_id',
        'name',
        'code',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    /**
     * Get the state that owns the LGA.
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(NigerianState::class, 'state_id');
    }

    /**
     * Get users from this LGA.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'lga_id');
    }

    /**
     * Get full location name (LGA, State).
     */
    public function getFullLocationAttribute(): string
    {
        return $this->name . ', ' . $this->state->name;
    }
}
