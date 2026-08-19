<?php

namespace Tests\Unit;

use App\Services\VideoMetadataFetcher;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class VideoMetadataFetcherTest extends TestCase
{
    public function test_fetches_youtube_via_oembed_without_api_key(): void
    {
        config(['services.youtube.key' => null]);

        Http::fake([
            'www.youtube.com/oembed*' => Http::response([
                'title' => 'Contoh Short',
                'thumbnail_url' => 'https://i.ytimg.com/vi/aqz-KE-bpKQ/hqdefault.jpg',
            ]),
        ]);

        $meta = app(VideoMetadataFetcher::class)->fetch('https://www.youtube.com/shorts/aqz-KE-bpKQ');

        $this->assertNotNull($meta);
        $this->assertSame('youtube', $meta['platform']);
        $this->assertSame('Contoh Short', $meta['title']);
        $this->assertStringContainsString('aqz-KE-bpKQ', (string) $meta['thumbnail_url']);
        $this->assertArrayNotHasKey('description', $meta);
    }

    public function test_fetches_youtube_description_with_api_key(): void
    {
        config(['services.youtube.key' => 'test-key', 'app.timezone' => 'Asia/Jakarta']);

        Http::fake([
            'www.googleapis.com/youtube/v3/videos*' => Http::response([
                'items' => [[
                    'snippet' => [
                        'title' => 'Judul API',
                        'description' => 'Deskripsi panjang dari API',
                        'publishedAt' => '2024-06-01T10:00:00Z',
                        'thumbnails' => [
                            'high' => ['url' => 'https://i.ytimg.com/vi/aqz-KE-bpKQ/hqdefault.jpg'],
                        ],
                    ],
                ]],
            ]),
        ]);

        $meta = app(VideoMetadataFetcher::class)->fetch('https://www.youtube.com/watch?v=aqz-KE-bpKQ');

        $this->assertSame('Judul API', $meta['title']);
        $this->assertSame('Deskripsi panjang dari API', $meta['description']);
        $this->assertArrayHasKey('published_at', $meta);
    }

    public function test_fetches_tiktok_and_stores_cover(): void
    {
        Storage::fake('public');

        Http::fake([
            'www.tiktok.com/oembed*' => Http::response([
                'title' => 'Video TikTok contoh',
                'author_name' => 'mtsn11',
                'thumbnail_url' => 'https://example.com/thumb.jpg',
            ]),
            'example.com/thumb.jpg' => Http::response('fake-image-bytes', 200, [
                'Content-Type' => 'image/jpeg',
            ]),
        ]);

        $meta = app(VideoMetadataFetcher::class)->fetch(
            'https://www.tiktok.com/@mtsn11/video/6718339390042524933'
        );

        $this->assertSame('tiktok', $meta['platform']);
        $this->assertSame('Video TikTok contoh', $meta['title']);
        $this->assertNotEmpty($meta['cover_path'] ?? null);
        Storage::disk('public')->assertExists($meta['cover_path']);
    }
}
