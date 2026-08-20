<?php

namespace App\Models;

use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = [
        'title',
        'body',
        'published_on',
        'is_pinned',
        'is_published',
        'push_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'published_on' => 'date',
            'is_pinned' => 'boolean',
            'is_published' => 'boolean',
            'push_sent_at' => 'datetime',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
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

        return $html;
    }
}
