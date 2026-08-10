<?php

namespace Tests\Unit;

use App\Models\Video;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class VideoEmbedTest extends TestCase
{
    #[DataProvider('platformProvider')]
    public function test_detects_platform_and_embed(string $url, string $platform, string $embedContains): void
    {
        $video = new Video(['video_url' => $url, 'type' => 'short']);

        $this->assertSame($platform, $video->platform());
        $this->assertNotNull($video->embedUrl());
        $this->assertStringContainsString($embedContains, (string) $video->embedUrl());
    }

    public static function platformProvider(): array
    {
        return [
            'youtube watch' => [
                'https://www.youtube.com/watch?v=aqz-KE-bpKQ',
                'youtube',
                'youtube.com/embed/aqz-KE-bpKQ',
            ],
            'youtube shorts' => [
                'https://www.youtube.com/shorts/aqz-KE-bpKQ',
                'youtube',
                'youtube.com/embed/aqz-KE-bpKQ',
            ],
            'tiktok' => [
                'https://www.tiktok.com/@scout2015/video/6718339390042524933',
                'tiktok',
                'tiktok.com/player/v1/6718339390042524933',
            ],
            'instagram reel' => [
                'https://www.instagram.com/reel/CxyzABC1234/',
                'instagram',
                'instagram.com/reel/CxyzABC1234/embed',
            ],
            'instagram post' => [
                'https://www.instagram.com/p/CxyzABC1234/',
                'instagram',
                'instagram.com/p/CxyzABC1234/embed',
            ],
        ];
    }

    public function test_tiktok_embed_respects_mute_flag(): void
    {
        $video = new Video([
            'video_url' => 'https://www.tiktok.com/@scout2015/video/6718339390042524933',
            'type' => 'short',
        ]);

        // muted=1 mengunci volume di player TikTok; mute dikontrol via postMessage.
        $muted = (string) $video->embedUrl(autoplay: true, mute: true);
        $sound = (string) $video->embedUrl(autoplay: true, mute: false);

        $this->assertStringContainsString('muted=0', $muted);
        $this->assertStringContainsString('muted=0', $sound);
        $this->assertStringContainsString('tiktok.com/player/v1/', $muted);
    }

    public function test_suggests_short_for_vertical_platforms(): void
    {
        $this->assertTrue((new Video(['video_url' => 'https://www.youtube.com/shorts/abc123']))->suggestsShort());
        $this->assertTrue((new Video(['video_url' => 'https://www.tiktok.com/@u/video/1']))->suggestsShort());
        $this->assertTrue((new Video(['video_url' => 'https://www.instagram.com/reel/abc/']))->suggestsShort());
        $this->assertFalse((new Video(['video_url' => 'https://www.youtube.com/watch?v=abc123']))->suggestsShort());
    }
}
