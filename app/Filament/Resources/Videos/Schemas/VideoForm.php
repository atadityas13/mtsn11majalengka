<?php

namespace App\Filament\Resources\Videos\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class VideoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Judul')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (?string $state, callable $set, callable $get): void {
                        if (blank($get('slug')) && filled($state)) {
                            $set('slug', Str::slug($state));
                        }
                    }),
                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true),
                Select::make('type')
                    ->label('Jenis')
                    ->options([
                        'short' => 'Short (vertikal / Shorts)',
                        'video' => 'Video biasa (horizontal)',
                    ])
                    ->required()
                    ->default('short'),
                TextInput::make('video_url')
                    ->label('URL YouTube / Shorts')
                    ->required()
                    ->url()
                    ->helperText('Contoh: https://youtube.com/shorts/xxxx atau https://youtu.be/xxxx')
                    ->columnSpanFull(),
                FileUpload::make('cover_image')
                    ->label('Cover (opsional)')
                    ->image()
                    ->directory('videos')
                    ->disk('public')
                    ->helperText('Jika kosong, pakai thumbnail YouTube')
                    ->columnSpanFull(),
                Textarea::make('description')
                    ->label('Deskripsi')
                    ->rows(3)
                    ->columnSpanFull(),
                TextInput::make('sort_order')
                    ->label('Urutan')
                    ->numeric()
                    ->default(0),
                DateTimePicker::make('published_at')
                    ->label('Tayang mulai')
                    ->native(false)
                    ->default(now()),
                Toggle::make('is_published')
                    ->label('Tayangkan')
                    ->default(true),
            ])
            ->columns(2);
    }
}
