<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentFlag extends Model
{
    use HasFactory;

    protected $fillable = [
        'flaggable_type',
        'flaggable_id',
        'user_id',
        'reason',
        'description',
        'status',
        'moderator_id',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    /**
     * Get the flaggable model
     */
    public function flaggable()
    {
        return $this->morphTo();
    }

    /**
     * Get the user who flagged the content
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the moderator who resolved the flag
     */
    public function moderator()
    {
        return $this->belongsTo(User::class, 'moderator_id');
    }

    /**
     * Get status badge
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => '<span class="badge bg-warning">Pending</span>',
            'resolved' => '<span class="badge bg-success">Resolved</span>',
            'dismissed' => '<span class="badge bg-secondary">Dismissed</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge bg-secondary">' . ucfirst($this->status) . '</span>';
    }
}
