<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Posts\PostResource;
use App\Models\Post;
use Filament\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class RecentPostsWidget extends TableWidget
{
    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Aktivitas berita terbaru')
            ->description('5 berita terakhir diperbarui')
            ->query(
                Post::query()
                    ->latest('updated_at')
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('title')
                    ->label('Judul')
                    ->limit(48)
                    ->wrap(),
                TextColumn::make('author_name')
                    ->label('Kontributor')
                    ->placeholder('—'),
                TextColumn::make('editor_name')
                    ->label('Redaktur')
                    ->placeholder('—'),
                IconColumn::make('is_published')
                    ->label('Tayang')
                    ->boolean(),
                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->since(),
            ])
            ->recordActions([
                Action::make('edit')
                    ->label('Edit')
                    ->url(fn (Post $record): string => PostResource::getUrl('edit', ['record' => $record])),
            ])
            ->paginated(false)
            ->emptyStateHeading('Belum ada berita');
    }
}
