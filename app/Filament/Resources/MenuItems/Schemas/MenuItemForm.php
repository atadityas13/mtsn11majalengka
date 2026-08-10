<?php

namespace App\Filament\Resources\MenuItems\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
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
                    ->helperText('Contoh: /berita atau https://ppdb.mtsn11majalengka.sch.id/')
                    ->required()
                    ->maxLength(255),
                Select::make('location')
                    ->label('Lokasi')
                    ->options([
                        'header' => 'Header',
                        'footer' => 'Footer',
                    ])
                    ->default('header')
                    ->required(),
                TextInput::make('sort_order')
                    ->label('Urutan')
                    ->numeric()
                    ->default(0)
                    ->required(),
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
