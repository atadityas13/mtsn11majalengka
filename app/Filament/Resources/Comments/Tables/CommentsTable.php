<?php

namespace App\Filament\Resources\Comments\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class CommentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                IconColumn::make('is_approved')
                    ->label('Tampil')
                    ->boolean()
                    ->trueIcon('heroicon-o-eye')
                    ->falseIcon('heroicon-o-eye-slash')
                    ->trueColor('success')
                    ->falseColor('gray'),
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('body')
                    ->label('Komentar')
                    ->limit(50)
                    ->wrap()
                    ->searchable(),
                TextColumn::make('post.title')
                    ->label('Berita')
                    ->limit(36)
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Masuk')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_approved')
                    ->label('Status tampil')
                    ->trueLabel('Ditampilkan')
                    ->falseLabel('Disembunyikan')
                    ->placeholder('Semua'),
            ])
            ->recordActions([
                Action::make('show')
                    ->label('Tampilkan')
                    ->icon('heroicon-o-eye')
                    ->color('success')
                    ->visible(fn ($record) => ! $record->is_approved)
                    ->action(function ($record): void {
                        $record->update(['is_approved' => true]);
                        Notification::make()
                            ->title('Komentar ditampilkan kembali')
                            ->success()
                            ->send();
                    }),
                Action::make('hide')
                    ->label('Sembunyikan')
                    ->icon('heroicon-o-eye-slash')
                    ->color('gray')
                    ->visible(fn ($record) => $record->is_approved)
                    ->action(function ($record): void {
                        $record->update(['is_approved' => false]);
                        Notification::make()
                            ->title('Komentar disembunyikan')
                            ->success()
                            ->send();
                    }),
                EditAction::make()->label('Detail'),
                DeleteAction::make()->label('Hapus'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
