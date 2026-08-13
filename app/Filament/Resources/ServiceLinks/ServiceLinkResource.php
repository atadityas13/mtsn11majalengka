<?php

namespace App\Filament\Resources\ServiceLinks;

use App\Filament\Concerns\RequiresSuperAdmin;
use App\Filament\Resources\ServiceLinks\Pages\CreateServiceLink;
use App\Filament\Resources\ServiceLinks\Pages\EditServiceLink;
use App\Filament\Resources\ServiceLinks\Pages\ListServiceLinks;
use App\Filament\Resources\ServiceLinks\Schemas\ServiceLinkForm;
use App\Filament\Resources\ServiceLinks\Tables\ServiceLinksTable;
use App\Models\ServiceLink;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ServiceLinkResource extends Resource
{
    use RequiresSuperAdmin;

    protected static ?string $model = ServiceLink::class;

    protected static ?string $navigationLabel = 'Akses Layanan';

    protected static ?string $modelLabel = 'Layanan';

    protected static ?string $pluralModelLabel = 'Akses Layanan';

    protected static string|UnitEnum|null $navigationGroup = 'Navigasi & Layanan';

    protected static ?int $navigationSort = 2;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    public static function form(Schema $schema): Schema
    {
        return ServiceLinkForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ServiceLinksTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListServiceLinks::route('/'),
            'create' => CreateServiceLink::route('/create'),
            'edit' => EditServiceLink::route('/{record}/edit'),
        ];
    }
}
