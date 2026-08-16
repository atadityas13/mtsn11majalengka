<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class SiteVisit extends Model
{
    protected $fillable = [
        'visited_on',
        'count',
        'page_views',
    ];

    protected function casts(): array
    {
        return [
            'visited_on' => 'date',
            'count' => 'integer',
            'page_views' => 'integer',
        ];
    }

    /**
     * Catat 1 tayangan halaman; pengunjung unik dihitung sekali per sesi.
     */
    public static function recordVisit(): void
    {
        $date = now()->toDateString();

        $row = static::query()->firstOrCreate(
            ['visited_on' => $date],
            ['count' => 0, 'page_views' => 0],
        );

        $row->increment('page_views');

        if (! Session::get('site_visit_counted')) {
            $row->increment('count');
            Session::put('site_visit_counted', true);
        }
    }

    public static function totalCount(): int
    {
        return (int) static::query()->sum('count');
    }

    /**
     * @return array{today_visitors: int, today_page_views: int, total_visitors: int, total_page_views: int}
     */
    public static function stats(): array
    {
        $today = static::query()->whereDate('visited_on', now()->toDateString())->first();

        return [
            'today_visitors' => (int) ($today?->count ?? 0),
            'today_page_views' => (int) ($today?->page_views ?? 0),
            'total_visitors' => (int) static::query()->sum('count'),
            'total_page_views' => (int) static::query()->sum('page_views'),
        ];
    }
}
