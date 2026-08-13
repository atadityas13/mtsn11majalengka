<?php

namespace App\Filament\Resources\MenuItems\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MenuItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('label')
                    ->label('Menu')
                    ->searchable()
                    ->description(fn ($record) => $record->parent?->label
                        ? 'Submenu dari: '.$record->parent->label
                        : 'Menu utama'),
                TextColumn::make('parent.label')
                    ->label('Induk')
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('url')
                    ->searchable()
                    ->limit(36),
                TextColumn::make('location')
                    ->badge()
                    ->searchable(),
                TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('open_in_new_tab')
                    ->label('Tab baru')
                    ->boolean(),
                IconColumn::make('is_visible')
                    ->label('Tampil')
                    ->boolean(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('location')
                    ->options([
                        'header' => 'Header',
                        'footer' => 'Footer',
                    ]),
                SelectFilter::make('parent_id')
                    ->label('Menu induk')
                    ->relationship('parent', 'label'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
