<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Filament\Forms\Components\RichEditor\RichContentRenderer;

class Post extends Model
{
    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'excerpt',
        'body',
        'cover_image',
        'author_name',
        'editor_name',
        'tags',
        'published_at',
        'is_published',
        'views_count',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'is_published' => 'boolean',
            'views_count' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Post $post): void {
            if (blank($post->slug) && filled($post->title)) {
                $post->slug = Str::slug($post->title);
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function readingMinutes(): int
    {
        $text = trim(strip_tags((string) $this->body));
        $words = str_word_count($text);

        return max(1, (int) ceil($words / 200));
    }

    public function renderedBody(): string
    {
        $html = RichContentRenderer::make($this->body ?? '')
            ->fileAttachmentsDisk('public')
            ->fileAttachmentsVisibility('public')
            ->toHtml();

        // Konten lama tanpa tag HTML: jadikan paragraf.
        if (! preg_match('/<(p|div|h[1-6]|ul|ol|blockquote|figure|img)\b/i', $html)) {
            $html = collect(preg_split("/\n\s*\n/", trim(str_replace(["\r\n", "\r"], "\n", strip_tags($html)))))
                ->map(fn (string $chunk) => trim($chunk))
                ->filter()
                ->map(fn (string $chunk) => '<p>'.e($chunk).'</p>')
                ->implode('');
        }

        $latestOther = static::query()
            ->published()
            ->whereKeyNot($this->getKey())
            ->latest('published_at')
            ->first();

        if ($latestOther) {
            // Di akhir isi berita agar tidak memotong daftar/paragraf.
            $html .= view('components.baca-juga', ['post' => $latestOther])->render();
        }

        return $html;
    }

    public function bestRelatedPost(): ?self
    {
        $tags = collect($this->tagList())
            ->map(fn (string $tag) => Str::lower($tag))
            ->filter()
            ->values();

        $candidates = static::query()
            ->published()
            ->with('category')
            ->whereKeyNot($this->getKey())
            ->latest('published_at')
            ->take(60)
            ->get();

        if ($candidates->isEmpty()) {
            return null;
        }

        if ($tags->isEmpty()) {
            $pool = $this->category_id
                ? $candidates->where('category_id', $this->category_id)->values()
                : $candidates;

            if ($pool->isEmpty()) {
                $pool = $candidates;
            }

            return $pool->random();
        }

        $scored = $candidates->map(function (self $post) use ($tags): array {
            $postTags = collect($post->tagList())
                ->map(fn (string $tag) => Str::lower($tag));

            return [
                'post' => $post,
                'score' => $tags->intersect($postTags)->count(),
            ];
        });

        $maxScore = (int) $scored->max('score');

        if ($maxScore < 1) {
            $pool = $this->category_id
                ? $candidates->where('category_id', $this->category_id)->values()
                : $candidates;

            if ($pool->isEmpty()) {
                $pool = $candidates;
            }

            return $pool->random();
        }

        return $scored
            ->where('score', $maxScore)
            ->pluck('post')
            ->values()
            ->random();
    }

    /**
     * @return list<string>
     */
    public function tagList(): array
    {
        if (blank($this->tags)) {
            return $this->category?->name ? [$this->category->name] : [];
        }

        return collect(explode(',', (string) $this->tags))
            ->map(fn (string $tag) => trim($tag))
            ->filter()
            ->values()
            ->all();
    }

    /**
     * Daftar bulan berita published, 6 per halaman (arsip=0,1,2…).
     *
     * @return array{months: \Illuminate\Support\Collection<int, object>, page: int, has_prev: bool, has_next: bool}
     */
    public static function archiveMonths(int $page = 0, int $perPage = 6): array
    {
        $page = max(0, $page);
        $driver = static::query()->getConnection()->getDriverName();

        $yearExpr = $driver === 'sqlite'
            ? "cast(strftime('%Y', published_at) as integer)"
            : 'YEAR(published_at)';
        $monthExpr = $driver === 'sqlite'
            ? "cast(strftime('%m', published_at) as integer)"
            : 'MONTH(published_at)';

        $all = static::published()
            ->selectRaw("{$yearExpr} as year, {$monthExpr} as month, COUNT(*) as posts_count")
            ->groupByRaw("{$yearExpr}, {$monthExpr}")
            ->orderByRaw("{$yearExpr} DESC, {$monthExpr} DESC")
            ->get();

        $total = $all->count();
        $months = $all->slice($page * $perPage, $perPage)->values();

        return [
            'months' => $months,
            'page' => $page,
            'has_prev' => $page > 0,
            'has_next' => ($page + 1) * $perPage < $total,
        ];
    }
}
