<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class SiteVisit extends Model
{
    protected $fillable = [
        'visited_on',
        'count',
    ];

    protected function casts(): array
    {
        return [
            'visited_on' => 'date',
            'count' => 'integer',
        ];
    }

    public static function recordVisit(): void
    {
        if (Session::get('site_visit_counted')) {
            return;
        }

        $date = now()->toDateString();

        $row = static::query()->firstOrCreate(
            ['visited_on' => $date],
            ['count' => 0],
        );

        $row->increment('count');
        Session::put('site_visit_counted', true);
    }

    public static function totalCount(): int
    {
        return (int) static::query()->sum('count');
    }
}
