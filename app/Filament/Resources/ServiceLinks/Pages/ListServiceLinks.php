<?php

namespace App\Filament\Resources\ServiceLinks\Pages;

use App\Filament\Resources\ServiceLinks\ServiceLinkResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListServiceLinks extends ListRecords
{
    protected static string $resource = ServiceLinkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
