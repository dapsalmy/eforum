<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModerationAction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'moderator_id',
        'action',
        'reason',
        'content_type',
        'content_id',
        'notes',
    ];

    /**
     * Get the user who was moderated
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the moderator who took the action
     */
    public function moderator()
    {
        return $this->belongsTo(User::class, 'moderator_id');
    }

    /**
     * Get action badge
     */
    public function getActionBadgeAttribute()
    {
        $badges = [
            'dismiss' => '<span class="badge bg-secondary">Dismissed</span>',
            'warn' => '<span class="badge bg-warning">Warning</span>',
            'remove' => '<span class="badge bg-danger">Content Removed</span>',
            'ban' => '<span class="badge bg-dark">Banned</span>',
            'unban' => '<span class="badge bg-success">Unbanned</span>',
        ];

        return $badges[$this->action] ?? '<span class="badge bg-secondary">' . ucfirst($this->action) . '</span>';
    }

    /**
     * Get reason badge
     */
    public function getReasonBadgeAttribute()
    {
        $badges = [
            'spam' => '<span class="badge bg-danger">Spam</span>',
            'harassment' => '<span class="badge bg-danger">Harassment</span>',
            'inappropriate' => '<span class="badge bg-warning">Inappropriate</span>',
            'misinformation' => '<span class="badge bg-warning">Misinformation</span>',
            'off_topic' => '<span class="badge bg-info">Off Topic</span>',
            'other' => '<span class="badge bg-secondary">Other</span>',
        ];

        return $badges[$this->reason] ?? '<span class="badge bg-secondary">' . ucfirst($this->reason) . '</span>';
    }
}
