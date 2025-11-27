<?php

namespace App\Filament\Widgets;

use App\Models\Article;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class MostViewedArticles extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $query = Article::query()
            ->where('is_published', true)
            ->orderBy('view_count', 'desc');

        // Authors can only see their own articles
        if (auth()->user()?->role === 'author') {
            $query->where('author_id', auth()->id());
        }

        return $table
            ->query($query->limit(10))
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(60)
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('category.name')
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('author.name')
                    ->badge()
                    ->color('info')
                    ->visible(fn () => auth()->user()?->role !== 'author'),

                Tables\Columns\TextColumn::make('view_count')
                    ->numeric()
                    ->sortable()
                    ->label('Views')
                    ->badge()
                    ->color('warning'),

                Tables\Columns\TextColumn::make('published_at')
                    ->dateTime()
                    ->since()
                    ->label('Published'),

                Tables\Columns\TextColumn::make('comments_count')
                    ->counts('comments')
                    ->label('Comments')
                    ->badge()
                    ->color('primary'),
            ])
            ->heading(auth()->user()?->role === 'author' ? 'My Most Viewed Articles' : 'Most Viewed Articles')
            ->actions([
                Tables\Actions\Action::make('view')
                    ->url(fn (Article $record): string => route('filament.admin.resources.articles.edit', $record))
                    ->icon('heroicon-o-eye'),
            ]);
    }
}
