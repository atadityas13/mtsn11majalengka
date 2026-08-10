<?php

namespace App\Filament\Resources\OrganizationNodes\Pages;

use App\Filament\Resources\OrganizationNodes\OrganizationNodeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOrganizationNode extends EditRecord
{
    protected static string $resource = OrganizationNodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
