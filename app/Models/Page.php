<?php

namespace App\Models;

use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Page extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'subtitle',
        'body',
        'hero_image',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Page $page): void {
            if (blank($page->slug) && filled($page->title)) {
                $page->slug = Str::slug($page->title);
            }
        });
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function renderedBody(): string
    {
        $html = RichContentRenderer::make($this->body ?? '')
            ->fileAttachmentsDisk('public')
            ->fileAttachmentsDirectory('pages/body')
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

        return $html;
    }
}
