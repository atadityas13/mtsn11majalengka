<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Posts\PostResource;
use App\Models\Post;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class PopularPostsWidget extends TableWidget
{
    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = [
        'md' => 1,
        'xl' => 1,
    ];

    public function table(Table $table): Table
    {
        return $table
            ->heading('Berita populer')
            ->description('Berdasarkan jumlah tayangan')
            ->query(
                Post::query()
                    ->published()
                    ->orderByDesc('views_count')
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('title')
                    ->label('Judul')
                    ->limit(36)
                    ->wrap(),
                TextColumn::make('views_count')
                    ->label('Dilihat')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('published_at')
                    ->label('Terbit')
                    ->date('d M Y'),
            ])
            ->recordActions([
                Action::make('edit')
                    ->label('Edit')
                    ->url(fn (Post $record): string => PostResource::getUrl('edit', ['record' => $record])),
            ])
            ->paginated(false)
            ->emptyStateHeading('Belum ada data tayangan');
    }
}
