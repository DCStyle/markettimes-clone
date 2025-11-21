<?php

namespace App\Filament\Widgets;

use App\Models\Article;
use App\Models\Comment;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Articles', Article::count())
                ->description('All articles in the system')
                ->descriptionIcon('heroicon-o-newspaper')
                ->color('success'),

            Stat::make('Published Articles', Article::where('is_published', true)->count())
                ->description('Currently published')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('primary'),

            Stat::make('Pending Comments', Comment::where('is_approved', false)->count())
                ->description('Awaiting moderation')
                ->descriptionIcon('heroicon-o-clock')
                ->color('warning')
                ->url(route('filament.admin.resources.comments.index')),

            Stat::make('Total Users', User::count())
                ->description('Registered users')
                ->descriptionIcon('heroicon-o-users')
                ->color('info'),
        ];
    }
}
