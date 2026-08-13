<?php

namespace App\Filament\Resources\Downloads;

use App\Filament\Concerns\RequiresSuperAdmin;
use App\Filament\Resources\Downloads\Pages\CreateDownload;
use App\Filament\Resources\Downloads\Pages\EditDownload;
use App\Filament\Resources\Downloads\Pages\ListDownloads;
use App\Filament\Resources\Downloads\Schemas\DownloadForm;
use App\Filament\Resources\Downloads\Tables\DownloadsTable;
use App\Models\Download;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class DownloadResource extends Resource
{
    use RequiresSuperAdmin;

    protected static ?string $model = Download::class;

    protected static ?string $navigationLabel = 'Unduhan';

    protected static ?string $modelLabel = 'Unduhan';

    protected static ?string $pluralModelLabel = 'Unduhan';

    protected static string|UnitEnum|null $navigationGroup = 'Dokumen';

    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowDownTray;

    public static function form(Schema $schema): Schema
    {
        return DownloadForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DownloadsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDownloads::route('/'),
            'create' => CreateDownload::route('/create'),
            'edit' => EditDownload::route('/{record}/edit'),
        ];
    }
}
