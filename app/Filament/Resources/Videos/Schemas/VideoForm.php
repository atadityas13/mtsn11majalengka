<?php

namespace App\Filament\Resources\Videos\Schemas;

use App\Models\Video;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
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
                    ->default('short')
                    ->helperText('TikTok & Instagram Reels biasanya pilih Short'),
                TextInput::make('video_url')
                    ->label('URL video')
                    ->required()
                    ->url()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (?string $state, callable $set, callable $get): void {
                        if (blank($state)) {
                            return;
                        }

                        $probe = new Video(['video_url' => $state]);
                        if ($probe->suggestsShort() && $get('type') !== 'video') {
                            $set('type', 'short');
                        }
                    })
                    ->helperText('YouTube/Shorts & TikTok diputar di situs. Instagram Reels: ketuk tengah untuk putar, geser di area atas/bawah untuk pindah short.')
                    ->columnSpanFull(),
                FileUpload::make('cover_image')
                    ->label('Cover')
                    ->image()
                    ->directory('videos')
                    ->disk('public')
                    ->required(fn (Get $get): bool => self::urlRequiresCover($get('video_url')))
                    ->markAsRequired(fn (Get $get): bool => self::urlRequiresCover($get('video_url')))
                    ->helperText(fn (Get $get): string => self::urlRequiresCover($get('video_url'))
                        ? 'Wajib untuk TikTok & Instagram (tidak ada thumbnail otomatis).'
                        : 'Opsional untuk YouTube (jika kosong pakai thumbnail YouTube).')
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

    protected static function urlRequiresCover(?string $url): bool
    {
        if (blank($url)) {
            return false;
        }

        return (new Video(['video_url' => $url]))->requiresCoverImage();
    }
}
