<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ServiceLink extends Model
{
    protected $fillable = [
        'label',
        'url',
        'description',
        'logo',
        'sort_order',
        'open_in_new_tab',
        'is_visible',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'open_in_new_tab' => 'boolean',
            'is_visible' => 'boolean',
        ];
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_visible', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('label');
    }

    public function resolvedUrl(): string
    {
        $url = trim((string) $this->url);

        if ($url === '') {
            return '#';
        }

        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://') || str_starts_with($url, 'mailto:') || str_starts_with($url, 'tel:')) {
            return $url;
        }

        return url($url);
    }

    public function isExternal(): bool
    {
        if ($this->open_in_new_tab) {
            return true;
        }

        $url = trim((string) $this->url);

        return str_starts_with($url, 'http://') || str_starts_with($url, 'https://');
    }
}
