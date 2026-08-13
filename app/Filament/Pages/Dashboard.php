<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationLabel = 'Ringkasan';

    protected static ?int $navigationSort = -1;

    public function getTitle(): string
    {
        return 'Ringkasan Si COMA';
    }

    public function getHeading(): string
    {
        return 'Ringkasan Si COMA';
    }

    public function getSubheading(): ?string
    {
        return 'Site Content Management — MTsN 11 Majalengka';
    }

    /**
     * @return int | array<string, ?int>
     */
    public function getColumns(): int | array
    {
        return [
            'md' => 2,
            'xl' => 2,
        ];
    }
}
