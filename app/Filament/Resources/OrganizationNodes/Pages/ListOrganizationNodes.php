<?php

namespace App\Filament\Resources\OrganizationNodes\Pages;

use App\Filament\Resources\OrganizationNodes\OrganizationNodeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOrganizationNodes extends ListRecords
{
    protected static string $resource = OrganizationNodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
