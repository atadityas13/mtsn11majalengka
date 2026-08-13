<?php

namespace App\Filament\Resources\OrganizationNodes;

use App\Filament\Concerns\RequiresSuperAdmin;
use App\Filament\Resources\OrganizationNodes\Pages\CreateOrganizationNode;
use App\Filament\Resources\OrganizationNodes\Pages\EditOrganizationNode;
use App\Filament\Resources\OrganizationNodes\Pages\ListOrganizationNodes;
use App\Filament\Resources\OrganizationNodes\Schemas\OrganizationNodeForm;
use App\Filament\Resources\OrganizationNodes\Tables\OrganizationNodesTable;
use App\Models\OrganizationNode;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class OrganizationNodeResource extends Resource
{
    use RequiresSuperAdmin;

    protected static ?string $model = OrganizationNode::class;

    protected static ?string $navigationLabel = 'Struktur Organisasi';

    protected static ?string $modelLabel = 'Jabatan';

    protected static ?string $pluralModelLabel = 'Struktur Organisasi';

    protected static ?string $recordTitleAttribute = 'title';

    protected static string|UnitEnum|null $navigationGroup = 'Profil';

    protected static ?int $navigationSort = 3;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleGroup;

    public static function form(Schema $schema): Schema
    {
        return OrganizationNodeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OrganizationNodesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrganizationNodes::route('/'),
            'create' => CreateOrganizationNode::route('/create'),
            'edit' => EditOrganizationNode::route('/{record}/edit'),
        ];
    }
}
