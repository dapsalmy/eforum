<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NigerianState extends Model
{
    use HasFactory;

    protected $table = 'nigerian_states';

    protected $fillable = [
        'name',
        'code',
        'capital',
        'region',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    /**
     * Get the LGAs for the state.
     */
    public function lgas(): HasMany
    {
        return $this->hasMany(NigerianLga::class, 'state_id');
    }

    /**
     * Get users from this state.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'state_id');
    }

    /**
     * Scope to filter by region.
     */
    public function scopeInRegion($query, $region)
    {
        return $query->where('region', $region);
    }

    /**
     * Get all regions.
     */
    public static function getRegions(): array
    {
        return [
            'North-West',
            'North-East',
            'North-Central',
            'South-West',
            'South-East',
            'South-South'
        ];
    }
}
