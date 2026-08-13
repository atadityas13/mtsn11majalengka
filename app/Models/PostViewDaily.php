<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostViewDaily extends Model
{
    protected $fillable = [
        'date',
        'views',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'views' => 'integer',
        ];
    }

    public static function recordView(): void
    {
        $date = now()->toDateString();

        $row = static::query()->firstOrCreate(
            ['date' => $date],
            ['views' => 0],
        );

        $row->increment('views');
    }
}
