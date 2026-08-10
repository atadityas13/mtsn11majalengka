<?php

namespace App\Filament\Resources\StaffMembers\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class StaffMemberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama')
                    ->required(),
                TextInput::make('role')
                    ->label('Jabatan')
                    ->required()
                    ->placeholder('Guru Mapel / Waka / Tendik'),
                FileUpload::make('photo')
                    ->label('Foto (bisa diganti nanti)')
                    ->image()
                    ->directory('staff')
                    ->disk('public')
                    ->columnSpanFull(),
                TextInput::make('email')->email(),
                TextInput::make('phone')->label('Telepon'),
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
