<?php

namespace App\Filament\Resources\Videos\Schemas;

use App\Models\Video;
use App\Services\VideoMetadataFetcher;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
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
                    })
                    ->helperText('Bisa terisi otomatis saat URL video ditempel.'),
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

                        self::autofillFromUrl($state, $set, $get);
                    })
                    ->helperText('Tempel URL lalu keluar dari kolom: judul/cover diisi otomatis (YouTube & TikTok). Deskripsi + tanggal asli YouTube butuh YOUTUBE_DATA_API_KEY di .env.')
                    ->columnSpanFull(),
                FileUpload::make('cover_image')
                    ->label('Cover')
                    ->image()
                    ->directory('videos')
                    ->disk('public')
                    ->required(fn (Get $get): bool => self::urlRequiresCover($get('video_url')))
                    ->markAsRequired(fn (Get $get): bool => self::urlRequiresCover($get('video_url')))
                    ->helperText(fn (Get $get): string => self::urlRequiresCover($get('video_url'))
                        ? 'Untuk TikTok biasanya terisi otomatis dari URL. Instagram sering masih perlu upload manual.'
                        : 'Opsional untuk YouTube (jika kosong pakai thumbnail YouTube).')
                    ->columnSpanFull(),
                Textarea::make('description')
                    ->label('Deskripsi')
                    ->rows(3)
                    ->helperText('Otomatis dari YouTube jika YOUTUBE_DATA_API_KEY diisi. TikTok/Instagram biasanya tidak menyediakan deskripsi lewat oEmbed.')
                    ->columnSpanFull(),
                TextInput::make('sort_order')
                    ->label('Urutan')
                    ->numeric()
                    ->default(0),
                DateTimePicker::make('published_at')
                    ->label('Tayang mulai')
                    ->native(false)
                    ->default(now())
                    ->helperText('Dari tanggal publish video jika API YouTube aktif; jika tidak, tetap bisa diisi manual.'),
                Toggle::make('is_published')
                    ->label('Tayangkan')
                    ->default(true),
            ])
            ->columns(2);
    }

    protected static function autofillFromUrl(string $url, callable $set, callable $get): void
    {
        $meta = app(VideoMetadataFetcher::class)->fetch($url);

        if (! $meta) {
            Notification::make()
                ->title('Metadata tidak ditemukan')
                ->body('Judul/cover tidak bisa diambil otomatis. Isi manual atau cek URL-nya.')
                ->warning()
                ->send();

            return;
        }

        $filled = [];

        if (filled($meta['title'] ?? null) && blank($get('title'))) {
            $set('title', $meta['title']);
            $filled[] = 'judul';

            if (blank($get('slug'))) {
                $set('slug', Str::slug((string) $meta['title']));
            }
        }

        if (filled($meta['description'] ?? null) && blank($get('description'))) {
            $set('description', $meta['description']);
            $filled[] = 'deskripsi';
        }

        if (filled($meta['published_at'] ?? null)) {
            // Hanya ganti jika masih default/kosong atau baru dibuat
            if (blank($get('published_at'))) {
                $set('published_at', $meta['published_at']);
                $filled[] = 'tanggal tayang';
            }
        }

        if (filled($meta['cover_path'] ?? null) && blank($get('cover_image'))) {
            $set('cover_image', $meta['cover_path']);
            $filled[] = 'cover';
        }

        if ($filled === []) {
            if (($meta['platform'] ?? null) === 'youtube' && filled($meta['thumbnail_url'] ?? null)) {
                Notification::make()
                    ->title('YouTube terdeteksi')
                    ->body('Cover memakai thumbnail YouTube otomatis. Isi judul jika belum terisi, atau tambahkan YOUTUBE_DATA_API_KEY untuk deskripsi & tanggal asli.')
                    ->info()
                    ->send();
            }

            return;
        }

        Notification::make()
            ->title('Metadata diisi otomatis')
            ->body('Terisi: '.implode(', ', $filled).'.')
            ->success()
            ->send();
    }

    protected static function urlRequiresCover(?string $url): bool
    {
        if (blank($url)) {
            return false;
        }

        return (new Video(['video_url' => $url]))->requiresCoverImage();
    }
}
