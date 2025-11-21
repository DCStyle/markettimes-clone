<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'article_id',
        'user_id',
        'content',
        'is_approved',
        'parent_id',
        'guest_name',
        'guest_email',
        'likes_count',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
    ];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id')
            ->where('is_approved', true)
            ->latest();
    }

    public function likes(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'comment_likes')
            ->withTimestamps();
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopePending($query)
    {
        return $query->where('is_approved', false);
    }

    public function scopeParent($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeSortByLikes($query)
    {
        return $query->orderBy('likes_count', 'desc');
    }

    // Check if comment can be edited (within 15 minutes)
    public function canEdit($userId = null, $ipAddress = null): bool
    {
        if (!$this->canModify($userId, $ipAddress)) {
            return false;
        }

        return $this->created_at->diffInMinutes(now()) <= 15;
    }

    // Check if comment can be deleted (within 15 minutes)
    public function canDelete($userId = null, $ipAddress = null): bool
    {
        if (!$this->canModify($userId, $ipAddress)) {
            return false;
        }

        return $this->created_at->diffInMinutes(now()) <= 15;
    }

    // Check if user/guest owns this comment
    private function canModify($userId = null, $ipAddress = null): bool
    {
        if ($this->user_id && $userId) {
            return $this->user_id === $userId;
        }

        // For guest comments, check IP address (simplified)
        if (!$this->user_id && $ipAddress) {
            // In production, you'd store IP with comment
            return false; // Guests can't edit for now
        }

        return false;
    }

    // Check if this comment is liked by user/IP
    public function isLikedBy($userId = null, $ipAddress = null): bool
    {
        if ($userId) {
            return $this->likes()->where('user_id', $userId)->exists();
        }

        if ($ipAddress) {
            return \DB::table('comment_likes')
                ->where('comment_id', $this->id)
                ->where('ip_address', $ipAddress)
                ->whereNull('user_id')
                ->exists();
        }

        return false;
    }

    // Get author name (user or guest)
    public function getAuthorNameAttribute(): string
    {
        return $this->user?->name ?? $this->guest_name ?? 'Ẩn danh';
    }
}
