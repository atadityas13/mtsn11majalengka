<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Comments\CommentResource;
use App\Filament\Resources\ContactMessages\ContactMessageResource;
use App\Models\Comment;
use App\Models\ContactMessage;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Str;

class ModerationQueueWidget extends TableWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = [
        'md' => 1,
        'xl' => 1,
    ];

    public function table(Table $table): Table
    {
        return $table
            ->heading('Antrian moderasi')
            ->description('Komentar menunggu persetujuan')
            ->query(
                Comment::query()
                    ->where('is_approved', false)
                    ->with('post')
                    ->latest()
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->limit(20),
                TextColumn::make('body')
                    ->label('Komentar')
                    ->formatStateUsing(fn (string $state): string => Str::limit($state, 40)),
                TextColumn::make('post.title')
                    ->label('Berita')
                    ->limit(24)
                    ->placeholder('—'),
                TextColumn::make('created_at')
                    ->label('Masuk')
                    ->since(),
            ])
            ->recordActions([
                Action::make('review')
                    ->label('Tinjau')
                    ->url(fn (Comment $record): string => CommentResource::getUrl('edit', ['record' => $record])),
            ])
            ->paginated([5])
            ->defaultPaginationPageOption(5)
            ->emptyStateHeading('Tidak ada antrian')
            ->emptyStateDescription(
                ContactMessage::query()->whereNull('read_at')->exists()
                    ? 'Ada pesan kontak belum dibaca. Buka menu Pesan Kontak.'
                    : 'Semua komentar sudah ditinjau.'
            )
            ->headerActions([
                Action::make('pesan')
                    ->label('Pesan kontak')
                    ->url(ContactMessageResource::getUrl('index'))
                    ->visible(fn (): bool => ContactMessage::query()->whereNull('read_at')->exists()),
                Action::make('semua')
                    ->label('Semua komentar')
                    ->url(CommentResource::getUrl('index')),
            ]);
    }
}
