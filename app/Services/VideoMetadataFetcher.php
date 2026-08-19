<?php

namespace App\Services;

use App\Models\Video;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VideoMetadataFetcher
{
    /**
     * @return array{
     *     title?: string,
     *     description?: string,
     *     thumbnail_url?: string,
     *     cover_path?: string,
     *     published_at?: string,
     *     platform?: string|null
     * }|null
     */
    public function fetch(string $url): ?array
    {
        $url = trim($url);

        if ($url === '') {
            return null;
        }

        $probe = new Video(['video_url' => $url]);
        $platform = $probe->platform();

        return match ($platform) {
            'youtube' => $this->fetchYouTube($url, $probe->youtubeId()),
            'tiktok' => $this->fetchTikTok($url),
            default => $this->fetchOEmbedGeneric($url, $platform),
        };
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function fetchYouTube(string $url, ?string $id): ?array
    {
        $meta = [
            'platform' => 'youtube',
        ];

        if ($id) {
            $meta['thumbnail_url'] = 'https://i.ytimg.com/vi/'.$id.'/hqdefault.jpg';
        }

        $apiKey = config('services.youtube.key');

        if (filled($apiKey) && filled($id)) {
            $response = Http::timeout(12)
                ->acceptJson()
                ->get('https://www.googleapis.com/youtube/v3/videos', [
                    'part' => 'snippet',
                    'id' => $id,
                    'key' => $apiKey,
                ]);

            if ($response->successful()) {
                $snippet = data_get($response->json(), 'items.0.snippet');

                if (is_array($snippet)) {
                    if (filled($snippet['title'] ?? null)) {
                        $meta['title'] = (string) $snippet['title'];
                    }
                    if (filled($snippet['description'] ?? null)) {
                        $meta['description'] = Str::limit((string) $snippet['description'], 2000, '');
                    }
                    if (filled($snippet['publishedAt'] ?? null)) {
                        $meta['published_at'] = Carbon::parse($snippet['publishedAt'])
                            ->timezone(config('app.timezone'))
                            ->format('Y-m-d H:i:s');
                    }
                    $thumb = data_get($snippet, 'thumbnails.high.url')
                        ?? data_get($snippet, 'thumbnails.medium.url')
                        ?? data_get($snippet, 'thumbnails.default.url');
                    if (filled($thumb)) {
                        $meta['thumbnail_url'] = (string) $thumb;
                    }

                    return $meta;
                }
            }
        }

        $oembed = $this->oEmbed('https://www.youtube.com/oembed', $url);

        if ($oembed) {
            if (filled($oembed['title'] ?? null)) {
                $meta['title'] = (string) $oembed['title'];
            }
            if (filled($oembed['thumbnail_url'] ?? null)) {
                $meta['thumbnail_url'] = (string) $oembed['thumbnail_url'];
            }
        }

        return isset($meta['title']) || isset($meta['thumbnail_url']) ? $meta : null;
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function fetchTikTok(string $url): ?array
    {
        $oembed = $this->oEmbed('https://www.tiktok.com/oembed', $url);

        if (! $oembed) {
            return ['platform' => 'tiktok'];
        }

        $meta = [
            'platform' => 'tiktok',
        ];

        if (filled($oembed['title'] ?? null)) {
            $meta['title'] = (string) $oembed['title'];
        }

        if (filled($oembed['author_name'] ?? null) && blank($meta['title'] ?? null)) {
            $meta['title'] = 'Video TikTok — '.$oembed['author_name'];
        }

        if (filled($oembed['thumbnail_url'] ?? null)) {
            $meta['thumbnail_url'] = (string) $oembed['thumbnail_url'];
            $path = $this->storeRemoteImage((string) $oembed['thumbnail_url'], 'tiktok');
            if ($path) {
                $meta['cover_path'] = $path;
            }
        }

        return $meta;
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function fetchOEmbedGeneric(string $url, ?string $platform): ?array
    {
        // Instagram oEmbed butuh token Meta — biasanya gagal tanpa kredensial.
        $oembed = $this->oEmbed('https://www.instagram.com/api/v1/oembed', $url)
            ?? $this->oEmbed('https://api.instagram.com/oembed', $url);

        if (! $oembed) {
            return $platform ? ['platform' => $platform] : null;
        }

        $meta = ['platform' => $platform];

        if (filled($oembed['title'] ?? null)) {
            $meta['title'] = (string) $oembed['title'];
        }

        if (filled($oembed['thumbnail_url'] ?? null)) {
            $meta['thumbnail_url'] = (string) $oembed['thumbnail_url'];
            $path = $this->storeRemoteImage((string) $oembed['thumbnail_url'], 'instagram');
            if ($path) {
                $meta['cover_path'] = $path;
            }
        }

        return $meta;
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function oEmbed(string $endpoint, string $url): ?array
    {
        try {
            $response = Http::timeout(12)
                ->acceptJson()
                ->get($endpoint, ['url' => $url, 'format' => 'json']);

            if (! $response->successful()) {
                return null;
            }

            $json = $response->json();

            return is_array($json) ? $json : null;
        } catch (\Throwable) {
            return null;
        }
    }

    protected function storeRemoteImage(string $imageUrl, string $prefix): ?string
    {
        try {
            $response = Http::timeout(20)->get($imageUrl);

            if (! $response->successful()) {
                return null;
            }

            $mime = (string) $response->header('Content-Type');
            $ext = match (true) {
                str_contains($mime, 'png') => 'png',
                str_contains($mime, 'webp') => 'webp',
                str_contains($mime, 'gif') => 'gif',
                default => 'jpg',
            };

            $path = 'videos/'.$prefix.'-'.Str::uuid().'.'.$ext;
            Storage::disk('public')->put($path, $response->body());

            return $path;
        } catch (\Throwable) {
            return null;
        }
    }
}
