<?php

namespace App\Services;

use App\Models\NavigationItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class NavigationService
{
    /**
     * Cache duration in seconds (1 hour)
     */
    protected int $cacheDuration = 3600;

    /**
     * Cache key for navigation
     */
    protected string $cacheKey = 'navigation.menu.tree';

    /**
     * Get the complete navigation menu tree
     *
     * @return Collection
     */
    public function getMenuTree(): Collection
    {
        return Cache::remember($this->cacheKey, $this->cacheDuration, function () {
            return NavigationItem::with(['category', 'page', 'children.category', 'children.page'])
                ->active()
                ->roots()
                ->ordered()
                ->get();
        });
    }

    /**
     * Get all navigation items (without tree structure)
     *
     * @return Collection
     */
    public function getAllItems(): Collection
    {
        return NavigationItem::with(['category', 'page', 'parent'])
            ->ordered()
            ->get();
    }

    /**
     * Clear navigation cache
     *
     * @return void
     */
    public function clearCache(): void
    {
        Cache::forget($this->cacheKey);
    }

    /**
     * Check if cache exists
     *
     * @return bool
     */
    public function hasCachedMenu(): bool
    {
        return Cache::has($this->cacheKey);
    }

    /**
     * Build hierarchical array for rendering (recursive)
     *
     * @param Collection|null $items
     * @param int|null $parentId
     * @return Collection
     */
    public function buildTree(?Collection $items = null, ?int $parentId = null): Collection
    {
        if ($items === null) {
            $items = $this->getAllItems();
        }

        return $items
            ->where('parent_id', $parentId)
            ->where('is_active', true)
            ->sortBy('order')
            ->map(function ($item) use ($items) {
                $item->children_items = $this->buildTree($items, $item->id);
                return $item;
            })
            ->values();
    }
}
