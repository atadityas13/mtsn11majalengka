<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Post;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $urls = [];

        foreach ($this->staticRoutes() as $route => $meta) {
            $urls[] = [
                'loc' => route($route),
                'lastmod' => now()->toAtomString(),
                'changefreq' => $meta['changefreq'],
                'priority' => $meta['priority'],
            ];
        }

        Post::query()
            ->published()
            ->orderByDesc('published_at')
            ->get(['slug', 'published_at', 'updated_at'])
            ->each(function (Post $post) use (&$urls): void {
                $urls[] = [
                    'loc' => route('posts.show', $post->slug),
                    'lastmod' => $this->atom($post->updated_at ?? $post->published_at),
                    'changefreq' => 'weekly',
                    'priority' => '0.7',
                ];
            });

        Page::query()
            ->published()
            ->orderBy('title')
            ->get(['slug', 'updated_at'])
            ->each(function (Page $page) use (&$urls): void {
                $urls[] = [
                    'loc' => route('pages.show', $page->slug),
                    'lastmod' => $this->atom($page->updated_at),
                    'changefreq' => 'monthly',
                    'priority' => '0.6',
                ];
            });

        return response()
            ->view('sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    /**
     * @return array<string, array{changefreq: string, priority: string}>
     */
    protected function staticRoutes(): array
    {
        return [
            'home' => ['changefreq' => 'daily', 'priority' => '1.0'],
            'posts.index' => ['changefreq' => 'daily', 'priority' => '0.9'],
            'announcements.index' => ['changefreq' => 'weekly', 'priority' => '0.8'],
            'agendas.index' => ['changefreq' => 'weekly', 'priority' => '0.7'],
            'gallery.index' => ['changefreq' => 'weekly', 'priority' => '0.7'],
            'achievements.index' => ['changefreq' => 'weekly', 'priority' => '0.7'],
            'downloads.index' => ['changefreq' => 'weekly', 'priority' => '0.6'],
            'staff.index' => ['changefreq' => 'monthly', 'priority' => '0.6'],
            'organization.index' => ['changefreq' => 'monthly', 'priority' => '0.6'],
            'videos.index' => ['changefreq' => 'weekly', 'priority' => '0.7'],
            'shorts.index' => ['changefreq' => 'weekly', 'priority' => '0.7'],
            'layanan' => ['changefreq' => 'monthly', 'priority' => '0.7'],
            'contact' => ['changefreq' => 'monthly', 'priority' => '0.8'],
        ];
    }

    protected function atom(mixed $value): string
    {
        return Carbon::parse($value ?? now())->toAtomString();
    }
}
