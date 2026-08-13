<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuItem extends Model
{
    protected $fillable = [
        'label',
        'url',
        'location',
        'parent_id',
        'sort_order',
        'open_in_new_tab',
        'is_visible',
    ];

    protected function casts(): array
    {
        return [
            'parent_id' => 'integer',
            'sort_order' => 'integer',
            'open_in_new_tab' => 'boolean',
            'is_visible' => 'boolean',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order')->orderBy('label');
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_visible', true);
    }

    public function scopeHeader(Builder $query): Builder
    {
        return $query->where('location', 'header');
    }

    public function scopeRoots(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    public function hasChildren(): bool
    {
        return $this->relationLoaded('children')
            ? $this->children->isNotEmpty()
            : $this->children()->visible()->exists();
    }

    public function resolvedUrl(): string
    {
        $url = trim((string) $this->url);

        if ($url === '' || $url === '#') {
            return '#';
        }

        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://') || str_starts_with($url, 'mailto:') || str_starts_with($url, 'tel:')) {
            return $url;
        }

        return url($url);
    }
}
