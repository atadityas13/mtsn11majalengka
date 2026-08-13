<?php

namespace App\Filament\Resources\MenuItems\Schemas;

use App\Models\MenuItem;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class MenuItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('label')
                    ->label('Label menu')
                    ->required()
                    ->maxLength(255),
                TextInput::make('url')
                    ->label('URL / path')
                    ->helperText('Untuk menu biasa: /berita. Untuk menu induk ber-submenu: URL diabaikan (hanya buka dropdown saat hover), boleh diisi #')
                    ->required()
                    ->maxLength(255),
                Select::make('location')
                    ->label('Lokasi')
                    ->options([
                        'header' => 'Header',
                        'footer' => 'Footer',
                    ])
                    ->default('header')
                    ->required()
                    ->live(),
                Select::make('parent_id')
                    ->label('Menu induk')
                    ->helperText('Pilih menu induk jika ini submenu. Kosongkan untuk menu utama.')
                    ->options(function (Get $get, ?MenuItem $record): array {
                        return MenuItem::query()
                            ->where('location', $get('location') ?: 'header')
                            ->whereNull('parent_id')
                            ->when(
                                $record,
                                fn ($query) => $query->whereKeyNot($record->getKey())
                            )
                            ->orderBy('sort_order')
                            ->orderBy('label')
                            ->pluck('label', 'id')
                            ->all();
                    })
                    ->searchable()
                    ->preload()
                    ->nullable(),
                TextInput::make('sort_order')
                    ->label('Urutan')
                    ->numeric()
                    ->default(0)
                    ->required()
                    ->helperText('Urutan di antara menu setingkat (utama atau sesama submenu)'),
                Toggle::make('open_in_new_tab')
                    ->label('Buka tab baru')
                    ->default(false),
                Toggle::make('is_visible')
                    ->label('Tampilkan')
                    ->default(true),
            ])
            ->columns(2);
    }
}
