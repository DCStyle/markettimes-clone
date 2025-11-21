<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NavigationItem extends Model
{
    protected $fillable = [
        'label',
        'type',
        'category_id',
        'page_id',
        'custom_url',
        'parent_id',
        'order',
        'is_active',
        'open_in_new_tab',
        'css_classes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'open_in_new_tab' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Relationship: Category (for category type links)
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relationship: Page (for page type links)
     */
    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    /**
     * Relationship: Parent navigation item
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(NavigationItem::class, 'parent_id');
    }

    /**
     * Relationship: Child navigation items
     */
    public function children(): HasMany
    {
        return $this->hasMany(NavigationItem::class, 'parent_id')
            ->where('is_active', true)
            ->orderBy('order');
    }

    /**
     * Scope: Get only active navigation items
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Get navigation items ordered by order field
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Scope: Get only root (top-level) navigation items
     */
    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope: Get only parent navigation items
     */
    public function scopeParent($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Get the URL for this navigation item based on its type
     */
    public function getUrlAttribute(): ?string
    {
        return match ($this->type) {
            'category' => $this->category ? route('category.show', $this->category->slug) : null,
            'page' => $this->page ? route('page.show', $this->page->slug) : null,
            'custom' => $this->custom_url,
            'divider' => null,
            default => null,
        };
    }

    /**
     * Check if this is an external URL
     */
    public function isExternal(): bool
    {
        if ($this->type !== 'custom' || !$this->custom_url) {
            return false;
        }

        return str_starts_with($this->custom_url, 'http://') ||
               str_starts_with($this->custom_url, 'https://') ||
               str_starts_with($this->custom_url, '//');
    }

    /**
     * Check if this navigation item has children
     */
    public function hasChildren(): bool
    {
        return $this->children()->exists();
    }

    /**
     * Get display label with type indicator (for admin)
     */
    public function getDisplayLabelAttribute(): string
    {
        $typeIcon = match ($this->type) {
            'category' => '📁',
            'page' => '📄',
            'custom' => '🔗',
            'divider' => '➖',
            default => '•',
        };

        return "{$typeIcon} {$this->label}";
    }
}
