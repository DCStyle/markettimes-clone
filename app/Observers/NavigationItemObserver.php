<?php

namespace App\Observers;

use App\Models\NavigationItem;
use App\Services\NavigationService;

class NavigationItemObserver
{
    protected NavigationService $navigationService;

    public function __construct(NavigationService $navigationService)
    {
        $this->navigationService = $navigationService;
    }

    /**
     * Handle the NavigationItem "created" event.
     */
    public function created(NavigationItem $navigationItem): void
    {
        $this->navigationService->clearCache();
    }

    /**
     * Handle the NavigationItem "updated" event.
     */
    public function updated(NavigationItem $navigationItem): void
    {
        $this->navigationService->clearCache();
    }

    /**
     * Handle the NavigationItem "deleted" event.
     */
    public function deleted(NavigationItem $navigationItem): void
    {
        $this->navigationService->clearCache();
    }

    /**
     * Handle the NavigationItem "restored" event.
     */
    public function restored(NavigationItem $navigationItem): void
    {
        $this->navigationService->clearCache();
    }

    /**
     * Handle the NavigationItem "force deleted" event.
     */
    public function forceDeleted(NavigationItem $navigationItem): void
    {
        $this->navigationService->clearCache();
    }
}
