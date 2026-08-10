<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Video extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'type',
        'video_url',
        'cover_image',
        'description',
        'sort_order',
        'is_published',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Video $video): void {
            if (blank($video->slug) && filled($video->title)) {
                $video->slug = Str::slug($video->title);
            }

            if (blank($video->type) && filled($video->video_url)) {
                $video->type = $video->suggestsShort() ? 'short' : 'video';
            }
        });
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('is_published', true)
            ->where(function (Builder $builder): void {
                $builder->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }

    public function scopeShorts(Builder $query): Builder
    {
        return $query->where('type', 'short');
    }

    public function scopeLongVideos(Builder $query): Builder
    {
        return $query->where('type', 'video');
    }

    /**
     * @return 'youtube'|'tiktok'|'instagram'|null
     */
    public function platform(): ?string
    {
        $url = strtolower((string) $this->video_url);

        if ($url === '') {
            return null;
        }

        if (str_contains($url, 'tiktok.com') || str_contains($url, 'vm.tiktok.com')) {
            return 'tiktok';
        }

        if (str_contains($url, 'instagram.com') || str_contains($url, 'instagr.am')) {
            return 'instagram';
        }

        if (
            str_contains($url, 'youtube.com')
            || str_contains($url, 'youtu.be')
            || str_contains($url, 'youtube-nocookie.com')
        ) {
            return 'youtube';
        }

        return null;
    }

    public function suggestsShort(): bool
    {
        $url = strtolower((string) $this->video_url);

        return match ($this->platform()) {
            'tiktok', 'instagram' => true,
            'youtube' => str_contains($url, '/shorts/'),
            default => false,
        };
    }

    public function youtubeId(): ?string
    {
        if ($this->platform() !== 'youtube') {
            return null;
        }

        $url = (string) $this->video_url;

        if (preg_match('/(?:youtu\.be\/|v=|shorts\/|embed\/)([A-Za-z0-9_-]{6,})/', $url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    public function tiktokId(): ?string
    {
        if ($this->platform() !== 'tiktok') {
            return null;
        }

        $url = (string) $this->video_url;

        if (preg_match('/\/video\/(\d+)/', $url, $matches)) {
            return $matches[1];
        }

        if (preg_match('/tiktok\.com\/(?:embed\/v2|player\/v1)\/(\d+)/', $url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    public function instagramCode(): ?string
    {
        if ($this->platform() !== 'instagram') {
            return null;
        }

        $url = (string) $this->video_url;

        if (preg_match('/instagram\.com\/(?:reels?|p|tv)\/([A-Za-z0-9_-]+)/', $url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    public function embedUrl(bool $autoplay = false, bool $mute = true, bool $shortsUi = false): ?string
    {
        return match ($this->platform()) {
            'youtube' => $this->youtubeEmbedUrl($autoplay, $mute, $shortsUi),
            'tiktok' => $this->tiktokEmbedUrl(),
            'instagram' => $this->instagramEmbedUrl(),
            default => null,
        };
    }

    public function thumbnailUrl(): ?string
    {
        if ($this->cover_image) {
            return asset('storage/'.$this->cover_image);
        }

        $id = $this->youtubeId();

        return $id ? 'https://i.ytimg.com/vi/'.$id.'/hqdefault.jpg' : null;
    }

    public function platformLabel(): string
    {
        return match ($this->platform()) {
            'youtube' => 'YouTube',
            'tiktok' => 'TikTok',
            'instagram' => 'Instagram',
            default => 'Video',
        };
    }

    protected function youtubeEmbedUrl(bool $autoplay, bool $mute, bool $shortsUi = false): ?string
    {
        $id = $this->youtubeId();

        if (! $id) {
            return null;
        }

        $params = [
            'rel' => 0,
            'modestbranding' => 1,
            'playsinline' => 1,
            'controls' => $shortsUi ? 0 : 1,
        ];

        if ($shortsUi) {
            $params['fs'] = 0;
            $params['iv_load_policy'] = 3;
            $params['disablekb'] = 1;
            $params['loop'] = 1;
            $params['playlist'] = $id;
            $params['enablejsapi'] = 1;
            $params['origin'] = rtrim((string) config('app.url'), '/');
        }

        if ($autoplay) {
            $params['autoplay'] = 1;
        }

        if ($mute) {
            $params['mute'] = 1;
        }

        return 'https://www.youtube.com/embed/'.$id.'?'.http_build_query($params);
    }

    protected function tiktokEmbedUrl(): ?string
    {
        $id = $this->tiktokId();

        if (! $id) {
            return null;
        }

        return 'https://www.tiktok.com/player/v1/'.$id.'?'.http_build_query([
            'autoplay' => 1,
            'loop' => 1,
            'music_info' => 0,
            'description' => 0,
            'controls' => 1,
            'progress_bar' => 1,
            'play_button' => 1,
            'volume_control' => 1,
            'fullscreen_button' => 0,
            'timestamp' => 0,
            'rel' => 0,
        ]);
    }

    protected function instagramEmbedUrl(): ?string
    {
        $code = $this->instagramCode();

        if (! $code) {
            return null;
        }

        $url = strtolower((string) $this->video_url);
        $kind = str_contains($url, '/reel') ? 'reel' : (str_contains($url, '/tv/') ? 'tv' : 'p');

        return "https://www.instagram.com/{$kind}/{$code}/embed/captioned/";
    }
}
