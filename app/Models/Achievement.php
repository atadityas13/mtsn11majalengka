<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    protected $fillable = [
        'title',
        'level',
        'winner_name',
        'achieved_on',
        'image',
        'description',
        'sort_order',
        'is_published',
        'push_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'achieved_on' => 'date',
            'sort_order' => 'integer',
            'is_published' => 'boolean',
            'push_sent_at' => 'datetime',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }
}
