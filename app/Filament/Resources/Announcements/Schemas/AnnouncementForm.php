<?php

namespace App\Filament\Resources\Announcements\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AnnouncementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Judul')
                    ->required()
                    ->columnSpanFull(),
                RichEditor::make('body')
                    ->label('Isi pengumuman')
                    ->required()
                    ->columnSpanFull(),
                DatePicker::make('published_on')
                    ->label('Tanggal')
                    ->native(false)
                    ->default(now()),
                Toggle::make('is_pinned')
                    ->label('Sematkan di atas')
                    ->default(false),
                Toggle::make('is_published')
                    ->label('Tayangkan')
                    ->default(true),
            ])
            ->columns(2);
    }
}
