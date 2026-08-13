<?php

namespace App\Filament\Resources\ServiceLinks\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ServiceLinkForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('label')
                    ->label('Nama layanan')
                    ->required()
                    ->maxLength(255),
                TextInput::make('url')
                    ->label('URL / path')
                    ->helperText('Contoh: https://ppdb.mtsn11majalengka.sch.id/ atau /kontak')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->label('Deskripsi singkat')
                    ->rows(2)
                    ->maxLength(255)
                    ->columnSpanFull(),
                FileUpload::make('logo')
                    ->label('Logo aplikasi')
                    ->image()
                    ->directory('layanan')
                    ->disk('public')
                    ->imageEditor()
                    ->helperText('Disarankan PNG/SVG kotak. Jika kosong, dipakai inisial nama.')
                    ->columnSpanFull(),
                TextInput::make('sort_order')
                    ->label('Urutan')
                    ->numeric()
                    ->default(0)
                    ->required(),
                Toggle::make('open_in_new_tab')
                    ->label('Buka di tab baru')
                    ->default(false),
                Toggle::make('is_visible')
                    ->label('Tampilkan di situs')
                    ->helperText('Nonaktifkan untuk menyembunyikan tanpa menghapus')
                    ->default(true),
            ])
            ->columns(2);
    }
}
