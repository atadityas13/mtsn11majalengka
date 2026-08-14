<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Comments\CommentResource;
use App\Filament\Resources\ContactMessages\ContactMessageResource;
use App\Models\Comment;
use App\Models\ContactMessage;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Str;

class ModerationQueueWidget extends TableWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = [
        'default' => 'full',
        'lg' => 1,
    ];

    public function table(Table $table): Table
    {
        return $table
            ->heading('Komentar terbaru')
            ->description('Langsung tayang di situs — kelola atau hapus jika perlu')
            ->query(
                Comment::query()
                    ->with('post')
                    ->latest()
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->wrap()
                    ->limit(28),
                TextColumn::make('body')
                    ->label('Komentar')
                    ->wrap()
                    ->formatStateUsing(fn (string $state): string => Str::limit($state, 64)),
                TextColumn::make('post.title')
                    ->label('Berita')
                    ->wrap()
                    ->limit(36)
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('is_approved')
                    ->label('Tampil')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Ya' : 'Sembunyi')
                    ->color(fn (bool $state): string => $state ? 'success' : 'gray'),
                TextColumn::make('created_at')
                    ->label('Masuk')
                    ->since()
                    ->toggleable(),
            ])
            ->recordActions([
                Action::make('kelola')
                    ->label('Kelola')
                    ->url(fn (Comment $record): string => CommentResource::getUrl('edit', ['record' => $record])),
                DeleteAction::make()->label('Hapus'),
            ])
            ->paginated([5])
            ->defaultPaginationPageOption(5)
            ->emptyStateHeading('Belum ada komentar')
            ->emptyStateDescription(
                ContactMessage::query()->whereNull('read_at')->exists()
                    ? 'Ada pesan kontak belum dibaca. Buka menu Pesan Kontak.'
                    : 'Komentar pengunjung akan muncul di sini.'
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
