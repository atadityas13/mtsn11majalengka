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

    public function youtubeId(): ?string
    {
        $url = $this->video_url;

        if (preg_match('/(?:youtu\.be\/|v=|shorts\/|embed\/)([A-Za-z0-9_-]{6,})/', $url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    public function embedUrl(bool $autoplay = false, bool $mute = true): ?string
    {
        $id = $this->youtubeId();

        if (! $id) {
            return null;
        }

        $params = [
            'rel' => 0,
            'modestbranding' => 1,
            'playsinline' => 1,
            'controls' => 1,
        ];

        if ($autoplay) {
            $params['autoplay'] = 1;
        }

        if ($mute) {
            $params['mute'] = 1;
        }

        return 'https://www.youtube.com/embed/'.$id.'?'.http_build_query($params);
    }

    public function thumbnailUrl(): ?string
    {
        if ($this->cover_image) {
            return asset('storage/'.$this->cover_image);
        }

        $id = $this->youtubeId();

        return $id ? 'https://i.ytimg.com/vi/'.$id.'/hqdefault.jpg' : null;
    }
}
