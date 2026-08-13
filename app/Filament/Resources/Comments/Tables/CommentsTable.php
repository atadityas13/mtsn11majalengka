<?php

namespace App\Filament\Resources\Comments\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
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
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('warning'),
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
                    ->label('Status')
                    ->trueLabel('Disetujui')
                    ->falseLabel('Menunggu')
                    ->placeholder('Semua'),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn ($record) => ! $record->is_approved)
                    ->action(function ($record): void {
                        $record->update(['is_approved' => true]);
                        Notification::make()
                            ->title('Komentar disetujui')
                            ->success()
                            ->send();
                    }),
                Action::make('unapprove')
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
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
