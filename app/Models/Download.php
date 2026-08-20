<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Download extends Model
{
    protected $fillable = [
        'title',
        'description',
        'file_path',
        'category',
        'download_count',
        'is_published',
        'push_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'download_count' => 'integer',
            'is_published' => 'boolean',
            'push_sent_at' => 'datetime',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }
}
