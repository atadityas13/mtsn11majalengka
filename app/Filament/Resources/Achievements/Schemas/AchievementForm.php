<?php

namespace App\Filament\Resources\Achievements\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AchievementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Judul prestasi')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('level')
                    ->label('Tingkat')
                    ->placeholder('Kecamatan / Kabupaten / Provinsi / Nasional'),
                TextInput::make('winner_name')
                    ->label('Nama peraih'),
                DatePicker::make('achieved_on')
                    ->label('Tanggal')
                    ->native(false),
                FileUpload::make('image')
                    ->label('Foto (bisa diganti nanti)')
                    ->image()
                    ->directory('achievements')
                    ->disk('public')
                    ->columnSpanFull(),
                Textarea::make('description')
                    ->label('Keterangan')
                    ->rows(3)
                    ->columnSpanFull(),
                TextInput::make('sort_order')
                    ->label('Urutan')
                    ->numeric()
                    ->default(0),
                Toggle::make('is_published')
                    ->label('Tayangkan')
                    ->default(true),
            ])
            ->columns(2);
    }
}
