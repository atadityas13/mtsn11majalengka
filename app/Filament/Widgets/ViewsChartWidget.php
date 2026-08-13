<?php

namespace App\Filament\Widgets;

use App\Models\PostViewDaily;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class ViewsChartWidget extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Tayangan berita 14 hari';

    protected ?string $description = 'Jumlah kunjungan halaman detail berita per hari';

    protected ?string $maxHeight = '280px';

    protected int | string | array $columnSpan = 'full';

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $start = now()->subDays(13)->startOfDay();

        $rows = PostViewDaily::query()
            ->where('date', '>=', $start->toDateString())
            ->get()
            ->mapWithKeys(fn (PostViewDaily $row) => [
                Carbon::parse($row->date)->toDateString() => (int) $row->views,
            ]);

        $labels = [];
        $data = [];

        for ($i = 0; $i < 14; $i++) {
            $day = $start->copy()->addDays($i);
            $labels[] = $day->translatedFormat('d M');
            $data[] = $rows[$day->toDateString()] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Tayangan',
                    'data' => $data,
                    'borderColor' => '#0a7a3e',
                    'backgroundColor' => 'rgba(10, 122, 62, 0.15)',
                    'fill' => true,
                    'tension' => 0.35,
                ],
            ],
            'labels' => $labels,
        ];
    }
}
